<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\Magang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogbookControllerTest extends TestCase
{
    use RefreshDatabase;

    private function magang(): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-03-06',
        ]);
    }

    public function test_store_creates_logbook(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.logbook.store', $magang), [
            'tanggal' => '2026-01-06',
            'deskripsi_tugas' => 'Menyusun laporan kegiatan',
            'target' => 'Selesai minggu ini',
            'persentase_capaian' => '80',
            'kendala' => null,
        ])->assertRedirect(route('magang.logbook.index', $magang));

        $this->assertDatabaseCount('logbooks', 1);
        $this->assertTrue(Logbook::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-06')
            ->where('deskripsi_tugas', 'Menyusun laporan kegiatan')
            ->exists());
    }

    public function test_store_persists_ttd_confirmation_flag(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.logbook.store', $magang), [
            'tanggal' => '2026-01-06',
            'deskripsi_tugas' => 'Menyusun laporan kegiatan',
            'persentase_capaian' => '80',
            'ttd_confirmed' => '1',
        ])->assertRedirect(route('magang.logbook.index', $magang));

        $this->assertTrue(Logbook::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-06')
            ->value('ttd_confirmed'));

        $this->post(route('magang.logbook.store', $magang), [
            'tanggal' => '2026-01-07',
            'deskripsi_tugas' => 'Revisi laporan',
            'persentase_capaian' => '60',
        ])->assertRedirect(route('magang.logbook.index', $magang));

        $this->assertFalse(Logbook::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-07')
            ->value('ttd_confirmed'));
    }

    public function test_update_flips_ttd_confirmation_flag(): void
    {
        $magang = $this->magang();
        $logbook = Logbook::factory()->create([
            'magang_id' => $magang->id,
            'tanggal' => '2026-01-06',
            'ttd_confirmed' => false,
        ]);

        $this->put(route('logbook.update', $logbook), [
            'tanggal' => '2026-01-06',
            'deskripsi_tugas' => 'Menyusun laporan kegiatan',
            'persentase_capaian' => '90',
            'ttd_confirmed' => '1',
        ])->assertRedirect(route('magang.logbook.index', $magang));

        $this->assertTrue($logbook->fresh()->ttd_confirmed);
    }

    public function test_store_validates_tanggal_within_magang_period(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.logbook.store', $magang), [
            'tanggal' => '2026-04-10',
            'deskripsi_tugas' => 'Di luar periode',
            'persentase_capaian' => '80',
        ])->assertSessionHasErrors('tanggal');

        $this->assertDatabaseCount('logbooks', 0);
    }
}
