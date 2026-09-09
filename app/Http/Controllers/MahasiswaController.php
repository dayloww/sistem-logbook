<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    private function handleTtdUpload(Mahasiswa $mahasiswa, Request $request): void
    {
        if ($request->boolean('hapus_ttd') || ($request->hasFile('ttd') && $mahasiswa->ttd_path)) {
            Storage::disk('public')->delete($mahasiswa->ttd_path);
            $mahasiswa->update(['ttd_path' => null]);
        }

        if ($request->hasFile('ttd')) {
            $ttd = $request->file('ttd');
            $path = $ttd->storeAs('ttd', Str::uuid().'.'.$ttd->extension(), 'public');

            $mahasiswa->update(['ttd_path' => $path]);
        }
    }

    public function index()
    {
        $mahasiswa = Mahasiswa::withCount('magang')->latest()->paginate(10);

        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:20', 'unique:mahasiswa,nim'],
            'nama' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'perguruan_tinggi' => ['required', 'string', 'max:255'],
            'ttd' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $mahasiswa = Mahasiswa::create($validated);

        $this->handleTtdUpload($mahasiswa, $request);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $magang = $mahasiswa->magang()->withCount(['presensi', 'logbooks'])->latest()->get();

        return view('mahasiswa.show', compact('mahasiswa', 'magang'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:20', 'unique:mahasiswa,nim,'.$mahasiswa->id],
            'nama' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'perguruan_tinggi' => ['required', 'string', 'max:255'],
            'ttd' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $mahasiswa->update($validated);

        $this->handleTtdUpload($mahasiswa, $request);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
