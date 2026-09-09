<?php

namespace App\Services;

use App\Models\Logbook;
use App\Models\Magang;
use App\Support\RomanNumeral;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class LogbookProgressService
{
    /**
     * Hitung progres logbook untuk satu magang.
     * Satu tanggal unik yang memiliki logbook = 1 aktivitas / 1 progres.
     */
    public function for(Magang $magang, ?CarbonInterface $today = null): array
    {
        $today = $today ? Carbon::instance($today) : Carbon::today();

        $dates = Logbook::where('magang_id', $magang->id)
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->startOfDay())
            ->unique()
            ->values();

        $targetDays = $this->targetDays($magang);
        $completedDays = $dates->count();
        $percent = $targetDays > 0 ? min(100, (int) round($completedDays / $targetDays * 100)) : 0;

        return [
            'target_days' => $targetDays,
            'completed_days' => $completedDays,
            'remaining_days' => max($targetDays - $completedDays, 0),
            'percent' => $percent,
            'status' => $this->status($magang, $completedDays, $targetDays, $today),
            'series' => $this->series($magang, $dates, $targetDays),
        ];
    }

    protected function targetDays(Magang $magang): int
    {
        return max($magang->tanggal_mulai->diffInDays($magang->tanggal_selesai) + 1, 1);
    }

    protected function status(Magang $magang, int $completedDays, int $targetDays, Carbon $today): array
    {
        $mulai = $magang->tanggal_mulai->startOfDay();

        if ($today->lt($mulai)) {
            return ['key' => 'on_track', 'label' => 'On Track'];
        }

        $elapsedDays = min($targetDays, max($today->diffInDays($mulai) + 1, 0));
        $expected = $targetDays > 0 ? $elapsedDays / $targetDays : 1;
        $actual = min($completedDays / max($targetDays, 1), 1);

        if ($actual >= $expected) {
            return ['key' => 'on_track', 'label' => 'On Track'];
        }

        if ($actual >= $expected * 0.7) {
            return ['key' => 'perlu_perhatian', 'label' => 'Perlu Perhatian'];
        }

        return ['key' => 'tertinggal', 'label' => 'Tertinggal'];
    }

    protected function series(Magang $magang, Collection $dates, int $targetDays): array
    {
        $weeks = $magang->jumlahMinggu();
        $counts = array_fill(1, max($weeks, 1), 0);

        foreach ($dates as $date) {
            $week = $magang->mingguKe($date);
            $week = max(1, min($week, $weeks));
            $counts[$week]++;
        }

        $series = [];
        $cumulative = 0;

        foreach (range(1, max($weeks, 1)) as $week) {
            $cumulative += $counts[$week];

            $series[] = [
                'minggu' => 'Minggu '.RomanNumeral::convert($week),
                'week' => $week,
                'persen' => $targetDays > 0 ? min(100, (int) round($cumulative / $targetDays * 100)) : 0,
                'aktivitas' => $cumulative,
            ];
        }

        return $series;
    }
}
