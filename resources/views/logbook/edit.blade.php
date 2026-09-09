@extends('layouts.app')

@section('page-title', 'Edit Logbook')

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
            <h1 class="page-title">Edit Logbook</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $logbook->tanggal->translatedFormat('l, d/m/Y') }}</p>
        </div>
    </div>
</div>

<div class="card" style="max-width:640px; padding:28px;">
    <form action="{{ route('logbook.update', $logbook) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="display:flex; flex-direction:column; gap:18px;">
            <div>
                <label class="form-label">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" id="logbook-tanggal" value="{{ old('tanggal', $logbook->tanggal->format('Y-m-d')) }}" required
                    class="form-input {{ $errors->has('tanggal') ? 'error' : '' }}">
                @error('tanggal') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Kegiatan / Task <span class="required">*</span></label>
                <select name="task_id" id="logbook-task" required
                    class="form-select {{ $errors->has('task_id') ? 'error' : '' }}" style="height:40px;"
                    data-url="{{ route('magang.time-schedule.available', $logbook->magang) }}">
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach ($tasks as $task)
                        <option value="{{ $task->id }}" {{ old('task_id', $logbook->task_id) == $task->id ? 'selected' : '' }}
                            data-target="{{ $task->timeSchedule?->target ?? '' }}">
                            {{ $task->nama_kegiatan }} ({{ $task->progress }}%)
                        </option>
                    @endforeach
                </select>
                @error('task_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Deskripsi Tugas <span class="required">*</span></label>
                <textarea name="deskripsi_tugas" rows="3" required
                    class="form-textarea {{ $errors->has('deskripsi_tugas') ? 'error' : '' }}">{{ old('deskripsi_tugas', $logbook->deskripsi_tugas) }}</textarea>
                @error('deskripsi_tugas') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label">Target</label>
                <input type="text" name="target" value="{{ old('target', $logbook->target) }}"
                    class="form-input" placeholder="Apa yang ingin dicapai">
                <div class="form-hint">Terisi otomatis dari time schedule (tetap bisa diedit).</div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Persentase Capaian (%) <span class="required">*</span></label>
                    <input type="number" name="persentase_capaian" value="{{ old('persentase_capaian', $logbook->persentase_capaian) }}" min="0" max="100" required
                        class="form-input {{ $errors->has('persentase_capaian') ? 'error' : '' }}">
                    @error('persentase_capaian') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Kendala</label>
                    <input type="text" name="kendala" value="{{ old('kendala', $logbook->kendala) }}"
                        class="form-input" placeholder="Kendala yang dihadapi jika ada">
                </div>
            </div>

            <div style="padding:14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;">
                <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13.5px; color:#374151; margin:0;">
                    <input type="checkbox" name="ttd_confirmed" value="1" {{ old('ttd_confirmed', $logbook->ttd_confirmed) ? 'checked' : '' }}
                        style="margin-top:2px; width:15px; height:15px; accent-color:#2563EB; flex-shrink:0;">
                    <div>
                        <div style="font-weight:500;">Sertakan TTD pada dokumen logbook</div>
                        <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Centang jika data tugas ini sudah valid dan ingin tanda tangan otomatis dicantumkan saat cetak dokumen.</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; align-items:center; gap:10px; padding-top:6px; border-top:1px solid #F1F5F9;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('magang.logbook.index', $magang) }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tanggal = document.getElementById('logbook-tanggal');
        const select = document.getElementById('logbook-task');
        const target = document.querySelector('input[name="target"]');

        if (!tanggal || !select || !target) return;

        function syncTarget(force) {
            const opt = select.options[select.selectedIndex];
            const t = opt && opt.dataset.target;
            if (t && (force || target.value === '')) {
                target.value = t;
            }
        }

        async function reloadTasks() {
            if (!tanggal.value) return;
            const res = await fetch(select.dataset.url + '?tanggal=' + encodeURIComponent(tanggal.value));
            if (!res.ok) return;
            const items = await res.json();
            const current = select.value;
            select.innerHTML = '<option value="">-- Pilih Kegiatan --</option>';
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.task_id;
                opt.textContent = item.nama_kegiatan + ' (' + (item.progress ?? 0) + '%)';
                if (item.target) opt.dataset.target = item.target;
                select.appendChild(opt);
            });
            select.value = current;
            syncTarget(false);
        }

        tanggal.addEventListener('change', reloadTasks);
        select.addEventListener('change', () => syncTarget(true));
        syncTarget(false);
    });
</script>
@endpush