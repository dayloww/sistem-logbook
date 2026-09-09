@extends('layouts.app')

@section('page-title', 'Tambah Mahasiswa')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('mahasiswa.index') }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Data Mahasiswa
                </a>
            </div>
            <h1 class="page-title">Tambah Mahasiswa</h1>
            <p class="page-subtitle">Masukkan identitas mahasiswa baru</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:640px; padding:28px;">
    <form action="{{ route('mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display:flex; flex-direction:column; gap:18px;">

            <div>
                <label class="form-label">NIM <span class="required">*</span></label>
                <input type="text" name="nim" value="{{ old('nim') }}" required
                    class="form-input {{ $errors->has('nim') ? 'error' : '' }}"
                    placeholder="Contoh: 212110023">
                @error('nim') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                    class="form-input {{ $errors->has('nama') ? 'error' : '' }}"
                    placeholder="Nama lengkap mahasiswa">
                @error('nama') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Program Studi <span class="required">*</span></label>
                <input type="text" name="program_studi" value="{{ old('program_studi') }}" required
                    class="form-input {{ $errors->has('program_studi') ? 'error' : '' }}"
                    placeholder="Contoh: D4 Teknik Informatika">
                @error('program_studi') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Jurusan <span class="required">*</span></label>
                    <input type="text" name="jurusan" value="{{ old('jurusan') }}" required
                        class="form-input {{ $errors->has('jurusan') ? 'error' : '' }}"
                        placeholder="Contoh: Teknologi Informatika">
                    @error('jurusan') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Perguruan Tinggi <span class="required">*</span></label>
                    <input type="text" name="perguruan_tinggi" value="{{ old('perguruan_tinggi', 'Politeknik Negeri Nusa Utara') }}" required
                        class="form-input {{ $errors->has('perguruan_tinggi') ? 'error' : '' }}">
                    @error('perguruan_tinggi') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Tanda Tangan (TTD)</label>
                <input type="file" name="ttd" accept="image/png,image/jpeg" class="form-file">
                <div class="form-hint">PNG/JPG, maks 2MB. Digunakan otomatis pada dokumen ekspor.</div>
                @error('ttd') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection