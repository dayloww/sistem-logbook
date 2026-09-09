@extends('layouts.app')

@section('page-title', $task->nama_kegiatan)

@section('content')
@php
    use App\Support\Percent;
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
            <h1 class="page-title">{{ $task->nama_kegiatan }}</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('magang.logbook.create', ['magang' => $magang, 'task_id' => $task->id]) }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Logbook
            </a>
            <a href="{{ route('magang.task.edit', [$magang, $task]) }}" class="btn btn-secondary">Edit Kegiatan</a>
        </div>
    </div>
</div>

{{-- PROGRESS CARD --}}
<div class="card" style="padding:20px; margin-bottom:20px;">
    <div style="display:grid; grid-template-columns:auto 1fr; gap:24px; align-items:center;" class="sm:grid-cols-1">
        <div style="text-align:center;">
            <div style="font-size:42px; font-weight:700; color:{{ $task->progress >= 100 ? '#16A34A' : '#2563EB' }}; line-height:1;">{{ Percent::format($task->progress) }}%</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:4px;">Progress kegiatan</div>
        </div>
        <div style="min-width:0;">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px;">
                <span class="badge {{ $task->status === 'selesai' ? 'badge-success' : ($task->status === 'berjalan' ? 'badge-info' : 'badge-gray') }}" style="font-size:12px; padding:4px 12px;">
                    {{ $task->status_label }}
                </span>
                <span style="font-size:13px; color:#64748B;">Bobot: <strong style="color:#0F172A;">{{ $task->bobot }}%</strong></span>
            </div>
            <div class="progress-bar" style="width:100%; height:10px;">
                <div class="progress-fill" style="width:{{ min($task->progress, 100) }}%; background:{{ $task->progress >= 100 ? '#16A34A' : ($task->progress > 0 ? '#2563EB' : '#CBD5E1') }};"></div>
            </div>
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-top:14px;" class="sm:grid-cols-1">
                <div>
                    <div style="font-size:11px; color:#94A3B8; font-weight:600; text-transform:uppercase;">Periode</div>
                    <div style="font-size:13px; color:#0F172A; font-weight:500; margin-top:2px;">
                        {{ $task->tanggal_mulai?->format('d M Y') ?? '-' }} – {{ $task->tanggal_selesai?->format('d M Y') ?? '-' }}
                    </div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; font-weight:600; text-transform:uppercase;">Total Logbook</div>
                    <div style="font-size:13px; color:#0F172A; font-weight:500; margin-top:2px;">{{ $logbooks->count() }} entri</div>
                </div>
                <div>
                    <div style="font-size:11px; color:#94A3B8; font-weight:600; text-transform:uppercase;">Kontribusi</div>
                    <div style="font-size:13px; color:#0F172A; font-weight:500; margin-top:2px;">{{ Percent::format(($task->bobot / 100) * $task->progress) }}% dari keseluruhan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DESCRIPTION --}}
@if ($task->deskripsi)
    <div class="card" style="padding:16px 20px; margin-bottom:20px;">
        <div style="font-size:11px; color:#94A3B8; font-weight:600; text-transform:uppercase; margin-bottom:6px;">Deskripsi</div>
        <div style="font-size:13.5px; color:#374151; line-height:1.7;">{{ $task->deskripsi }}</div>
    </div>
@endif

{{-- RIWAYAT PROGRESS --}}
<div class="table-container">
    <div class="card-header">
        <div>
            <div class="card-title">Riwayat Progress Harian</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Perkembangan kegiatan dari hari ke hari</div>
        </div>
        <a href="{{ route('magang.logbook.index', $magang) }}" class="btn btn-ghost btn-sm" style="font-size:12px;">Lihat semua logbook →</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px; text-align:center;">No</th>
                    <th style="white-space:nowrap;">Tanggal</th>
                    <th>Deskripsi Tugas</th>
                    <th>Target</th>
                    <th style="text-align:center;">Progress Hari</th>
                    <th>Kendala</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logbooks as $i => $l)
                    <tr>
                        <td style="text-align:center; color:#94A3B8; font-size:12px;">{{ $i + 1 }}</td>
                        <td style="white-space:nowrap; color:#374151; font-size:13px;">{{ $l->tanggal->translatedFormat('l, d M Y') }}</td>
                        <td style="max-width:220px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;">{{ $l->deskripsi_tugas }}</div>
                        </td>
                        <td style="color:#64748B; font-size:13px; max-width:140px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:130px;">{{ $l->target ?? '-' }}</div>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:4px; min-width:72px;">
                                <span class="badge {{ $l->persentase_capaian >= 100 ? 'badge-success' : ($l->persentase_capaian >= 50 ? 'badge-info' : 'badge-warning') }}">
                                    {{ $l->persentase_capaian }}%
                                </span>
                                <div class="progress-bar" style="width:60px;">
                                    <div class="progress-fill" style="width:{{ min($l->persentase_capaian, 100) }}%; background:{{ $l->persentase_capaian >= 100 ? '#16A34A' : '#2563EB' }};"></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:#64748B; font-size:13px; max-width:140px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:130px;">{{ $l->kendala ?? '-' }}</div>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('logbook.edit', $l) }}" class="action-link action-link-edit" style="margin-right:12px;">Edit</a>
                            <form action="{{ route('logbook.destroy', $l) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus entri logbook tanggal {{ $l->tanggal->format('d M Y') }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link action-link-delete" style="background:none; border:none; padding:0; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">Belum ada logbook untuk kegiatan ini</div>
                                <div class="empty-state-desc">Mulai catat kegiatan harian untuk melihat riwayat progress.</div>
                                <a href="{{ route('magang.logbook.create', ['magang' => $magang, 'task_id' => $task->id]) }}" class="btn btn-primary">Tambah Logbook</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection