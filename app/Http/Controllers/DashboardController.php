<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Services\LogbookProgressService;
use App\Services\TaskProgressService;
use Illuminate\Http\Request;

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

        $taskProgress = $selected ? app(TaskProgressService::class)->overview($selected) : null;

        return view('dashboard.index', compact(
            'stats',
            'recentPresensi',
            'recentLogbook',
            'magangAktif',
            'magangList',
            'selected',
            'progress',
            'taskProgress'
        ));
    }
}
