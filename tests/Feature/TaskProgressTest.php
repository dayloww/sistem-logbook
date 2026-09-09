<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Task;
use App\Models\WeeklyProgress;
use App\Services\TaskProgressService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskProgressTest extends TestCase
{
    use RefreshDatabase;

    private function magang(string $mulai = '2026-01-05', string $selesai = '2026-03-06'): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
        ]);
    }

    private function task(Magang $magang, array $attributes = []): Task
    {
        return Task::factory()->create(array_merge(['magang_id' => $magang->id], $attributes));
    }

    private function logbook(Magang $magang, Task $task, string $tanggal, int $progress): Logbook
    {
        return Logbook::factory()->create([
            'magang_id' => $magang->id,
            'task_id' => $task->id,
            'tanggal' => $tanggal,
            'persentase_capaian' => $progress,
        ]);
    }

    private function service(): TaskProgressService
    {
        return app(TaskProgressService::class);
    }

    public function test_store_creates_task_and_inits_progress_from_logbook(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.task.store', $magang), [
            'nama_kegiatan' => 'Membuat Tampilan UI Dashboard',
            'deskripsi' => 'Desain halaman utama',
            'bobot' => '20',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-20',
        ])->assertRedirect(route('magang.task.index', $magang));

        $task = Task::where('magang_id', $magang->id)->first();

        $this->assertNotNull($task);
        $this->assertSame(20, $task->bobot);
        $this->assertSame(0, $task->progress);
        $this->assertSame('belum_dimulai', $task->status);
    }

    public function test_same_task_progress_takes_highest_not_sum(): void
    {
        $magang = $this->magang();
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);

        $this->logbook($magang, $task, '2026-01-06', 50);
        $this->logbook($magang, $task, '2026-01-07', 20);

        $this->service()->recalculateTask($task->fresh());

        $this->assertSame(50, $task->fresh()->progress);
        $this->assertSame('berjalan', $task->fresh()->status);

        $this->logbook($magang, $task, '2026-01-08', 100);
        $this->service()->recalculateTask($task->fresh());

        $this->assertSame(100, $task->fresh()->progress);
        $this->assertSame('selesai', $task->fresh()->status);
    }

    public function test_overall_progress_is_weighted_by_bobot(): void
    {
        $magang = $this->magang();

        $dashboard = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 20]);
        $login = $this->task($magang, ['nama_kegiatan' => 'Login', 'bobot' => 15]);

        $this->logbook($magang, $dashboard, '2026-01-06', 100);
        $this->logbook($magang, $login, '2026-01-07', 70);

        $this->service()->recalculateAll($magang);

        // (20/100)*100 + (15/100)*70 = 20 + 10.5 = 30.5
        $this->assertEqualsWithDelta(30.5, $this->service()->overallProgress($magang), 0.05);
    }

    public function test_logbook_store_warns_when_progress_input_is_below_task_progress(): void
    {
        $magang = $this->magang();
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);

        $this->logbook($magang, $task, '2026-01-06', 80);
        $this->service()->recalculateTask($task->fresh());

        $this->post(route('magang.logbook.store', $magang), [
            'task_id' => $task->id,
            'tanggal' => '2026-01-07',
            'deskripsi_tugas' => 'Lanjutan',
            'persentase_capaian' => '60',
        ])->assertRedirect(route('magang.logbook.index', $magang))
            ->assertSessionHas('warning');

        $this->assertSame(80, $task->fresh()->progress);
    }

    public function test_editing_max_logbook_cannot_regress_task_progress(): void
    {
        $magang = $this->magang();
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);

        $highest = $this->logbook($magang, $task, '2026-01-06', 80);
        $this->logbook($magang, $task, '2026-01-07', 60);
        $this->service()->recalculateTask($task->fresh());

        $this->put(route('logbook.update', $highest), [
            'task_id' => $task->id,
            'tanggal' => '2026-01-06',
            'deskripsi_tugas' => 'Ubah ke 60',
            'persentase_capaian' => '60',
        ])->assertSessionHasErrors('persentase_capaian');

        $this->assertSame(80, $task->fresh()->progress);
    }

    public function test_weekly_progress_is_snapshot_as_of_week_end(): void
    {
        $magang = $this->magang('2026-01-05', '2026-01-18');
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);

        $this->logbook($magang, $task, '2026-01-06', 50);   // minggu 1
        $this->logbook($magang, $task, '2026-01-13', 100);  // minggu 2

        $this->service()->syncWeeklyProgress($magang, null, Carbon::parse('2026-01-18'));

        $week1 = WeeklyProgress::where('magang_id', $magang->id)->where('minggu_ke', 1)->first();
        $week2 = WeeklyProgress::where('magang_id', $magang->id)->where('minggu_ke', 2)->first();

        $this->assertEquals(50.0, (float) $week1->progress_akhir);
        $this->assertEquals(50.0, (float) $week1->perkembangan);
        $this->assertEquals(100.0, (float) $week2->progress_akhir);
        $this->assertEquals(50.0, (float) $week2->perkembangan);
    }

    public function test_current_week_uses_cutoff_today(): void
    {
        $magang = $this->magang('2026-01-05', '2026-02-15');
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);

        $this->logbook($magang, $task, '2026-01-06', 40);
        $this->logbook($magang, $task, '2026-01-08', 80);

        $overview = $this->service()->overview($magang, Carbon::parse('2026-01-08'));

        $this->assertSame(1, $overview['current_week']);
        $this->assertEquals(80.0, $overview['current_week_progress']);
        $this->assertEquals(80.0, $overview['perkembangan_minggu_ini']);
    }

    public function test_bobot_total_cannot_exceed_100(): void
    {
        $magang = $this->magang();
        $this->task($magang, ['nama_kegiatan' => 'A', 'bobot' => 60]);

        $this->post(route('magang.task.store', $magang), [
            'nama_kegiatan' => 'B',
            'bobot' => '50',
        ])->assertSessionHasErrors('bobot');
    }

    public function test_task_tanggal_must_be_within_magang_period(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');

        $this->post(route('magang.task.store', $magang), [
            'nama_kegiatan' => 'Di luar periode',
            'bobot' => '10',
            'tanggal_mulai' => '2026-04-01',
        ])->assertSessionHasErrors('tanggal_mulai');
    }

    public function test_progress_page_renders_overview_and_weekly_table(): void
    {
        $magang = $this->magang('2026-01-05', '2026-02-15');
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);
        $this->logbook($magang, $task, '2026-01-06', 50);

        $this->get(route('progress', ['magang' => $magang->id]))
            ->assertOk()
            ->assertSee('Progress Keseluruhan')
            ->assertSee('Progress Mingguan')
            ->assertSee('UI Dashboard')
            ->assertSee('Daftar Kegiatan');
    }

    public function test_dashboard_shows_task_progress_section(): void
    {
        $magang = $this->magang('2026-01-05', '2026-02-15');
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);
        $this->logbook($magang, $task, '2026-01-06', 50);

        $this->get(route('dashboard', ['magang' => $magang->id]))
            ->assertOk()
            ->assertSee('Progress Magang')
            ->assertSee('Lihat Semua Kegiatan')
            ->assertSee('UI Dashboard');
    }

    public function test_task_show_displays_logbook_history(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 20]);
        $this->logbook($magang, $task, '2026-01-06', 50);
        $this->logbook($magang, $task, '2026-01-07', 100);
        $this->service()->recalculateTask($task->fresh());

        $this->get(route('magang.task.show', [$magang, $task]))
            ->assertOk()
            ->assertSee('Riwayat Progress Harian')
            ->assertSee('Selesai');
    }

    public function test_deleting_highest_logbook_recalculates_task_progress(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $task = $this->task($magang, ['nama_kegiatan' => 'UI Dashboard', 'bobot' => 100]);

        $highest = $this->logbook($magang, $task, '2026-01-06', 90);
        $this->logbook($magang, $task, '2026-01-07', 40);

        $this->service()->recalculateTask($task->fresh());
        $this->assertSame(90, $task->fresh()->progress);

        $this->delete(route('logbook.destroy', $highest))
            ->assertRedirect(route('magang.logbook.index', $magang));

        $this->assertSame(40, $task->fresh()->progress);
    }
}