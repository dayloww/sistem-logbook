@extends('layouts.app')

@section('page-title', $magang ? 'Edit Data Magang' : 'Tambah Data Magang')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.index') }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Data Magang
                </a>
            </div>
            <h1 class="page-title">{{ $magang ? 'Edit Data Magang' : 'Tambah Data Magang' }}</h1>
            <p class="page-subtitle">Data magang digunakan pada seluruh dokumen keluaran</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:760px; padding:28px;">
    <form action="{{ $magang ? route('magang.update', $magang) : route('magang.store') }}" method="POST">
        @csrf
        @if ($magang) @method('PUT') @endif
        <div style="display:flex; flex-direction:column; gap:28px;">

            {{-- A. Data Mahasiswa --}}
            <div>
                <div class="form-section-title">A. Data Mahasiswa</div>
                <div>
                    <label class="form-label">Mahasiswa <span class="required">*</span></label>
                    <select name="mahasiswa_id" required class="form-select {{ $errors->has('mahasiswa_id') ? 'error' : '' }}" style="height:40px;">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($mahasiswa as $m)
                            <option value="{{ $m->id }}" {{ old('mahasiswa_id', $magang?->mahasiswa_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->nama }} ({{ $m->nim }})
                            </option>
                        @endforeach
                    </select>
                    @error('mahasiswa_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- B. Informasi Kegiatan --}}
            <div>
                <div class="form-section-title">B. Informasi Kegiatan</div>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div>
                        <label class="form-label">Judul/Topik Kegiatan <span class="required">*</span></label>
                        <input type="text" name="judul_topik" value="{{ old('judul_topik', $magang?->judul_topik) }}" required
                            class="form-input {{ $errors->has('judul_topik') ? 'error' : '' }}"
                            placeholder="Contoh: Pengembangan Sistem Informasi Kearsipan">
                        @error('judul_topik') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label class="form-label">Instansi <span class="required">*</span></label>
                            <input type="text" name="instansi" value="{{ old('instansi', $magang?->instansi) }}" required
                                class="form-input {{ $errors->has('instansi') ? 'error' : '' }}"
                                placeholder="Nama instansi/lokasi magang">
                            @error('instansi') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Bagian/Unit Kerja</label>
                            <input type="text" name="bagian_unit" value="{{ old('bagian_unit', $magang?->bagian_unit) }}"
                                class="form-input" placeholder="Contoh: Bidang TI">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Lokasi <span class="required">*</span></label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $magang?->lokasi) }}" required
                            class="form-input {{ $errors->has('lokasi') ? 'error' : '' }}"
                            placeholder="Kota/kabupaten lokasi magang">
                        @error('lokasi') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
                        <div>
                            <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $magang?->tanggal_mulai?->format('Y-m-d')) }}" required
                                class="form-input {{ $errors->has('tanggal_mulai') ? 'error' : '' }}">
                            @error('tanggal_mulai') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Tanggal Selesai <span class="required">*</span></label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $magang?->tanggal_selesai?->format('Y-m-d')) }}" required
                                class="form-input {{ $errors->has('tanggal_selesai') ? 'error' : '' }}">
                            @error('tanggal_selesai') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Lama Kegiatan</label>
                            <input type="text" name="lama_kegiatan" value="{{ old('lama_kegiatan', $magang?->lama_kegiatan) }}"
                                class="form-input" placeholder="Contoh: 6 Bulan">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label class="form-label">Total Jam</label>
                            <input type="number" name="total_jam" value="{{ old('total_jam', $magang?->total_jam) }}" min="1"
                                class="form-input" placeholder="Contoh: 720">
                        </div>
                        <div>
                            <label class="form-label">Jumlah SKS</label>
                            <input type="number" name="jumlah_sks" value="{{ old('jumlah_sks', $magang?->jumlah_sks) }}" min="1"
                                class="form-input" placeholder="Contoh: 20">
                        </div>
                    </div>
                </div>
            </div>

            {{-- C. Pembimbing & Supervisor --}}
            <div>
                <div class="form-section-title">C. Pembimbing & Supervisor</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label class="form-label">Dosen Pembimbing I <span class="required">*</span></label>
                        <input type="text" name="dosen_pembimbing_1" value="{{ old('dosen_pembimbing_1', $magang?->dosen_pembimbing_1) }}" required
                            class="form-input {{ $errors->has('dosen_pembimbing_1') ? 'error' : '' }}">
                        @error('dosen_pembimbing_1') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Dosen Pembimbing II</label>
                        <input type="text" name="dosen_pembimbing_2" value="{{ old('dosen_pembimbing_2', $magang?->dosen_pembimbing_2) }}"
                            class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Supervisor/Mentor <span class="required">*</span></label>
                        <input type="text" name="supervisor" value="{{ old('supervisor', $magang?->supervisor) }}" required
                            class="form-input {{ $errors->has('supervisor') ? 'error' : '' }}">
                        @error('supervisor') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Jabatan Supervisor</label>
                        <input type="text" name="jabatan_supervisor" value="{{ old('jabatan_supervisor', $magang?->jabatan_supervisor) }}"
                            class="form-input" placeholder="Contoh: Kepala Bidang TI">
                    </div>
                </div>
            </div>

            {{-- D. Kop Surat --}}
            <div>
                <div class="form-section-title">D. Kop Surat</div>
                <div>
                    <label class="form-label">Jenis Kop Surat</label>
                    <select name="kop_surats_id" class="form-select" style="height:40px;">
                        <option value="">-- Pilih Kop Surat --</option>
                        @foreach ($kopSurats as $k)
                            <option value="{{ $k->id }}" {{ old('kop_surats_id', $magang?->kop_surats_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->jenis === 'kampus' ? 'Kampus' : 'Instansi' }} - {{ $k->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">
                    {{ $magang ? 'Simpan Perubahan' : 'Simpan Magang' }}
                </button>
                <a href="{{ route('magang.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection