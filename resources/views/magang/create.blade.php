@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">{{ $magang ? 'Edit Data Magang' : 'Tambah Data Magang' }}</h1>
    <p class="text-sm text-gray-500">Data magang digunakan pada seluruh dokumen keluaran</p>
</div>

<div class="max-w-3xl bg-white rounded-xl border border-gray-200 p-6">
    <form action="{{ $magang ? route('magang.update', $magang) : route('magang.store') }}" method="POST">
        @csrf
        @if ($magang) @method('PUT') @endif

        <div class="space-y-5">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">A. Data Mahasiswa</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mahasiswa <span class="text-red-500">*</span></label>
                    <select name="mahasiswa_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($mahasiswa as $m)
                            <option value="{{ $m->id }}" {{ old('mahasiswa_id', $magang?->mahasiswa_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->nama }} ({{ $m->nim }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">B. Informasi Kegiatan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul/Topik Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" name="judul_topik" value="{{ old('judul_topik', $magang?->judul_topik) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Pengembangan Sistem Informasi Kearsipan">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instansi <span class="text-red-500">*</span></label>
                            <input type="text" name="instansi" value="{{ old('instansi', $magang?->instansi) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama instansi/lokasi magang">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bagian/Unit Kerja</label>
                            <input type="text" name="bagian_unit" value="{{ old('bagian_unit', $magang?->bagian_unit) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Bidang TI">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $magang?->lokasi) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Kota/kabupaten lokasi magang">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $magang?->tanggal_mulai?->format('Y-m-d')) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $magang?->tanggal_selesai?->format('Y-m-d')) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lama Kegiatan</label>
                            <input type="text" name="lama_kegiatan" value="{{ old('lama_kegiatan', $magang?->lama_kegiatan) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 6 Bulan">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Jam</label>
                            <input type="number" name="total_jam" value="{{ old('total_jam', $magang?->total_jam) }}" min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 720">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah SKS</label>
                            <input type="number" name="jumlah_sks" value="{{ old('jumlah_sks', $magang?->jumlah_sks) }}" min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 20">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">C. Pembimbing & Supervisor</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Pembimbing I <span class="text-red-500">*</span></label>
                        <input type="text" name="dosen_pembimbing_1" value="{{ old('dosen_pembimbing_1', $magang?->dosen_pembimbing_1) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Pembimbing II</label>
                        <input type="text" name="dosen_pembimbing_2" value="{{ old('dosen_pembimbing_2', $magang?->dosen_pembimbing_2) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor/Mentor <span class="text-red-500">*</span></label>
                        <input type="text" name="supervisor" value="{{ old('supervisor', $magang?->supervisor) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Supervisor</label>
                        <input type="text" name="jabatan_supervisor" value="{{ old('jabatan_supervisor', $magang?->jabatan_supervisor) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Kepala Bidang TI">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">D. Kop Surat</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kop Surat</label>
                    <select name="kop_surats_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Kop Surat --</option>
                        @foreach ($kopSurats as $k)
                            <option value="{{ $k->id }}" {{ old('kop_surats_id', $magang?->kop_surats_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->jenis === 'kampus' ? 'Kampus' : 'Instansi' }} - {{ $k->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    {{ $magang ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('magang.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection