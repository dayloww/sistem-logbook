<?php

namespace App\Http\Controllers;

use App\Models\KopSurat;
use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Services\TaskProgressService;
use Illuminate\Http\Request;

class MagangController extends Controller
{
    public function index()
    {
        $magang = Magang::with('mahasiswa')->latest()->paginate(10);

        return view('magang.index', compact('magang'));
    }

    public function create()
    {
        $magang = null;
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        $kopSurats = KopSurat::all();

        return view('magang.create', compact('magang', 'mahasiswa', 'kopSurats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'judul_topik' => ['required', 'string', 'max:255'],
            'instansi' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'bagian_unit' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'lama_kegiatan' => ['nullable', 'string', 'max:100'],
            'total_jam' => ['nullable', 'integer', 'min:1'],
            'jumlah_sks' => ['nullable', 'integer', 'min:1'],
            'dosen_pembimbing_1' => ['required', 'string', 'max:255'],
            'dosen_pembimbing_2' => ['nullable', 'string', 'max:255'],
            'supervisor' => ['required', 'string', 'max:255'],
            'jabatan_supervisor' => ['nullable', 'string', 'max:255'],
            'kop_surats_id' => ['nullable', 'exists:kop_surats,id'],
        ]);

        Magang::create($validated);

        return redirect()->route('magang.index')->with('success', 'Data magang berhasil ditambahkan.');
    }

    public function show(Magang $magang)
    {
        $magang->load([
            'mahasiswa',
            'presensi' => fn ($q) => $q->orderBy('tanggal'),
            'logbooks' => fn ($q) => $q->orderBy('tanggal'),
            'catatanMingguan' => fn ($q) => $q->orderBy('minggu_ke'),
            'tasks' => fn ($q) => $q->orderBy('nama_kegiatan'),
        ]);
        $presensiStats = [
            'total' => $magang->presensi->count(),
            'jam' => $magang->presensi->filter(fn ($p) => $p->jam_masuk && $p->jam_pulang)->count(),
        ];

        $logbooksStats = [
            'total' => $magang->logbooks->count(),
            'rata_capaian' => round($magang->logbooks->avg('persentase_capaian') ?? 0),
        ];

        $jumlahMinggu = $magang->jumlahMinggu();

        $taskProgress = app(TaskProgressService::class)->overview($magang);

        return view('magang.show', compact('magang', 'presensiStats', 'logbooksStats', 'jumlahMinggu', 'taskProgress'));
    }

    public function edit(Magang $magang)
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        $kopSurats = KopSurat::all();

        return view('magang.edit', compact('magang', 'mahasiswa', 'kopSurats'));
    }

    public function update(Request $request, Magang $magang)
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'judul_topik' => ['required', 'string', 'max:255'],
            'instansi' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'bagian_unit' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'lama_kegiatan' => ['nullable', 'string', 'max:100'],
            'total_jam' => ['nullable', 'integer', 'min:1'],
            'jumlah_sks' => ['nullable', 'integer', 'min:1'],
            'dosen_pembimbing_1' => ['required', 'string', 'max:255'],
            'dosen_pembimbing_2' => ['nullable', 'string', 'max:255'],
            'supervisor' => ['required', 'string', 'max:255'],
            'jabatan_supervisor' => ['nullable', 'string', 'max:255'],
            'kop_surats_id' => ['nullable', 'exists:kop_surats,id'],
        ]);

        $magang->update($validated);

        return redirect()->route('magang.show', $magang)->with('success', 'Data magang berhasil diperbarui.');
    }

    public function destroy(Magang $magang)
    {
        $magang->delete();

        return redirect()->route('magang.index')->with('success', 'Data magang berhasil dihapus.');
    }
}