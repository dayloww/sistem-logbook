@extends('layouts.app')

@section('page-title', 'Edit Mahasiswa')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('mahasiswa.show', $mahasiswa) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $mahasiswa->nama }}
                </a>
            </div>
            <h1 class="page-title">Edit Mahasiswa</h1>
            <p class="page-subtitle">Perbarui identitas mahasiswa</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:640px; padding:28px;">
    <form action="{{ route('mahasiswa.update', $mahasiswa) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div style="display:flex; flex-direction:column; gap:18px;">

            <div>
                <label class="form-label">NIM <span class="required">*</span></label>
                <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required
                    class="form-input {{ $errors->has('nim') ? 'error' : '' }}">
                @error('nim') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                    class="form-input {{ $errors->has('nama') ? 'error' : '' }}">
                @error('nama') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Program Studi <span class="required">*</span></label>
                <input type="text" name="program_studi" value="{{ old('program_studi', $mahasiswa->program_studi) }}" required
                    class="form-input {{ $errors->has('program_studi') ? 'error' : '' }}">
                @error('program_studi') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Jurusan <span class="required">*</span></label>
                    <input type="text" name="jurusan" value="{{ old('jurusan', $mahasiswa->jurusan) }}" required
                        class="form-input {{ $errors->has('jurusan') ? 'error' : '' }}">
                    @error('jurusan') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Perguruan Tinggi <span class="required">*</span></label>
                    <input type="text" name="perguruan_tinggi" value="{{ old('perguruan_tinggi', $mahasiswa->perguruan_tinggi) }}" required
                        class="form-input {{ $errors->has('perguruan_tinggi') ? 'error' : '' }}">
                    @error('perguruan_tinggi') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Tanda Tangan (TTD)</label>
                @if ($mahasiswa->ttd_path)
                    <div style="margin-bottom:10px; display:flex; align-items:center; gap:16px; padding:10px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
                        <img src="{{ asset('storage/'.$mahasiswa->ttd_path) }}" alt="TTD {{ $mahasiswa->nama }}"
                             style="height:48px; object-fit:contain;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#64748B; cursor:pointer; margin:0;">
                            <input type="checkbox" name="hapus_ttd" value="1" style="border-radius:4px; width:15px; height:15px;">
                            Hapus TTD ini
                        </label>
                    </div>
                @else
                    <p class="form-hint" style="margin-bottom:6px;">Belum ada TTD tersimpan.</p>
                @endif
                <input type="file" name="ttd" accept="image/png,image/jpeg" class="form-file">
                <div class="form-hint">PNG/JPG, maks 2MB. Biarkan kosong untuk mempertahankan TTD saat ini.</div>
                @error('ttd') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection