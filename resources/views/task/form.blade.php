@extends('layouts.app')

@section('page-title', $task ? 'Edit Kegiatan' : 'Tambah Kegiatan')

@section('content')
@php
    $isEdit = $task !== null;
    $otherBobot = $magang->tasks()
        ->when($task, fn ($q) => $q->where('id', '!=', $task->id))
        ->sum('bobot');
@endphp

<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.task.index', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Daftar Kegiatan
                </a>
            </div>
            <h1 class="page-title">{{ $isEdit ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:680px; padding:28px;">
    <form action="{{ $isEdit ? route('magang.task.update', [$magang, $task]) : route('magang.task.store', $magang) }}" method="POST">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div style="display:flex; flex-direction:column; gap:18px;">
            <div>
                <label class="form-label">Nama Kegiatan <span class="required">*</span></label>
                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $task?->nama_kegiatan) }}" required
                    class="form-input {{ $errors->has('nama_kegiatan') ? 'error' : '' }}"
                    placeholder="Contoh: Membuat Tampilan UI Dashboard">
                @error('nama_kegiatan') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="form-textarea {{ $errors->has('deskripsi') ? 'error' : '' }}"
                    placeholder="Penjelasan lingkup pekerjaan kegiatan ini">{{ old('deskripsi', $task?->deskripsi) }}</textarea>
                @error('deskripsi') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Bobot (%) <span class="required">*</span></label>
                <input type="number" name="bobot" value="{{ old('bobot', $task?->bobot ?? 0) }}" min="0" max="100" required
                    class="form-input {{ $errors->has('bobot') ? 'error' : '' }}" placeholder="0 - 100">
                <div class="form-hint">
                    Bobot kontribusi kegiatan terhadap progress keseluruhan.
                    Total bobot saat ini: <span style="font-weight:600; color:#0F172A;">{{ App\Support\Percent::format($otherBobot) }}%</span>,
                    sisa yang dapat ditambahkan: <span style="font-weight:600; color:#2563EB;">{{ max(100 - $otherBobot, 0) }}%</span>.
                </div>
                @error('bobot') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $task?->tanggal_mulai?->format('Y-m-d')) }}"
                        class="form-input {{ $errors->has('tanggal_mulai') ? 'error' : '' }}">
                    @error('tanggal_mulai') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $task?->tanggal_selesai?->format('Y-m-d')) }}"
                        class="form-input {{ $errors->has('tanggal_selesai') ? 'error' : '' }}">
                    @error('tanggal_selesai') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-hint" style="margin-top:-10px;">Periode magang: {{ $magang->tanggal_mulai->format('d M Y') }} – {{ $magang->tanggal_selesai->format('d M Y') }}</div>

            <div style="padding:14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; font-size:12.5px; color:#64748B; line-height:1.6;">
                <strong style="color:#374151;">Progress otomatis</strong><br>
                Progress kegiatan dihitung otomatis dari logbook harian (progres tertinggi), bukan diisi manual di sini. Isi logbook untuk memperbarui progress.
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}</button>
                <a href="{{ route('magang.task.index', $magang) }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection