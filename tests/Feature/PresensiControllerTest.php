<?php

namespace Tests\Feature;

use App\Models\Magang;
use App\Models\Presensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiControllerTest extends TestCase
{
    use RefreshDatabase;

    private function magang(): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-03-06',
        ]);
    }

    public function test_store_creates_presensi(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.presensi.store', $magang), [
            'tanggal' => '2026-01-06',
            'jam_masuk' => '08:00',
            'jam_pulang' => '17:00',
        ])->assertRedirect(route('magang.presensi.index', $magang));

        $this->assertDatabaseCount('presensi', 1);
        $this->assertTrue(Presensi::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-06')
            ->where('jam_masuk', '08:00')
            ->where('jam_pulang', '17:00')
            ->exists());
    }

    public function test_store_updates_existing_record_for_same_date(): void
    {
        $magang = $this->magang();
        Presensi::factory()->create([
            'magang_id' => $magang->id,
            'tanggal' => '2026-01-06',
            'jam_masuk' => '09:00',
            'jam_pulang' => '16:00',
        ]);

        $this->post(route('magang.presensi.store', $magang), [
            'tanggal' => '2026-01-06',
            'jam_masuk' => '09:00',
            'jam_pulang' => '17:00',
        ])->assertRedirect(route('magang.presensi.index', $magang));

        $this->assertSame(1, Presensi::where('magang_id', $magang->id)->count());
        $this->assertTrue(Presensi::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-06')
            ->where('jam_pulang', '17:00')
            ->exists());
    }

    public function test_store_accepts_date_outside_magang_range(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.presensi.store', $magang), [
            'tanggal' => '2026-05-01',
            'jam_masuk' => '08:00',
        ])->assertRedirect(route('magang.presensi.index', $magang));

        $this->assertDatabaseHas('presensi', ['magang_id' => $magang->id]);
        $this->assertTrue(Presensi::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-05-01')
            ->exists());
    }

    public function test_store_persists_ttd_confirmation_flag(): void
    {
        $magang = $this->magang();

        $this->post(route('magang.presensi.store', $magang), [
            'tanggal' => '2026-01-06',
            'jam_masuk' => '08:00',
            'jam_pulang' => '17:00',
            'ttd_confirmed' => '1',
        ])->assertRedirect(route('magang.presensi.index', $magang));

        $this->assertTrue(Presensi::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-06')
            ->value('ttd_confirmed'));

        $this->post(route('magang.presensi.store', $magang), [
            'tanggal' => '2026-01-07',
            'jam_masuk' => '08:00',
        ])->assertRedirect(route('magang.presensi.index', $magang));

        $this->assertFalse(Presensi::where('magang_id', $magang->id)
            ->whereDate('tanggal', '2026-01-07')
            ->value('ttd_confirmed'));
    }

    public function test_update_flips_ttd_confirmation_flag(): void
    {
        $magang = $this->magang();
        $presensi = Presensi::factory()->create([
            'magang_id' => $magang->id,
            'tanggal' => '2026-01-06',
            'ttd_confirmed' => false,
        ]);

        $this->put(route('presensi.update', $presensi), [
            'tanggal' => '2026-01-06',
            'jam_masuk' => '08:00',
            'jam_pulang' => '17:00',
            'ttd_confirmed' => '1',
        ])->assertRedirect(route('magang.presensi.index', $magang));

        $this->assertTrue($presensi->fresh()->ttd_confirmed);
    }
}
