<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Task;
use App\Models\TimeSchedule;
use App\Services\TaskProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function magang(string $mulai = '2026-01-05', string $selesai = '2026-03-06'): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
        ]);
    }

    private function linkedSchedule(Magang $magang, array $attributes = []): array
    {
        $task = Task::factory()->create([
            'magang_id' => $magang->id,
            'nama_kegiatan' => $attributes['nama_kegiatan'] ?? $attributes['kegiatan'] ?? 'Kegiatan Terjadwal',
            'tanggal_mulai' => $attributes['tanggal_mulai'] ?? $magang->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $attributes['tanggal_selesai'] ?? $magang->tanggal_selesai->format('Y-m-d'),
        ]);

        $schedule = TimeSchedule::factory()->create(array_merge([
            'magang_id' => $magang->id,
            'task_id' => $task->id,
        ], $attributes));

        return [$schedule, $task];
    }

    public function test_store_creates_schedule_and_linked_task_without_duplication(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Membuat Tampilan UI Dashboard',
            'deskripsi' => 'Desain halaman utama',
            'target' => 'Selesai minggu ini',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
            'prioritas' => 'tinggi',
            'status' => 'berjalan',
            'catatan' => 'Prioritas utama',
        ])->assertRedirect(route('magang.time-schedule.show', [$magang, TimeSchedule::first()]));

        $schedule = TimeSchedule::where('magang_id', $magang->id)->first();

        $this->assertNotNull($schedule);
        $this->assertSame(1, Task::where('magang_id', $magang->id)->count());
        $this->assertSame('Membuat Tampilan UI Dashboard', $schedule->task->nama_kegiatan);
        $this->assertSame(0, $schedule->task->bobot);
        $this->assertSame('belum_dimulai', $schedule->task->status);
        $this->assertSame(1, $schedule->minggu_ke);
        $this->assertSame('berjalan', $schedule->status);
    }

    public function test_multi_day_range_is_single_record(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Kegiatan berhari-hari',
            'tanggal_mulai' => '2026-01-10',
            'tanggal_selesai' => '2026-01-12',
            'prioritas' => 'sedang',
            'status' => 'belum_dimulai',
        ])->assertRedirect();

        $this->assertSame(1, TimeSchedule::where('magang_id', $magang->id)->count());
        $this->assertSame(1, Task::where('magang_id', $magang->id)->count());
    }

    public function test_store_validates_selesai_before_mulai(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Salah urutan',
            'tanggal_mulai' => '2026-01-10',
            'tanggal_selesai' => '2026-01-05',
            'prioritas' => 'sedang',
            'status' => 'belum_dimulai',
        ])->assertSessionHasErrors('tanggal_selesai');

        $this->assertDatabaseCount('time_schedules', 0);
    }

    public function test_store_validates_dates_within_magang_period(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Di luar periode',
            'tanggal_mulai' => '2026-04-01',
            'tanggal_selesai' => '2026-04-05',
            'prioritas' => 'sedang',
            'status' => 'belum_dimulai',
        ])->assertSessionHasErrors('tanggal_mulai');

        $this->assertDatabaseCount('time_schedules', 0);
    }

    public function test_store_validates_prioritas_and_status(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Nilai salah',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
            'prioritas' => 'urgent',
            'status' => 'draft',
        ])->assertSessionHasErrors(['prioritas', 'status']);
    }

    public function test_minggu_ke_derived_from_tanggal_mulai(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Minggu ketiga',
            'tanggal_mulai' => '2026-01-19',
            'tanggal_selesai' => '2026-01-25',
            'prioritas' => 'sedang',
            'status' => 'belum_dimulai',
        ])->assertRedirect();

        $this->assertSame(3, TimeSchedule::where('magang_id', $magang->id)->value('minggu_ke'));
    }

    public function test_available_returns_only_schedules_active_on_date(): void
    {
        $magang = $this->magang();
        [$scheduleA, $taskA] = $this->linkedSchedule($magang, [
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
            'target' => 'Target A',
        ]);
        [$scheduleB, $taskB] = $this->linkedSchedule($magang, [
            'tanggal_mulai' => '2026-01-20',
            'tanggal_selesai' => '2026-01-25',
        ]);

        $data = $this->getJson(route('magang.time-schedule.available', $magang).'?tanggal=2026-01-07')
            ->assertOk()
            ->json();

        $ids = collect($data)->pluck('task_id')->all();

        $this->assertContains($taskA->id, $ids);
        $this->assertNotContains($taskB->id, $ids);
        $this->assertSame('Target A', collect($data)->firstWhere('task_id', $taskA->id)['target']);
    }

    public function test_available_requires_valid_date(): void
    {
        $magang = $this->magang();

        $this->get(route('magang.time-schedule.available', $magang))
            ->assertSessionHasErrors('tanggal');
    }

    public function test_logbook_create_lists_only_tasks_active_on_default_date(): void
    {
        $magang = $this->magang();
        [$scheduleA, $taskA] = $this->linkedSchedule($magang, [
            'kegiatan' => 'Kegiatan Minggu 1',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
        ]);
        [$scheduleB, $taskB] = $this->linkedSchedule($magang, [
            'kegiatan' => 'Kegiatan Minggu 3',
            'tanggal_mulai' => '2026-01-19',
            'tanggal_selesai' => '2026-01-25',
        ]);

        $this->get(route('magang.logbook.create', $magang))
            ->assertOk()
            ->assertSee('Kegiatan Minggu 1')
            ->assertDontSee('Kegiatan Minggu 3');
    }

    public function test_logbook_store_rejects_task_inactive_on_tanggal(): void
    {
        $magang = $this->magang();
        [$schedule, $task] = $this->linkedSchedule($magang, [
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
        ]);

        $this->post(route('magang.logbook.store', $magang), [
            'task_id' => $task->id,
            'tanggal' => '2026-01-15',
            'deskripsi_tugas' => 'Di luar jadwal',
            'persentase_capaian' => '80',
        ])->assertSessionHasErrors('task_id');

        $this->assertDatabaseCount('logbooks', 0);
    }

    public function test_logbook_store_accepts_legacy_task_without_schedule(): void
    {
        $magang = $this->magang();
        $task = Task::factory()->create(['magang_id' => $magang->id]);

        $this->post(route('magang.logbook.store', $magang), [
            'task_id' => $task->id,
            'tanggal' => '2026-01-15',
            'deskripsi_tugas' => 'Kegiatan lama',
            'persentase_capaian' => '80',
        ])->assertRedirect(route('magang.logbook.index', $magang));

        $this->assertDatabaseCount('logbooks', 1);
    }

    public function test_overall_progress_unaffected_by_schedule_without_logbook(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.time-schedule.store', $magang), [
            'kegiatan' => 'Belum dikerjakan',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
            'prioritas' => 'sedang',
            'status' => 'belum_dimulai',
        ])->assertRedirect();

        $service = app(TaskProgressService::class);

        $this->assertEquals(0.0, $service->overallProgress($magang));
    }

    public function test_update_syncs_linked_task_and_recomputes_minggu(): void
    {
        $magang = $this->magang();
        [$schedule, $task] = $this->linkedSchedule($magang, [
            'kegiatan' => 'Kegiatan Lama',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-10',
        ]);

        $this->put(route('magang.time-schedule.update', [$magang, $schedule]), [
            'kegiatan' => 'Kegiatan Baru',
            'deskripsi' => 'Revisi lingkup',
            'target' => 'Target baru',
            'tanggal_mulai' => '2026-01-19',
            'tanggal_selesai' => '2026-01-25',
            'prioritas' => 'rendah',
            'status' => 'ditunda',
            'catatan' => 'Ditunda ke pekan depan',
        ])->assertRedirect(route('magang.time-schedule.show', [$magang, $schedule]));

        $fresh = $schedule->fresh();

        $this->assertSame('Kegiatan Baru', $fresh->kegiatan);
        $this->assertSame(3, $fresh->minggu_ke);
        $this->assertSame('ditunda', $fresh->status);
        $this->assertSame('Kegiatan Baru', $task->fresh()->nama_kegiatan);
        $this->assertSame('2026-01-19', $task->fresh()->tanggal_mulai->toDateString());
    }

    public function test_destroy_deletes_task_when_no_logbook_exists(): void
    {
        $magang = $this->magang();
        [$schedule, $task] = $this->linkedSchedule($magang);

        $this->delete(route('magang.time-schedule.destroy', [$magang, $schedule]))
            ->assertRedirect(route('magang.time-schedule.index', $magang));

        $this->assertNull(TimeSchedule::find($schedule->id));
        $this->assertNull(Task::find($task->id));
    }

    public function test_destroy_keeps_task_when_logbook_exists(): void
    {
        $magang = $this->magang();
        [$schedule, $task] = $this->linkedSchedule($magang);

        Logbook::factory()->create([
            'magang_id' => $magang->id,
            'task_id' => $task->id,
            'tanggal' => '2026-01-07',
        ]);

        $this->delete(route('magang.time-schedule.destroy', [$magang, $schedule]))
            ->assertRedirect(route('magang.time-schedule.index', $magang));

        $this->assertNull(TimeSchedule::find($schedule->id));
        $this->assertNotNull(Task::find($task->id));
        $this->assertSame($task->id, Logbook::where('magang_id', $magang->id)->first()->task_id);
    }

    public function test_show_displays_schedule_detail(): void
    {
        $magang = $this->magang();
        [$schedule, $task] = $this->linkedSchedule($magang, ['kegiatan' => 'Kegiatan Detail']);

        $this->get(route('magang.time-schedule.show', [$magang, $schedule]))
            ->assertOk()
            ->assertSee('Kegiatan Detail')
            ->assertSee('Riwayat Logbook');
    }

    public function test_index_renders_schedule_list(): void
    {
        $magang = $this->magang();
        [$schedule, $task] = $this->linkedSchedule($magang, ['kegiatan' => 'Kegiatan Index']);

        $this->get(route('magang.time-schedule.index', $magang))
            ->assertOk()
            ->assertSee('Kegiatan Index')
            ->assertSee('Time Schedule');
    }

    public function test_create_form_renders(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');

        $this->get(route('magang.time-schedule.create', $magang))
            ->assertOk()
            ->assertSee('Tambah Time Schedule')
            ->assertSee('Minggu')
            ->assertSee('Prioritas');
    }

    public function test_dashboard_widget_shows_only_upcoming_schedules(): void
    {
        $magang = $this->magang();
        [$upcoming, $taskUpcoming] = $this->linkedSchedule($magang, [
            'kegiatan' => 'Kegiatan yang Akan Datang',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
        ]);
        [$past, $taskPast] = $this->linkedSchedule($magang, [
            'kegiatan' => 'Kegiatan yang Sudah Lewat',
            'tanggal_mulai' => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->subDays(8)->toDateString(),
        ]);

        $html = $this->get(route('dashboard', ['magang' => $magang->id]))
            ->assertOk()
            ->assertSee('Time Schedule Terdekat')
            ->getContent();

        preg_match('/Time Schedule Terdekat(.*?)Magang Aktif/s', $html, $matches);

        $this->assertNotFalse($matches);
        $this->assertStringContainsString('Kegiatan yang Akan Datang', $matches[1]);
        $this->assertStringNotContainsString('Kegiatan yang Sudah Lewat', $matches[1]);
    }
}
