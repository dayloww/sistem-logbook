<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Services\TaskProgressService;
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

        $progress = $selected ? app(TaskProgressService::class)->overview($selected) : null;

        return view('progress.index', compact('magangList', 'selected', 'progress'));
    }
}