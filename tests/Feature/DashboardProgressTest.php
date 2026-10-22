<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\Magang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardProgressTest extends TestCase
{
    use RefreshDatabase;

    private function magang(string $mulai, string $selesai): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
        ]);
    }

    private function targetDays(Magang $magang): int
    {
        return $magang->tanggal_mulai->diffInDays($magang->tanggal_selesai) + 1;
    }

    private function addLogbook(Magang $magang, string $tanggal): Logbook
    {
        return Logbook::factory()->create(['magang_id' => $magang->id, 'tanggal' => $tanggal]);
    }

    public function test_dashboard_shows_empty_state_when_no_magang_exists(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada data magang');
    }

    public function test_no_logbook_shows_zero_progress_and_empty_state(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $target = $this->targetDays($magang);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('0 dari '.$target.' hari')
            ->assertSee('Belum ada aktivitas logbook');
    }

    public function test_one_logbook_on_one_date_counts_as_one_progress(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $this->addLogbook($magang, '2026-01-06');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 dari '.$this->targetDays($magang).' hari');
    }

    public function test_two_logbooks_on_same_date_count_as_one_progress(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');

        for ($i = 0; $i < 2; $i++) {
            $this->addLogbook($magang, '2026-01-06');
        }

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 dari '.$this->targetDays($magang).' hari')
            ->assertSee('Total Aktivitas');
    }

    public function test_logbooks_on_multiple_dates_count_each_date(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $this->addLogbook($magang, '2026-01-06');
        $this->addLogbook($magang, '2026-01-07');
        $this->addLogbook($magang, '2026-01-08');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('3 dari '.$this->targetDays($magang).' hari');
    }

    public function test_chart_series_is_cumulative_across_weeks(): void
    {
        $magang = $this->magang('2026-01-05', '2026-02-15');

        $this->addLogbook($magang, '2026-01-06');
        $this->addLogbook($magang, '2026-01-07');
        $this->addLogbook($magang, '2026-01-12');
        $this->addLogbook($magang, '2026-01-13');
        $this->addLogbook($magang, '2026-01-19');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('5 dari '.$this->targetDays($magang).' hari')
            ->assertSee('"Minggu I"', false)
            ->assertSee('"Minggu II"', false)
            ->assertSee('"Minggu III"', false)
            ->assertSee('"persen":5', false)
            ->assertSee('"persen":10', false)
            ->assertSee('"persen":12', false);
    }

    public function test_deleting_one_of_two_logbooks_on_same_date_keeps_progress(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $first = $this->addLogbook($magang, '2026-01-06');
        $this->addLogbook($magang, '2026-01-06');

        $first->delete();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 dari '.$this->targetDays($magang).' hari');
    }

    public function test_deleting_all_logbooks_on_a_date_reduces_progress(): void
    {
        $magang = $this->magang('2026-01-05', '2026-03-06');
        $this->addLogbook($magang, '2026-01-06');
        $this->addLogbook($magang, '2026-01-07');

        Logbook::where('magang_id', $magang->id)->whereDate('tanggal', '2026-01-06')->delete();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 dari '.$this->targetDays($magang).' hari');
    }

    public function test_target_uses_magang_period_days(): void
    {
        $magang = $this->magang('2026-01-01', '2026-02-19');
        $this->addLogbook($magang, '2026-01-02');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dari 50 hari');
    }

    public function test_progress_reaches_100_percent_when_every_day_is_filled(): void
    {
        $magang = $this->magang('2026-01-05', '2026-01-07');
        $this->addLogbook($magang, '2026-01-05');
        $this->addLogbook($magang, '2026-01-06');
        $this->addLogbook($magang, '2026-01-07');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('100%')
            ->assertSee('3 dari 3 hari');
    }

    public function test_progress_is_capped_at_100_but_keeps_actual_total(): void
    {
        $magang = $this->magang('2026-01-05', '2026-01-07');

        foreach (['2026-01-05', '2026-01-06', '2026-01-07', '2026-01-08', '2026-01-09', '2026-01-10'] as $date) {
            $this->addLogbook($magang, $date);
        }

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('100%')
            ->assertSee('6 dari 3 hari')
            ->assertSee('0')
            ->assertSee('Sisa Aktivitas');
    }

    public function test_progress_of_one_mahasiswa_does_not_leak_into_another(): void
    {
        $magangA = $this->magang('2026-01-05', '2026-03-06');
        $magangB = $this->magang('2026-01-05', '2026-03-06');

        $this->addLogbook($magangA, '2026-01-06');

        $this->get(route('dashboard', ['magang' => $magangA->id]))
            ->assertOk()
            ->assertSee('1 dari '.$this->targetDays($magangA).' hari');

        $this->get(route('dashboard', ['magang' => $magangB->id]))
            ->assertOk()
            ->assertSee('0 dari '.$this->targetDays($magangB).' hari')
            ->assertSee('Belum ada aktivitas logbook');
    }

    public function test_progress_widget_has_responsive_layout_markup(): void
    {
        $this->magang('2026-01-05', '2026-02-15');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('analytics-layout')
            ->assertSee('dashboard-bottom');
    }
}
