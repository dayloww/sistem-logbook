@extends('layouts.app')

@section('page-title', 'Catatan Mingguan')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.show', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $magang->mahasiswa->nama }}
                </a>
            </div>
            <h1 class="page-title">Catatan Mingguan</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
    </div>
</div>

<div class="card" style="padding:22px; margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:14px;">Input / Perbarui Catatan Minggu</div>
    <form action="{{ route('magang.catatan.store', $magang) }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns:180px 1fr 120px; gap:16px; align-items:flex-end;">
            <div>
                <label class="form-label">Minggu Ke- <span class="required">*</span></label>
                <select name="minggu_ke" required class="form-select {{ $errors->has('minggu_ke') ? 'error' : '' }}">
                    <option value="">Pilih minggu</option>
                    @for ($i = 1; $i <= $jumlahMinggu; $i++)
                        <option value="{{ $i }}" {{ old('minggu_ke') == $i ? 'selected' : '' }}>Minggu {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label">Catatan / Feedback <span class="required">*</span></label>
                <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Catatan progress, kendala, atau evaluasi mingguan"
                    class="form-input {{ $errors->has('catatan') ? 'error' : '' }}" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Simpan
                </button>
            </div>
        </div>
        @error('minggu_ke') <div class="form-error">{{ $message }}</div> @enderror
        @error('catatan') <div class="form-error">{{ $message }}</div> @enderror
    </form>
</div>

<div class="table-container">
    <div class="card-header">
        <div class="card-title">Daftar Catatan Mingguan</div>
        <span style="font-size:12px; color:#94A3B8;">{{ $catatan->count() }} catatan tersimpan</span>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:90px; text-align:center;">Minggu</th>
                    <th>Catatan Evaluasi / Keterangan</th>
                    <th style="width:200px;">Terakhir Diperbarui</th>
                    <th style="width:100px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($catatan as $c)
                    <tr>
                        <td style="text-align:center;">
                            <span class="week-badge">{{ $c->minggu_ke }}</span>
                        </td>
                        <td style="color:#0F172A; font-weight:500;">{{ $c->catatan }}</td>
                        <td style="color:#64748B; font-size:12.5px;">{{ $c->updated_at->translatedFormat('d M Y H:i') }}</td>
                        <td style="text-align:right; white-space:nowrap;">
                            <form action="{{ route('catatan.destroy', $c) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus catatan minggu ke-{{ $c->minggu_ke }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link action-link-delete" style="background:none; border:none; padding:0; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">Belum ada catatan mingguan</div>
                                <div class="empty-state-desc">Tambahkan catatan atau feedback mingguan menggunakan form di atas.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection