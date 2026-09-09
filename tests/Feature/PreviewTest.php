<?php

namespace Tests\Feature;

use App\Models\Magang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_logbook_preview_renders_all_weeks_including_empty(): void
    {
        $magang = $this->duaMingguTanpaData();

        $this->get(route('preview.logbook', $magang))
            ->assertOk()
            ->assertSee('Minggu I')
            ->assertSee('Minggu II');
    }

    public function test_presensi_preview_renders_all_weeks_including_empty(): void
    {
        $magang = $this->duaMingguTanpaData();

        $this->get(route('preview.presensi', $magang))
            ->assertOk()
            ->assertSee('Minggu Ke')
            ->assertSee('Hari Tanggal')
            ->assertSee('TTD')
            ->assertSee('Sen 05/01')
            ->assertSee('12/01');
    }

    private function duaMingguTanpaData(): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-18',
        ]);
    }
}
