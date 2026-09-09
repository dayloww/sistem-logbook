@extends('layouts.app')

@section('page-title', 'Input Logbook')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.logbook.index', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Daftar Logbook
                </a>
            </div>
            <h1 class="page-title">Input Logbook</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:640px; padding:28px;">
    <form action="{{ route('magang.logbook.store', $magang) }}" method="POST">
        @csrf
        <div style="display:flex; flex-direction:column; gap:18px;">
            <div>
                <label class="form-label">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $defaultDate->format('Y-m-d')) }}" required
                    class="form-input {{ $errors->has('tanggal') ? 'error' : '' }}">
                <div class="form-hint">Periode: {{ $magang->tanggal_mulai->format('d M Y') }} – {{ $magang->tanggal_selesai->format('d M Y') }}</div>
                @error('tanggal') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Kegiatan / Task <span class="required">*</span></label>
                <select name="task_id" required
                    class="form-select {{ $errors->has('task_id') ? 'error' : '' }}" style="height:40px;">
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach ($tasks as $task)
                        <option value="{{ $task->id }}" {{ old('task_id', $selectedTaskId ?? null) == $task->id ? 'selected' : '' }}>
                            {{ $task->nama_kegiatan }} ({{ $task->progress }}%)
                        </option>
                    @endforeach
                </select>
                @if ($tasks->isEmpty())
                    <div class="form-hint" style="color:#D97706;">Belum ada kegiatan. <a href="{{ route('magang.task.create', $magang) }}" style="color:#2563EB;">Buat kegiatan</a> terlebih dahulu agar progress dapat dihitung.</div>
                @endif
                @error('task_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Deskripsi Tugas <span class="required">*</span></label>
                <textarea name="deskripsi_tugas" rows="3" required
                    class="form-textarea {{ $errors->has('deskripsi_tugas') ? 'error' : '' }}"
                    placeholder="Deskripsikan kegiatan yang dilakukan hari ini">{{ old('deskripsi_tugas') }}</textarea>
                @error('deskripsi_tugas') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Target</label>
                <input type="text" name="target" value="{{ old('target') }}"
                    class="form-input" placeholder="Apa yang ingin dicapai">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Persentase Capaian (%) <span class="required">*</span></label>
                    <input type="number" name="persentase_capaian" value="{{ old('persentase_capaian', 0) }}" min="0" max="100" required
                        class="form-input {{ $errors->has('persentase_capaian') ? 'error' : '' }}" placeholder="0 - 100">
                    @error('persentase_capaian') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Kendala</label>
                    <input type="text" name="kendala" value="{{ old('kendala') }}"
                        class="form-input" placeholder="Kendala yang dihadapi jika ada">
                </div>
            </div>

            <div style="padding:14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;">
                <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13.5px; color:#374151; margin:0;">
                    <input type="checkbox" name="ttd_confirmed" value="1" {{ old('ttd_confirmed') ? 'checked' : '' }}
                        style="margin-top:2px; width:15px; height:15px; accent-color:#2563EB; flex-shrink:0;">
                    <div>
                        <div style="font-weight:500;">Sertakan TTD pada dokumen logbook</div>
                        <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Centang jika data tugas ini sudah valid dan ingin tanda tangan otomatis dicantumkan saat cetak dokumen.</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">Simpan Logbook</button>
                <a href="{{ route('magang.logbook.index', $magang) }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection