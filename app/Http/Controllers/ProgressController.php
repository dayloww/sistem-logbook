<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Services\LogbookProgressService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $magangList = Magang::with('mahasiswa')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $selected = $magangList->firstWhere('id', $request->integer('magang'))
            ?? $magangList->first();

        $progress = $selected ? app(LogbookProgressService::class)->for($selected) : null;
        $recentActivities = $selected
            ? $selected->logbooks()->latest('tanggal')->take(6)->get()
            : collect();

        return view('progress.index', compact('magangList', 'selected', 'progress', 'recentActivities'));
    }
}
