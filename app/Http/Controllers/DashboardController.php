<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Services\LogbookProgressService;
use App\Support\RomanNumeral;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'mahasiswa' => Mahasiswa::count(),
            'magang' => Magang::count(),
            'presensi' => Presensi::count(),
            'logbook' => Logbook::count(),
        ];

        $recentPresensi = Presensi::with('magang.mahasiswa')
            ->latest()
            ->take(5)
            ->get();

        $recentLogbook = Logbook::with('magang.mahasiswa')
            ->latest()
            ->take(5)
            ->get();

        $magangAktif = Magang::with('mahasiswa')
            ->where('tanggal_mulai', '<=', today())
            ->where('tanggal_selesai', '>=', today())
            ->get();

        $magangList = Magang::with('mahasiswa')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $selected = $magangList->firstWhere('id', $request->integer('magang'))
            ?? $magangList->first(fn ($m) => $m->tanggal_mulai->lte(today()) && $m->tanggal_selesai->gte(today()))
            ?? $magangList->first();

        $progress = $selected ? app(LogbookProgressService::class)->for($selected) : null;

        $trend = $this->trendSeries($selected, $progress);

        $displayStatus = $progress
            ? ($progress['percent'] >= 100
                ? ['key' => 'selesai', 'label' => 'Selesai']
                : $progress['status'])
            : ['key' => 'none', 'label' => 'Belum ada data'];

        return view('dashboard.index', compact(
            'stats',
            'recentPresensi',
            'recentLogbook',
            'magangAktif',
            'magangList',
            'selected',
            'progress',
            'trend',
            'displayStatus',
        ));
    }

    /**
     * Tren kumulatif berbasis tanggal aktivitas logbook yang benar-benar ada.
     * Tidak membuat titik data buatan untuk minggu/tanggal yang kosong.
     */
    protected function trendSeries(?Magang $magang, ?array $progress): array
    {
        if (! $magang || ! $progress) {
            return [];
        }

        $targetDays = max($progress['target_days'], 1);
        $cumulative = 0;

        return Logbook::where('magang_id', $magang->id)
            ->orderBy('tanggal')
            ->get(['tanggal'])
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->startOfDay())
            ->unique(fn (Carbon $date) => $date->toDateString())
            ->values()
            ->map(function (Carbon $date) use (&$cumulative, $targetDays, $magang) {
                $cumulative++;

                return [
                    'tanggal' => $date->format('Y-m-d'),
                    'label' => $date->translatedFormat('D, d/m'),
                    'minggu' => 'Minggu '.RomanNumeral::convert($magang->mingguKe($date)),
                    'persen' => min(100, (int) round($cumulative / $targetDays * 100)),
                    'aktivitas' => $cumulative,
                    'full' => $date->translatedFormat('l, d/m/Y'),
                ];
            })
            ->all();
    }
}
