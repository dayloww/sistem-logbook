@extends('layouts.app')

@section('page-title', 'Time Schedule')

@section('content')
@php
    use App\Support\RomanNumeral;
@endphp

<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.show', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $magang->mahasiswa->nama }}
                </a>
            </div>
            <h1 class="page-title">Time Schedule</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('magang.task.index', $magang) }}" class="btn btn-secondary">Kegiatan / Task</a>
            <a href="{{ route('magang.time-schedule.create', $magang) }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jadwal
            </a>
        </div>
    </div>
</div>

{{-- INFO BAR --}}
<div class="card" style="padding:14px 20px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:6px; font-size:13.5px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#64748B">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span style="color:#64748B;">Periode:</span>
        <span style="font-weight:600; color:#0F172A;">{{ $magang->tanggal_mulai->format('d M Y') }} – {{ $magang->tanggal_selesai->format('d M Y') }}</span>
        <span style="margin-left:14px; color:#64748B;">Total: <span style="font-weight:600; color:#0F172A;">{{ $schedules->count() }}</span> jadwal</span>
    </div>
</div>

{{-- FILTER --}}
<form method="GET" action="{{ route('magang.time-schedule.index', $magang) }}" class="card" style="padding:16px 20px; margin-bottom:20px; display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:12px; align-items:end;">
    <div>
        <label class="form-label" style="margin-bottom:6px;">Cari Kegiatan</label>
        <input type="text" name="cari" value="{{ $filters['cari'] ?? '' }}" class="form-input" style="height:38px;" placeholder="Kata kunci">
    </div>
    <div>
        <label class="form-label" style="margin-bottom:6px;">Minggu</label>
        <select name="minggu" class="form-select" style="height:38px;">
            <option value="">Semua</option>
            @foreach (range(1, $jumlahMinggu) as $week)
                <option value="{{ $week }}" @selected(($filters['minggu'] ?? null) !== null && (int) $filters['minggu'] === $week)>
                    Minggu {{ RomanNumeral::convert($week) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label" style="margin-bottom:6px;">Status</label>
        <select name="status" class="form-select" style="height:38px;">
            <option value="">Semua</option>
            @foreach (['belum_dimulai' => 'Belum Dimulai', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'ditunda' => 'Ditunda'] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label" style="margin-bottom:6px;">Prioritas</label>
        <select name="prioritas" class="form-select" style="height:38px;">
            <option value="">Semua</option>
            @foreach (['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['prioritas'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label" style="margin-bottom:6px;">Dari Tanggal</label>
        <input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}" class="form-input" style="height:38px;">
    </div>
    <div>
        <label class="form-label" style="margin-bottom:6px;">Sampai Tanggal</label>
        <div style="display:flex; gap:8px;">
            <input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}" class="form-input" style="height:38px;">
            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Filter</button>
            <a href="{{ route('magang.time-schedule.index', $magang) }}" class="btn btn-secondary" style="white-space:nowrap;">Reset</a>
        </div>
    </div>
</form>

<div class="table-container">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px; text-align:center;">No</th>
                    <th style="text-align:center; white-space:nowrap;">Minggu</th>
                    <th style="white-space:nowrap;">Periode</th>
                    <th>Kegiatan</th>
                    <th>Target</th>
                    <th style="text-align:center;">Prioritas</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $i => $schedule)
                    <tr>
                        <td style="text-align:center; color:#94A3B8; font-size:12px;">{{ $i + 1 }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-gray">{{ 'Minggu '.RomanNumeral::convert($schedule->minggu_ke) }}</span>
                        </td>
                        <td style="color:#64748B; font-size:13px; white-space:nowrap;">
                            {{ $schedule->tanggal_mulai->format('d/m/Y') }} – {{ $schedule->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td>
                            <a href="{{ route('magang.time-schedule.show', [$magang, $schedule]) }}" class="action-link action-link-view" style="font-size:13px;">
                                {{ $schedule->kegiatan }}
                            </a>
                            @if ($schedule->deskripsi)
                                <div style="font-size:12px; color:#94A3B8; max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $schedule->deskripsi }}</div>
                            @endif
                        </td>
                        <td style="color:#64748B; font-size:13px; max-width:160px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:150px;">{{ $schedule->target ?? '-' }}</div>
                        </td>
                        <td style="text-align:center;">
                            @php
                                $prioritasBadge = $schedule->prioritas === 'tinggi' ? 'badge-danger' : ($schedule->prioritas === 'rendah' ? 'badge-gray' : 'badge-warning');
                            @endphp
                            <span class="badge {{ $prioritasBadge }}">{{ $schedule->prioritas_label }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge {{ $schedule->status_badge }}">{{ $schedule->status_label }}</span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('magang.time-schedule.show', [$magang, $schedule]) }}" class="action-link action-link-view" style="margin-right:10px;">Detail</a>
                            <a href="{{ route('magang.time-schedule.edit', [$magang, $schedule]) }}" class="action-link action-link-edit" style="margin-right:10px;">Edit</a>
                            <form action="{{ route('magang.time-schedule.destroy', [$magang, $schedule]) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus jadwal \"{{ $schedule->kegiatan }}\"?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link action-link-delete" style="background:none; border:none; padding:0; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">Belum ada time schedule</div>
                                <div class="empty-state-desc">Buat jadwal kegiatan sebagai sumber daftar Kegiatan/Task pada Input Logbook.</div>
                                <a href="{{ route('magang.time-schedule.create', $magang) }}" class="btn btn-primary">Tambah Jadwal</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection