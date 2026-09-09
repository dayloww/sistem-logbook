<?php

namespace App\Http\Controllers;

use App\Models\Magang;

class PreviewController extends Controller
{
    public function logbook(Magang $magang)
    {
        $magang->load([
            'mahasiswa',
            'logbooks' => fn ($q) => $q->orderBy('tanggal'),
            'catatanMingguan' => fn ($q) => $q->orderBy('minggu_ke'),
            'kopSurat',
        ]);

        $jumlahMinggu = $magang->jumlahMinggu();
        $logbooksByWeek = collect(range(1, $jumlahMinggu))
            ->map(fn ($week) => [
                'minggu_ke' => $week,
                'tanggal_mulai' => $magang->tanggal_mulai->copy()->addDays(($week - 1) * 7),
                'tanggal_selesai' => $magang->tanggal_mulai->copy()->addDays($week * 7 - 1),
                'items' => $magang->logbooks->filter(fn ($l) => $l->mingguKe() === $week)->values(),
                'catatan' => $magang->catatanMingguan->firstWhere('minggu_ke', $week),
            ]);

        return view('preview.logbook', compact('magang', 'jumlahMinggu', 'logbooksByWeek'));
    }

    public function presensi(Magang $magang)
    {
        $magang->load([
            'mahasiswa',
            'presensi' => fn ($q) => $q->orderBy('tanggal'),
            'kopSurat',
        ]);

        $jumlahMinggu = $magang->jumlahMinggu();
        $presensiByWeek = collect(range(1, $jumlahMinggu))
            ->map(fn ($week) => [
                'minggu_ke' => $week,
                'items' => $magang->presensi->filter(fn ($p) => $p->mingguKe() === $week)->values(),
            ]);

        return view('preview.presensi', compact('magang', 'jumlahMinggu', 'presensiByWeek'));
    }
}
