@extends('layouts.app')

@section('page-title', 'Kegiatan / Task')

@section('content')
@php
    use App\Support\Percent;
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
            <h1 class="page-title">Kegiatan / Task</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('progress', ['magang' => $magang->id]) }}" class="btn btn-secondary">
                Progress Magang
            </a>
            <a href="{{ route('magang.task.create', $magang) }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kegiatan
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
    </div>
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap; font-size:13px; color:#64748B;">
        <span>Progress: <span style="font-weight:700; color:#2563EB;">{{ Percent::format($progress['overall']) }}%</span></span>
        <span>Bobot: <span style="font-weight:600;">{{ Percent::format($progress['total_bobot']) }}%</span></span>
    </div>
</div>

@if (!$progress['bobot_penuh'])
    <div class="alert alert-warning" role="alert">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0; margin-top:1px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>Total bobot seluruh kegiatan baru <strong>{{ Percent::format($progress['total_bobot']) }}%</strong>. Sebaiknya lengkapi hingga mencapai 100% agar progress keseluruhan akurat.</div>
    </div>
@endif

<div class="table-container">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px; text-align:center;">No</th>
                    <th>Kegiatan</th>
                    <th style="white-space:nowrap;">Periode</th>
                    <th style="text-align:center;">Bobot</th>
                    <th style="text-align:center;">Progress</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Logbook</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $i => $task)
                    <tr>
                        <td style="text-align:center; color:#94A3B8; font-size:12px;">{{ $i + 1 }}</td>
                        <td>
                            <a href="{{ route('magang.task.show', [$magang, $task]) }}" class="action-link action-link-view" style="font-size:13px;">
                                {{ $task->nama_kegiatan }}
                            </a>
                            @if ($task->deskripsi)
                                <div style="font-size:12px; color:#94A3B8; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $task->deskripsi }}</div>
                            @endif
                        </td>
                        <td style="color:#64748B; font-size:13px; white-space:nowrap;">
                            @if ($task->tanggal_mulai)
                                {{ $task->tanggal_mulai->format('d/m/Y') }} – {{ $task->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                            @else
                                <span style="color:#94A3B8;">-</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="badge badge-gray">{{ $task->bobot }}%</span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:4px; min-width:72px;">
                                <span class="badge {{ $task->progress >= 100 ? 'badge-success' : ($task->progress > 0 ? 'badge-info' : 'badge-gray') }}">
                                    {{ Percent::format($task->progress) }}%
                                </span>
                                <div class="progress-bar" style="width:60px;">
                                    <div class="progress-fill" style="width:{{ min($task->progress, 100) }}%; background:{{ $task->progress >= 100 ? '#16A34A' : ($task->progress > 0 ? '#2563EB' : '#CBD5E1') }};"></div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge {{ $task->status === 'selesai' ? 'badge-success' : ($task->status === 'berjalan' ? 'badge-info' : 'badge-gray') }}">{{ $task->status_label }}</span>
                        </td>
                        <td style="text-align:center; color:#64748B;">{{ $task->logbooks_count }}</td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('magang.task.show', [$magang, $task]) }}" class="action-link action-link-view" style="margin-right:10px;">Detail</a>
                            <a href="{{ route('magang.task.edit', [$magang, $task]) }}" class="action-link action-link-edit" style="margin-right:10px;">Edit</a>
                            <form action="{{ route('magang.task.destroy', [$magang, $task]) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus kegiatan \"{{ $task->nama_kegiatan }}\"? Logbook yang terkait akan dipertahankan.')">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">Belum ada kegiatan/task</div>
                                <div class="empty-state-desc">Buat kegiatan sebagai induk dari logbook harian untuk menghitung progress berbobot.</div>
                                <a href="{{ route('magang.task.create', $magang) }}" class="btn btn-primary">Tambah Kegiatan</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection