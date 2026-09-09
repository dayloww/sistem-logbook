@extends('layouts.app')

@section('page-title', 'Progress Magang')

@section('content')
@php
    use App\Support\Percent;

    $has = $progress !== null;
@endphp

<div class="page-header">
    <div class="page-header-inner">
        <div>
            <h1 class="page-title">Progress Magang</h1>
            <p class="page-subtitle">Progress keseluruhan berbasis bobot kegiatan dengan snapshot mingguan</p>
        </div>
        @if ($has && $magangList->count() > 1)
            <form method="GET" action="{{ route('progress') }}">
                <select name="magang" onchange="this.form.submit()" class="form-select" style="width:260px;" aria-label="Pilih magang">
                    @foreach ($magangList as $m)
                        <option value="{{ $m->id }}" @selected($selected && $selected->id === $m->id)>
                            {{ $m->mahasiswa->nama }} – {{ $m->instansi }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>
</div>

@if (!$has)
    <div class="table-container">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="empty-state-title">Belum ada data magang</div>
            <div class="empty-state-desc">Tambahkan data magang terlebih dahulu untuk melihat progress.</div>
            <a href="{{ route('magang.create') }}" class="btn btn-primary">Tambah Magang</a>
        </div>
    </div>
@endif

@if ($has)
{{-- OVERVIEW ROW --}}
<div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:20px;" class="sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card stagger-1">
        <div class="stat-card-value" style="color:#2563EB; font-size:32px;">{{ Percent::format($progress['overall']) }}%</div>
        <div class="stat-card-label">Progress Keseluruhan</div>
        <div class="stat-card-sub">{{ $selected->mahasiswa->nama }}</div>
    </div>
    <div class="stat-card stagger-2">
        <div class="stat-card-value" style="color:#0EA5E9; font-size:32px;">{{ Percent::format($progress['current_week_progress']) }}%</div>
        <div class="stat-card-label">Progress Minggu Ini</div>
        <div class="stat-card-sub">Minggu {{ $progress['current_week'] }} dari {{ $progress['jumlah_minggu'] }}</div>
    </div>
    <div class="stat-card stagger-3">
        <div class="stat-card-value" style="color:{{ $progress['perkembangan_minggu_ini'] < 0 ? '#DC2626' : '#16A34A' }}; font-size:32px;">
            {{ Percent::signed($progress['perkembangan_minggu_ini']) }}%
        </div>
        <div class="stat-card-label">Perkembangan Minggu Ini</div>
        <div class="stat-card-sub">dibanding minggu lalu</div>
    </div>
    <div class="stat-card stagger-4">
        <div class="stat-card-value" style="font-size:32px;">{{ $progress['total_tasks'] }}</div>
        <div class="stat-card-label">Total Task</div>
        <div class="stat-card-sub">
            <span style="color:#16A34A;">{{ $progress['selesai'] }} selesai</span> · <span style="color:#2563EB;">{{ $progress['berjalan'] }} berjalan</span> · <span style="color:#64748B;">{{ $progress['belum'] }} belum</span>
        </div>
    </div>
</div>

@if (!$progress['bobot_penuh'])
    <div class="alert alert-warning" role="alert" style="margin-bottom:20px;">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0; margin-top:1px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>Total bobot seluruh kegiatan baru <strong>{{ Percent::format($progress['total_bobot']) }}%</strong>. Sebaiknya lengkapi hingga 100% agar progress keseluruhan akurat.</div>
    </div>
@endif

{{-- CHART --}}
<div style="margin-bottom:20px;">
    <x-task-progress-chart :progress="$progress" />
</div>

{{-- PROGRESS MINGGUAN --}}
<div class="table-container" style="margin-bottom:20px;">
    <div class="card-header">
        <div>
            <div class="card-title">Progress Mingguan</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Snapshot posisi progress keseluruhan di akhir tiap minggu</div>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px; text-align:center;">No</th>
                    <th style="white-space:nowrap;">Minggu</th>
                    <th style="white-space:nowrap;">Periode</th>
                    <th style="text-align:right;">Progress</th>
                    <th style="text-align:right;">Perkembangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($progress['week_rows'] as $w)
                    <tr style="{{ $w['is_current'] ? 'background:#EFF6FF;' : '' }}">
                        <td style="text-align:center; color:#94A3B8; font-size:12px;">{{ $w['minggu_ke'] }}</td>
                        <td style="white-space:nowrap; font-weight:600; color:#0F172A;">
                            {{ $w['minggu'] }}
                            @if ($w['is_current'])
                                <span class="badge badge-info" style="margin-left:6px;">Berjalan</span>
                            @endif
                        </td>
                        <td style="color:#64748B; font-size:13px; white-space:nowrap;">{{ $w['tanggal_mulai'] }} – {{ $w['tanggal_selesai'] }}</td>
                        <td style="text-align:right;">
                            @if ($w['progress'] !== null)
                                <span class="badge {{ $w['progress'] >= 100 ? 'badge-success' : ($w['progress'] > 0 ? 'badge-info' : 'badge-gray') }}">
                                    {{ Percent::format($w['progress']) }}%
                                </span>
                            @else
                                <span style="color:#CBD5E1;">—</span>
                            @endif
                        </td>
                        <td style="text-align:right; color:{{ $w['perkembangan'] !== null && $w['perkembangan'] < 0 ? '#DC2626' : '#16A34A' }}; font-weight:600;">
                            @if ($w['perkembangan'] !== null)
                                {{ Percent::signed($w['perkembangan']) }}%
                            @else
                                <span style="color:#CBD5E1;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- DAFTAR TASK --}}
<div class="table-container">
    <div class="card-header">
        <div>
            <div class="card-title">Daftar Kegiatan / Task</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Kontribusi tiap kegiatan = (bobot / 100) × progress</div>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('magang.task.create', $selected) }}" class="btn btn-ghost btn-sm" style="font-size:12px;">Tambah →</a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kegiatan</th>
                    <th style="text-align:center;">Bobot</th>
                    <th style="text-align:center;">Progress</th>
                    <th style="text-align:right;">Kontribusi</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($progress['tasks'] as $task)
                    <tr>
                        <td style="max-width:260px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:240px; font-weight:500; color:#0F172A;">{{ $task->nama_kegiatan }}</div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge badge-gray">{{ $task->bobot }}%</span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:4px; min-width:80px;">
                                <span class="badge {{ $task->progress >= 100 ? 'badge-success' : ($task->progress > 0 ? 'badge-info' : 'badge-gray') }}">
                                    {{ Percent::format($task->progress) }}%
                                </span>
                                <div class="progress-bar" style="width:70px;">
                                    <div class="progress-fill" style="width:{{ min($task->progress, 100) }}%; background:{{ $task->progress >= 100 ? '#16A34A' : ($task->progress > 0 ? '#2563EB' : '#CBD5E1') }};"></div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:right; font-weight:600; color:#0F172A; white-space:nowrap;">
                            {{ Percent::format(($task->bobot / 100) * $task->progress) }}%
                        </td>
                        <td style="text-align:center;">
                            <span class="badge {{ $task->status === 'selesai' ? 'badge-success' : ($task->status === 'berjalan' ? 'badge-info' : 'badge-gray') }}">{{ $task->status_label }}</span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('magang.task.show', [$selected, $task]) }}" class="action-link action-link-view">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-desc">Belum ada kegiatan/task untuk magang ini.</div>
                                <a href="{{ route('magang.task.create', $selected) }}" class="btn btn-primary">Tambah Kegiatan</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection