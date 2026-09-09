@extends('layouts.app')

@section('page-title', $kopSurat ? 'Edit Kop Surat' : 'Tambah Kop Surat')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('kop-surat.index') }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Pengaturan Kop Surat
                </a>
            </div>
            <h1 class="page-title">{{ $kopSurat ? 'Edit Kop Surat' : 'Tambah Kop Surat' }}</h1>
            <p class="page-subtitle">Konfigurasi kop kampus atau instansi yang digunakan pada dokumen</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:680px; padding:28px;">
    <form action="{{ $kopSurat ? route('kop-surat.update', $kopSurat) : route('kop-surat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if ($kopSurat) @method('PUT') @endif

        <div style="display:flex; flex-direction:column; gap:18px;">
            <div>
                <label class="form-label">Jenis Kop <span class="required">*</span></label>
                <select name="jenis" required class="form-select {{ $errors->has('jenis') ? 'error' : '' }}">
                    <option value="kampus" {{ old('jenis', $kopSurat?->jenis) === 'kampus' ? 'selected' : '' }}>Kampus / Program Studi</option>
                    <option value="instansi" {{ old('jenis', $kopSurat?->jenis) === 'instansi' ? 'selected' : '' }}>Instansi / Lokasi Magang</option>
                </select>
                @error('jenis') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Nama Instansi / Perguruan Tinggi <span class="required">*</span></label>
                <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $kopSurat?->nama_instansi) }}" required
                    class="form-input {{ $errors->has('nama_instansi') ? 'error' : '' }}"
                    placeholder="Contoh: Politeknik Negeri Nusa Utara">
                @error('nama_instansi') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat" rows="2"
                    class="form-textarea {{ $errors->has('alamat') ? 'error' : '' }}"
                    placeholder="Alamat lengkap instansi/kampus">{{ old('alamat', $kopSurat?->alamat) }}</textarea>
                @error('alamat') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Telepon / Fax</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $kopSurat?->telepon) }}"
                        class="form-input {{ $errors->has('telepon') ? 'error' : '' }}"
                        placeholder="(0432) 24745">
                    @error('telepon') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Website</label>
                    <input type="text" name="website" value="{{ old('website', $kopSurat?->website) }}"
                        class="form-input {{ $errors->has('website') ? 'error' : '' }}"
                        placeholder="polnustar.ac.id">
                    @error('website') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Logo Instansi / Kampus</label>
                @if ($kopSurat?->logo_path)
                    <div style="margin-bottom:12px; padding:12px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; display:inline-flex; align-items:center; gap:12px;">
                        <img src="{{ asset('storage/'.$kopSurat->logo_path) }}" alt="Logo {{ $kopSurat->nama_instansi }}"
                             style="height:48px; object-fit:contain;"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<span style=\'font-size:12px; color:#94A3B8;\'>[Logo tidak ditemukan]</span>';">
                        <span style="font-size:12px; color:#64748B;">Logo saat ini tersimpan. Biarkan kosong jika tidak ingin mengubah.</span>
                    </div>
                @endif
                <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="form-file">
                <div class="form-hint">Maks 2MB. Format: PNG, JPG, SVG. Diutamakan latar belakang transparan.</div>
                @error('logo') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:8px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">
                    {{ $kopSurat ? 'Simpan Perubahan' : 'Simpan Kop Surat' }}
                </button>
                <a href="{{ route('kop-surat.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection