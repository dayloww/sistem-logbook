@extends('layouts.app')

@section('page-title', $timeSchedule ? 'Edit Time Schedule' : 'Tambah Time Schedule')

@section('content')
@php
    use App\Support\RomanNumeral;

    $isEdit = $timeSchedule !== null;
    $defaultMulai = old('tanggal_mulai', $timeSchedule?->tanggal_mulai?->format('Y-m-d') ?? $magang->tanggal_mulai->format('Y-m-d'));
    $defaultSelesai = old('tanggal_selesai', $timeSchedule?->tanggal_selesai?->format('Y-m-d') ?? $defaultMulai);
    $magangMulai = $magang->tanggal_mulai->format('Y-m-d');

    try {
        $mingguDisplay = $magang->mingguKe($defaultMulai);
    } catch (\Throwable $e) {
        $mingguDisplay = 1;
    }
@endphp

<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.time-schedule.index', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Daftar Time Schedule
                </a>
            </div>
            <h1 class="page-title">{{ $isEdit ? 'Edit Time Schedule' : 'Tambah Time Schedule' }}</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:680px; padding:28px;">
    <form action="{{ $isEdit ? route('magang.time-schedule.update', [$magang, $timeSchedule]) : route('magang.time-schedule.store', $magang) }}" method="POST">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div style="display:flex; flex-direction:column; gap:18px;">
            <div style="display:grid; grid-template-columns:1fr 2fr; gap:16px;">
                <div>
                    <label class="form-label">Minggu</label>
                    <input type="text" id="minggu-display" value="{{ 'Minggu '.RomanNumeral::convert($mingguDisplay) }}" readonly
                        class="form-input" style="background:#F8FAFC; color:#64748B;">
                    <div class="form-hint">Dihitung otomatis dari tanggal mulai</div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal-mulai" value="{{ $defaultMulai }}" required
                            class="form-input {{ $errors->has('tanggal_mulai') ? 'error' : '' }}">
                        @error('tanggal_mulai') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai <span class="required">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ $defaultSelesai }}" required
                            class="form-input {{ $errors->has('tanggal_selesai') ? 'error' : '' }}">
                        @error('tanggal_selesai') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="form-hint" style="margin-top:-10px;">Periode magang: {{ $magang->tanggal_mulai->format('d M Y') }} – {{ $magang->tanggal_selesai->format('d M Y') }}</div>

            <div>
                <label class="form-label">Kegiatan <span class="required">*</span></label>
                <input type="text" name="kegiatan" value="{{ old('kegiatan', $timeSchedule?->kegiatan) }}" required
                    class="form-input {{ $errors->has('kegiatan') ? 'error' : '' }}"
                    placeholder="Contoh: Membuat Tampilan UI Dashboard">
                @error('kegiatan') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="form-textarea {{ $errors->has('deskripsi') ? 'error' : '' }}"
                    placeholder="Penjelasan lingkup pekerjaan kegiatan ini">{{ old('deskripsi', $timeSchedule?->deskripsi) }}</textarea>
                @error('deskripsi') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Target</label>
                <input type="text" name="target" value="{{ old('target', $timeSchedule?->target) }}"
                    class="form-input {{ $errors->has('target') ? 'error' : '' }}"
                    placeholder="Sasaran yang ingin dicapai">
                <div class="form-hint">Target ini akan terisi otomatis pada Input Logbook (tetap dapat diedit).</div>
                @error('target') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Prioritas <span class="required">*</span></label>
                    <select name="prioritas" required
                        class="form-select {{ $errors->has('prioritas') ? 'error' : '' }}" style="height:40px;">
                        @foreach (['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('prioritas', $timeSchedule?->prioritas ?? 'sedang') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('prioritas') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status" required
                        class="form-select {{ $errors->has('status') ? 'error' : '' }}" style="height:40px;">
                        @foreach (['belum_dimulai' => 'Belum Dimulai', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'ditunda' => 'Ditunda'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $timeSchedule?->status ?? 'belum_dimulai') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Catatan</label>
                <textarea name="catatan" rows="2"
                    class="form-textarea {{ $errors->has('catatan') ? 'error' : '' }}"
                    placeholder="Catatan tambahan jika ada">{{ old('catatan', $timeSchedule?->catatan) }}</textarea>
                @error('catatan') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="padding:14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; font-size:12.5px; color:#64748B; line-height:1.6;">
                <strong style="color:#374151;">Task otomatis</strong><br>
                Menyimpan jadwal akan membuat satu <strong>Kegiatan / Task</strong> terkait (berbobot 0) yang menjadi sumber dropdown Input Logbook. Atur bobot pada halaman Kegiatan / Task. Progress task dihitung otomatis dari logbook harian.
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jadwal' }}</button>
                <a href="{{ route('magang.time-schedule.index', $magang) }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mulai = document.getElementById('tanggal-mulai');
        const mingguEl = document.getElementById('minggu-display');
        const magangMulai = @json($magangMulai);

        function computeWeek(dateStr) {
            if (!dateStr) return '-';
            const start = new Date(magangMulai + 'T00:00:00');
            const date = new Date(dateStr + 'T00:00:00');
            if (isNaN(date.getTime())) return '-';
            const diffDays = Math.round((date - start) / 86400000);
            return Math.max(1, Math.floor(diffDays / 7) + 1);
        }

        const romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV'];

        mulai.addEventListener('change', () => {
            const week = computeWeek(mulai.value);
            mingguEl.value = week === '-' ? '-' : 'Minggu ' + (romans[week] || week);
        });
    });
</script>
@endpush