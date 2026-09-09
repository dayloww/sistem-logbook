<?php

namespace App\Http\Controllers;

use App\Models\CatatanMingguan;
use App\Models\Magang;
use Illuminate\Http\Request;

class CatatanMingguanController extends Controller
{
    public function index(Magang $magang)
    {
        $jumlahMinggu = $magang->jumlahMinggu();
        $catatan = $magang->catatanMingguan()->orderBy('minggu_ke')->get();

        return view('catatan-mingguan.index', compact('magang', 'jumlahMinggu', 'catatan'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $request->validate([
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:'.$magang->jumlahMinggu()],
            'catatan' => ['required', 'string'],
        ]);

        $validated['magang_id'] = $magang->id;

        CatatanMingguan::updateOrCreate(
            ['magang_id' => $magang->id, 'minggu_ke' => $validated['minggu_ke']],
            $validated
        );

        return redirect()->route('magang.catatan.index', $magang)->with('success', 'Catatan mingguan berhasil disimpan.');
    }

    public function update(Request $request, CatatanMingguan $catatanMingguan)
    {
        $validated = $request->validate([
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:'.$catatanMingguan->magang->jumlahMinggu()],
            'catatan' => ['required', 'string'],
        ]);

        $catatanMingguan->update($validated);

        return redirect()->route('magang.catatan.index', $catatanMingguan->magang_id)->with('success', 'Catatan mingguan berhasil diperbarui.');
    }

    public function destroy(CatatanMingguan $catatanMingguan)
    {
        $magangId = $catatanMingguan->magang_id;
        $catatanMingguan->delete();

        return redirect()->route('magang.catatan.index', $magangId)->with('success', 'Catatan mingguan berhasil dihapus.');
    }
}
