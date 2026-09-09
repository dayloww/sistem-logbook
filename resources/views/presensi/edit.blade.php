@extends('layouts.app')

@section('page-title', 'Edit Presensi')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.presensi.index', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Daftar Presensi
                </a>
            </div>
            <h1 class="page-title">Edit Presensi</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $presensi->tanggal->translatedFormat('l, d/m/Y') }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:520px; padding:28px;">
    <form action="{{ route('presensi.update', $presensi) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="display:flex; flex-direction:column; gap:18px;">
            <div>
                <label class="form-label">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $presensi->tanggal->format('Y-m-d')) }}" required
                    class="form-input {{ $errors->has('tanggal') ? 'error' : '' }}">
                @error('tanggal') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Jam Masuk</label>
                    <input type="time" name="jam_masuk"
                        value="{{ old('jam_masuk', $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '') }}"
                        class="form-input {{ $errors->has('jam_masuk') ? 'error' : '' }}">
                    @error('jam_masuk') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Jam Pulang</label>
                    <input type="time" name="jam_pulang"
                        value="{{ old('jam_pulang', $presensi->jam_pulang ? \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i') : '') }}"
                        class="form-input {{ $errors->has('jam_pulang') ? 'error' : '' }}">
                    @error('jam_pulang') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $presensi->keterangan) }}"
                    class="form-input" placeholder="Opsional">
            </div>

            <div style="padding:14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;">
                <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13.5px; color:#374151; margin:0;">
                    <input type="checkbox" name="ttd_confirmed" value="1"
                        {{ old('ttd_confirmed', $presensi->ttd_confirmed) ? 'checked' : '' }}
                        style="margin-top:2px; width:15px; height:15px; accent-color:#2563EB; flex-shrink:0;">
                    <div>
                        <div style="font-weight:500;">Sertakan TTD pada dokumen presensi</div>
                        <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Centang jika data jam ini sudah benar dan tanda tangan otomatis ingin dipasang.</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('magang.presensi.index', $magang) }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection