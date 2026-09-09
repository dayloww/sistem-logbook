@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Ringkasan sistem logbook dan presensi mahasiswa magang</p>
        </div>
        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#64748B;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
</div>

{{-- STAT CARDS --}}
<div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:28px;"
     class="sm:grid-cols-2 lg:grid-cols-4">

    <a href="{{ route('mahasiswa.index') }}" class="stat-card stagger-1" style="text-decoration:none; display:block;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
            <div class="stat-card-icon" style="background:#EFF6FF;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#2563EB">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['mahasiswa'] }}</div>
        <div class="stat-card-label" style="margin-top:4px;">Total Mahasiswa</div>
        <div class="stat-card-sub">Terdaftar di sistem</div>
    </a>

    <a href="{{ route('magang.index') }}" class="stat-card stagger-2" style="text-decoration:none; display:block;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
            <div class="stat-card-icon" style="background:#F0FDF4;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['magang'] }}</div>
        <div class="stat-card-label" style="margin-top:4px;">Data Magang</div>
        <div class="stat-card-sub">Total kegiatan magang</div>
    </a>

    <div class="stat-card stagger-3">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
            <div class="stat-card-icon" style="background:#FEF3C7;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#D97706">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['presensi'] }}</div>
        <div class="stat-card-label" style="margin-top:4px;">Total Presensi</div>
        <div class="stat-card-sub">Entri kehadiran</div>
    </div>

    <div class="stat-card stagger-4">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
            <div class="stat-card-icon" style="background:#F3E8FF;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#7C3AED">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['logbook'] }}</div>
        <div class="stat-card-label" style="margin-top:4px;">Total Logbook</div>
        <div class="stat-card-sub">Entri kegiatan</div>
    </div>
</div>

{{-- PROGRESS MAGANG (berbasis task) --}}
<div style="display:grid; grid-template-columns:1fr; gap:20px; margin-bottom:20px;" class="lg:grid-cols-3">
    <div class="lg:col-span-1">
        <x-task-progress-overview :progress="$taskProgress" :magangs="$magangList" :selected="$selected" />
    </div>
    <div class="lg:col-span-2">
        <x-task-progress-chart :progress="$taskProgress" />
    </div>
</div>

<x-task-progress-list :progress="$taskProgress" :selected="$selected" />

{{-- PROGRES LOGBOOK --}}
<div style="display:grid; grid-template-columns:1fr; gap:20px; margin-bottom:20px; margin-top:20px;" class="lg:grid-cols-3">
    <div class="lg:col-span-1">
        <x-logbook-progress-card :progress="$progress" :magangs="$magangList" :selected="$selected" />
    </div>
    <div class="lg:col-span-2">
        <x-logbook-progress-chart :progress="$progress" />
    </div>
</div>

{{-- TIME SCHEDULE TERDEKAT --}}
@if ($selected && $upcomingSchedules && $upcomingSchedules->isNotEmpty())
<div class="table-container" style="margin-bottom:20px;">
    <div class="card-header">
        <div>
            <div class="card-title">Time Schedule Terdekat</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">{{ $selected->mahasiswa->nama }} · Kegiatan terjadwal aktif / berikutnya</div>
        </div>
        <a href="{{ route('magang.time-schedule.index', $selected) }}" class="btn btn-ghost btn-sm" style="font-size:12px;">
            Lihat semua →
        </a>
    </div>
    <div>
        @foreach ($upcomingSchedules as $ts)
            <a href="{{ route('magang.time-schedule.show', [$selected, $ts]) }}"
               style="display:flex; align-items:center; justify-content:space-between; padding:13px 20px; text-decoration:none; transition: background 120ms;"
               onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                    <div style="width:34px; height:34px; border-radius:50%; background:#FEF3C7; color:#D97706; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:13.5px; font-weight:600; color:#0F172A; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $ts->kegiatan }}</div>
                        <div style="font-size:12px; color:#64748B;">{{ $ts->tanggal_mulai->format('d M') }} – {{ $ts->tanggal_selesai->format('d M Y') }}</div>
                    </div>
                </div>
                <div style="text-align:right; flex-shrink:0; margin-left:12px;">
                    <span class="badge {{ $ts->status_badge }}">{{ $ts->status_label }}</span>
                </div>
            </a>
            @if (!$loop->last)
                <div style="height:1px; background:#F1F5F9; margin:0 20px;"></div>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- BOTTOM SECTION --}}
<div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; margin-bottom:20px;">

    {{-- Magang Aktif --}}
    <div class="table-container">
        <div class="card-header">
            <div>
                <div class="card-title">Magang Aktif</div>
                <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Sedang berjalan saat ini</div>
            </div>
            <a href="{{ route('magang.index') }}" class="btn btn-ghost btn-sm" style="font-size:12px;">
                Lihat semua →
            </a>
        </div>
        <div>
            @forelse ($magangAktif as $m)
                <a href="{{ route('magang.show', $m) }}"
                   style="display:flex; align-items:center; justify-content:space-between; padding:13px 20px; text-decoration:none; transition: background 120ms;"
                   onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                    <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                        <div style="width:34px; height:34px; border-radius:50%; background:#EFF6FF; color:#2563EB; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;">
                            {{ mb_strimwidth($m->mahasiswa->nama, 0, 1) }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:13.5px; font-weight:600; color:#0F172A; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $m->mahasiswa->nama }}</div>
                            <div style="font-size:12px; color:#64748B; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $m->instansi }}</div>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0; margin-left:12px;">
                        <span class="badge badge-success">Berjalan</span>
                        <div style="font-size:11px; color:#94A3B8; margin-top:3px;">{{ $m->tanggal_mulai->format('d M') }} - {{ $m->tanggal_selesai->format('d M Y') }}</div>
                    </div>
                </a>
                @if (!$loop->last)
                    <div style="height:1px; background:#F1F5F9; margin:0 20px;"></div>
                @endif
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="empty-state-title">Tidak ada magang aktif</div>
                    <div class="empty-state-desc">Belum ada kegiatan magang yang sedang berjalan.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Presensi Terakhir --}}
    <div class="table-container">
        <div class="card-header">
            <div class="card-title">Presensi Terakhir</div>
        </div>
        <div>
            @forelse ($recentPresensi as $p)
                <div style="padding:12px 20px;">
                    <div style="font-size:13px; font-weight:600; color:#0F172A;">{{ $p->magang->mahasiswa->nama ?? '-' }}</div>
                    <div style="font-size:12px; color:#64748B; margin-top:2px;">
                        {{ $p->tanggal->translatedFormat('d M Y') }}
                    </div>
                    <div style="font-size:12px; color:#94A3B8; margin-top:1px;">
                        {{ $p->jam_masuk ?? '-' }} – {{ $p->jam_pulang ?? '-' }}
                    </div>
                </div>
                @if (!$loop->last)
                    <div style="height:1px; background:#F1F5F9; margin:0 20px;"></div>
                @endif
            @empty
                <div class="empty-state" style="padding:32px 16px;">
                    <div class="empty-state-desc">Belum ada presensi.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Logbook Terakhir --}}
<div class="table-container">
    <div class="card-header">
        <div class="card-title">Logbook Terakhir</div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Deskripsi Tugas</th>
                    <th>Tanggal</th>
                    <th style="text-align:center;">Capaian</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentLogbook as $l)
                    <tr>
                        <td style="font-weight:500; color:#0F172A;">{{ $l->magang->mahasiswa->nama ?? '-' }}</td>
                        <td style="max-width:280px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:260px;">{{ $l->deskripsi_tugas }}</div>
                        </td>
                        <td style="color:#64748B; white-space:nowrap;">{{ $l->tanggal->translatedFormat('d M Y') }}</td>
                        <td style="text-align:center;">
                            <span class="badge {{ $l->persentase_capaian >= 100 ? 'badge-success' : 'badge-info' }}">
                                {{ $l->persentase_capaian }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-state-desc">Belum ada entri logbook.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection