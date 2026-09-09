<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Magang $magang)
    {
        $presensi = $magang->presensi()->orderBy('tanggal', 'desc')->get();
        $jumlahMinggu = $magang->jumlahMinggu();

        $presensiByWeek = collect(range(1, $jumlahMinggu))->map(fn ($week) => [
            'minggu_ke' => $week,
            'items' => $presensi->filter(fn ($p) => $p->mingguKe() === $week)->values(),
        ])->filter(fn ($week) => $week['items']->isNotEmpty());

        return view('presensi.index', compact('magang', 'presensiByWeek', 'jumlahMinggu', 'presensi'));
    }

    public function create(Magang $magang)
    {
        $lastDate = $magang->presensi()->max('tanggal');
        $defaultDate = $lastDate ? Carbon::parse($lastDate)->addDay() : $magang->tanggal_mulai;

        return view('presensi.create', compact('magang', 'defaultDate'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'ttd_confirmed' => ['nullable', 'in:0,1'],
        ]);

        $validated['magang_id'] = $magang->id;
        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        $presensi = Presensi::where('magang_id', $magang->id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        if ($presensi) {
            $presensi->update($validated);
        } else {
            Presensi::create($validated);
        }

        return redirect()->route('magang.presensi.index', $magang)->with('success', 'Presensi berhasil disimpan.');
    }

    public function edit(Presensi $presensi)
    {
        $magang = $presensi->magang;

        return view('presensi.edit', compact('presensi', 'magang'));
    }

    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'ttd_confirmed' => ['nullable', 'in:0,1'],
        ]);

        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        $presensi->update($validated);

        return redirect()->route('magang.presensi.index', $presensi->magang_id)->with('success', 'Presensi berhasil diperbarui.');
    }

    public function destroy(Presensi $presensi)
    {
        $magangId = $presensi->magang_id;
        $presensi->delete();

        return redirect()->route('magang.presensi.index', $magangId)->with('success', 'Presensi berhasil dihapus.');
    }
}
