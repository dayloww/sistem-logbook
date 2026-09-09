<?php

namespace App\Services;

use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Task;
use App\Models\WeeklyProgress;
use App\Support\RomanNumeral;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TaskProgressService
{
    /**
     * Ringkasan progress magang berbasis task/kegiatan.
     *
     * Progress task diambil dari progress TERTINGGI seluruh logbook task tersebut
     * (progress tidak bisa turun). Progress keseluruhan dihitung dari bobot task:
     * kontribusi_task = (bobot / 100) * progress_task.
     */
    public function overview(Magang $magang, ?CarbonInterface $today = null): array
    {
        $today = $today ? Carbon::instance($today) : Carbon::today();

        $tasks = $magang->tasks()->with('logbooks')->orderBy('nama_kegiatan')->get();

        if ($tasks->isEmpty()) {
            $tasks = collect();
        }

        $this->syncTaskStatuses($tasks);

        $overall = $this->overallProgress($magang, $tasks);

        $this->syncWeeklyProgress($magang, $tasks, $today);

        $weekly = $magang->weeklyProgresses()->orderBy('minggu_ke')->get();

        $currentWeek = $this->currentWeek($magang, $today);
        $current = $weekly->firstWhere('minggu_ke', $currentWeek);
        $previous = $weekly->firstWhere('minggu_ke', $currentWeek - 1);

        $currentProgress = $current ? (float) $current->progress_akhir : (float) $overall;
        $previousProgress = $previous ? (float) $previous->progress_akhir : 0.0;

        return [
            'tasks' => $tasks,
            'total_tasks' => $tasks->count(),
            'selesai' => $tasks->where('status', 'selesai')->count(),
            'berjalan' => $tasks->where('status', 'berjalan')->count(),
            'belum' => $tasks->where('status', 'belum_dimulai')->count(),
            'overall' => round($overall, 1),
            'total_bobot' => round((float) $tasks->sum('bobot'), 1),
            'bobot_penuh' => abs((float) $tasks->sum('bobot') - 100) < 0.0001,
            'current_week' => $currentWeek,
            'jumlah_minggu' => $magang->jumlahMinggu(),
            'current_week_progress' => round($currentProgress, 1),
            'perkembangan_minggu_ini' => round($currentProgress - $previousProgress, 1),
            'weekly' => $weekly,
            'week_rows' => $this->weekRows($magang, $weekly, $currentWeek),
        ];
    }

    /**
     * Progress keseluruhan berbobot dari daftar task.
     */
    public function overallProgress(Magang $magang, ?Collection $tasks = null): float
    {
        $tasks = $tasks ?? $magang->tasks()->get();

        return round((float) $tasks->sum(fn (Task $task) => ($task->bobot / 100) * $task->progress), 1);
    }

    /**
     * Update progress & status sebuah task berdasarkan logbook tertinggi.
     */
    public function recalculateTask(Task $task): void
    {
        $highest = (int) ($task->logbooks()->max('persentase_capaian') ?? 0);

        $task->progress = min(100, max(0, $highest));
        $task->status = $this->statusFor($task->progress);
        $task->save();
    }

    /**
     * Rekalkulasi seluruh task dalam satu magang.
     */
    public function recalculateAll(Magang $magang): void
    {
        $magang->tasks()->get()->each(fn (Task $task) => $this->recalculateTask($task));
    }

    /**
     * Sinkronisasi snapshot progress mingguan (upsert) setelah ada perubahan logbook/task.
     */
    public function syncAfterLogbookChange(Magang $magang): void
    {
        $today = Carbon::today();
        $tasks = $magang->tasks()->with('logbooks')->get();

        $this->recalculateAll($magang);
        $this->syncWeeklyProgress($magang, $tasks, $today);
    }

    /**
     * Simpan (upsert) snapshot progress per minggu hingga minggu berjalan.
     *
     * Progress akhir minggu W adalah posisi progress keseluruhan pada akhir minggu W
     * (dihitung dari progress task yang tercatat sampai akhir minggu W).
     */
    public function syncWeeklyProgress(Magang $magang, ?Collection $tasks = null, ?CarbonInterface $today = null): void
    {
        $tasks = $tasks ?? $magang->tasks()->with('logbooks')->get();
        $tasks = $tasks ?: collect();
        $today = $today ? Carbon::instance($today) : Carbon::today();

        $weeks = max($magang->jumlahMinggu(), 1);
        $upTo = min($this->currentWeek($magang, $today), $weeks);

        $lastOverall = 0.0;

        foreach (range(1, $upTo) as $week) {
            $weekStart = $magang->tanggal_mulai->copy()->startOfDay()->addDays(($week - 1) * 7);
            $weekEnd = $weekStart->copy()->addDays(6);
            $cutoff = $today->lt($weekEnd) ? $today->copy() : $weekEnd->copy();

            $overall = $this->overallAsOf($tasks, $cutoff);

            WeeklyProgress::updateOrCreate(
                ['magang_id' => $magang->id, 'minggu_ke' => $week],
                [
                    'tanggal_mulai' => $weekStart->toDateString(),
                    'tanggal_selesai' => $weekEnd->toDateString(),
                    'progress_awal' => round($lastOverall, 1),
                    'progress_akhir' => round($overall, 1),
                    'perkembangan' => round($overall - $lastOverall, 1),
                ]
            );

            $lastOverall = $overall;
        }
    }

    protected function overallAsOf(Collection $tasks, Carbon $cutoff): float
    {
        return round((float) $tasks->sum(function (Task $task) use ($cutoff) {
            $progress = (int) ($task->logbooks
                ->filter(fn (Logbook $logbook) => $logbook->tanggal->lte($cutoff))
                ->max('persentase_capaian') ?? 0);

            return ($task->bobot / 100) * $progress;
        }), 1);
    }

    protected function currentWeek(Magang $magang, CarbonInterface $today): int
    {
        if ($today->lt($magang->tanggal_mulai->startOfDay())) {
            return 1;
        }

        if ($today->gt($magang->tanggal_selesai->startOfDay())) {
            return $magang->jumlahMinggu();
        }

        return max(1, min($magang->mingguKe($today), $magang->jumlahMinggu()));
    }

    protected function weekRows(Magang $magang, Collection $weekly, int $currentWeek): array
    {
        $weeks = max($magang->jumlahMinggu(), 1);
        $rows = [];

        foreach (range(1, $weeks) as $week) {
            $weekStart = $magang->tanggal_mulai->copy()->addDays(($week - 1) * 7);
            $snapshot = $weekly->firstWhere('minggu_ke', $week);

            $rows[] = [
                'minggu_ke' => $week,
                'minggu' => 'Minggu '.RomanNumeral::convert($week),
                'tanggal_mulai' => $weekStart->format('d M'),
                'tanggal_selesai' => $weekStart->copy()->addDays(6)->format('d M Y'),
                'progress' => $snapshot ? (float) $snapshot->progress_akhir : null,
                'perkembangan' => $snapshot ? (float) $snapshot->perkembangan : null,
                'is_current' => $week === $currentWeek,
            ];
        }

        return $rows;
    }

    protected function syncTaskStatuses(Collection $tasks): void
    {
        foreach ($tasks as $task) {
            $expected = $this->statusFor($task->progress);

            if ($task->status !== $expected) {
                $task->status = $expected;
                $task->save();
            }
        }
    }

    protected function statusFor(int $progress): string
    {
        if ($progress >= 100) {
            return 'selesai';
        }

        if ($progress > 0) {
            return 'berjalan';
        }

        return 'belum_dimulai';
    }
}