<?php

namespace App\Http\Controllers;

use App\Models\KopSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KopSuratController extends Controller
{
    public function index()
    {
        $kopSurats = KopSurat::latest()->get();

        return view('kop-surat.index', compact('kopSurats'));
    }

    public function create()
    {
        $kopSurat = null;

        return view('kop-surat.create', compact('kopSurat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:kampus,instansi'],
            'nama_instansi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        KopSurat::create($validated);

        return redirect()->route('kop-surat.index')->with('success', 'Kop surat berhasil ditambahkan.');
    }

    public function edit(KopSurat $kopSurat)
    {
        return view('kop-surat.edit', compact('kopSurat'));
    }

    public function update(Request $request, KopSurat $kopSurat)
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:kampus,instansi'],
            'nama_instansi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($kopSurat->logo_path) {
                Storage::disk('public')->delete($kopSurat->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $kopSurat->update($validated);

        return redirect()->route('kop-surat.index')->with('success', 'Kop surat berhasil diperbarui.');
    }

    public function destroy(KopSurat $kopSurat)
    {
        if ($kopSurat->logo_path) {
            Storage::disk('public')->delete($kopSurat->logo_path);
        }

        $kopSurat->delete();

        return redirect()->route('kop-surat.index')->with('success', 'Kop surat berhasil dihapus.');
    }
}