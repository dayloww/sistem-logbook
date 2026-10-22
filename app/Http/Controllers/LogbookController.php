<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Magang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index(Magang $magang)
    {
        $logbooks = $magang->logbooks()->orderBy('tanggal', 'desc')->get();
        $jumlahMinggu = $magang->jumlahMinggu();

        $logbooksByWeek = collect(range(1, $jumlahMinggu))->map(fn ($week) => [
            'minggu_ke' => $week,
            'items' => $logbooks->filter(fn ($l) => $l->mingguKe() === $week)->values(),
        ])->filter(fn ($week) => $week['items']->isNotEmpty());

        return view('logbook.index', compact('magang', 'logbooksByWeek', 'jumlahMinggu', 'logbooks'));
    }

    public function create(Magang $magang)
    {
        $lastDate = $magang->logbooks()->max('tanggal');
        $defaultDate = $lastDate ? Carbon::parse($lastDate)->addDay() : $magang->tanggal_mulai;

        return view('logbook.create', compact('magang', 'defaultDate'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $this->validated($request, $magang);

        $validated['magang_id'] = $magang->id;
        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        Logbook::create($validated);

        return redirect()->route('magang.logbook.index', $magang)->with('success', 'Logbook berhasil disimpan.');
    }

    public function edit(Logbook $logbook)
    {
        $magang = $logbook->magang;

        return view('logbook.edit', compact('logbook', 'magang'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        $validated = $this->validated($request, $logbook->magang);

        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        $logbook->update($validated);

        return redirect()->route('magang.logbook.index', $logbook->magang_id)->with('success', 'Logbook berhasil diperbarui.');
    }

    public function destroy(Logbook $logbook)
    {
        $magangId = $logbook->magang_id;
        $logbook->delete();

        return redirect()->route('magang.logbook.index', $magangId)->with('success', 'Logbook berhasil dihapus.');
    }

    protected function validated(Request $request, Magang $magang): array
    {
        return $request->validate([
            'tanggal' => [
                'required', 'date',
                'after_or_equal:'.$magang->tanggal_mulai->format('Y-m-d'),
                'before_or_equal:'.$magang->tanggal_selesai->format('Y-m-d'),
            ],
            'deskripsi_tugas' => ['required', 'string'],
            'target' => ['nullable', 'string'],
            'persentase_capaian' => ['required', 'integer', 'min:0', 'max:100'],
            'kendala' => ['nullable', 'string'],
            'ttd_confirmed' => ['nullable', 'in:0,1'],
        ]);
    }
}
