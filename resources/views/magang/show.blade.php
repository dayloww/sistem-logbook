@extends('layouts.app')

@section('page-title', $magang->mahasiswa->nama)

@section('content')
@php
    $isActive = $magang->tanggal_mulai <= today() && $magang->tanggal_selesai >= today();
    $isSelesai = $magang->tanggal_selesai < today();
@endphp

<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.index') }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Data Magang
                </a>
            </div>
            <h1 class="page-title">{{ $magang->mahasiswa->nama }}</h1>
            <p class="page-subtitle">{{ $magang->judul_topik }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('magang.export.index', $magang) }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Dokumen
            </a>
            <a href="{{ route('magang.edit', $magang) }}" class="btn btn-secondary">Edit</a>
        </div>
    </div>
</div>

{{-- STAT CARDS --}}
<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:20px;">
    <div class="stat-card stagger-1">
        <div class="stat-card-icon" style="background:#EFF6FF; margin-bottom:10px;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563EB">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-card-value" style="font-size:24px;">{{ $presensiStats['total'] }}</div>
        <div class="stat-card-label">Total Presensi</div>
        <div class="stat-card-sub">hari tercatat</div>
    </div>
    <div class="stat-card stagger-2">
        <div class="stat-card-icon" style="background:#F0FDF4; margin-bottom:10px;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="stat-card-value" style="font-size:24px;">{{ $presensiStats['jam'] }}</div>
        <div class="stat-card-label">Presensi Lengkap</div>
        <div class="stat-card-sub">jam masuk & pulang</div>
    </div>
    <div class="stat-card stagger-3">
        <div class="stat-card-icon" style="background:#FEF3C7; margin-bottom:10px;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#D97706">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="stat-card-value" style="font-size:24px;">{{ $logbooksStats['total'] }}</div>
        <div class="stat-card-label">Total Logbook</div>
        <div class="stat-card-sub">entri kegiatan</div>
    </div>
    <div class="stat-card stagger-4">
        <div class="stat-card-icon" style="background:#F3E8FF; margin-bottom:10px;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#7C3AED">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div class="stat-card-value" style="font-size:24px;">{{ $logbooksStats['rata_capaian'] }}%</div>
        <div class="stat-card-label">Rata-rata Capaian</div>
        <div class="stat-card-sub">dari seluruh entri</div>
    </div>
</div>

{{-- INFO BAR --}}
<div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:20px;">
    <div class="card" style="padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:13px; color:#64748B;">Status</span>
        @if ($isActive)
            <span class="badge badge-success">Berjalan</span>
        @elseif ($isSelesai)
            <span class="badge badge-gray">Selesai</span>
        @else
            <span class="badge badge-info">Akan Datang</span>
        @endif
    </div>
    <div class="card" style="padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:13px; color:#64748B;">Lama Kegiatan</span>
        <span style="font-size:13.5px; font-weight:600; color:#0F172A;">{{ $magang->lama_kegiatan ?? $jumlahMinggu.' minggu' }}</span>
    </div>
    <div class="card" style="padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:13px; color:#64748B;">Total Jam / SKS</span>
        <span style="font-size:13.5px; font-weight:600; color:#0F172A;">{{ $magang->total_jam ?? '-' }} jam / {{ $magang->jumlah_sks ?? '-' }} SKS</span>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 280px; gap:20px; margin-bottom:20px; align-items:start;">
    {{-- Data Detail --}}
    <div class="card" style="padding:0; overflow:hidden;">
        <div class="card-header">
            <div>
                <div class="card-title">Data Mahasiswa & Magang</div>
                <div style="font-size:12px; color:#94A3B8; margin-top:2px;">{{ $magang->tanggal_mulai->format('d M Y') }} – {{ $magang->tanggal_selesai->format('d M Y') }} ({{ $magang->jumlahMinggu() }} minggu)</div>
            </div>
        </div>
        <div style="padding:20px;">
            <dl style="display:grid; grid-template-columns:1fr 1fr; gap:18px 24px;">
                <div class="info-row"><dt>NIM</dt><dd>{{ $magang->mahasiswa->nim }}</dd></div>
                <div class="info-row"><dt>Program Studi</dt><dd>{{ $magang->mahasiswa->program_studi }}</dd></div>
                <div class="info-row"><dt>Jurusan</dt><dd>{{ $magang->mahasiswa->jurusan }}</dd></div>
                <div class="info-row"><dt>Perguruan Tinggi</dt><dd>{{ $magang->mahasiswa->perguruan_tinggi }}</dd></div>
                <div class="info-row"><dt>Lokasi</dt><dd>{{ $magang->lokasi }}</dd></div>
                <div class="info-row"><dt>Bagian/Unit</dt><dd>{{ $magang->bagian_unit ?? '-' }}</dd></div>
                <div class="info-row"><dt>Dosen Pembimbing I</dt><dd>{{ $magang->dosen_pembimbing_1 }}</dd></div>
                <div class="info-row"><dt>Dosen Pembimbing II</dt><dd>{{ $magang->dosen_pembimbing_2 ?? '-' }}</dd></div>
                <div class="info-row"><dt>Supervisor/Mentor</dt><dd>{{ $magang->supervisor }} {{ $magang->jabatan_supervisor ? '('.$magang->jabatan_supervisor.')' : '' }}</dd></div>
                <div class="info-row"><dt>Kop Surat</dt><dd>{{ $magang->kopSurat?->nama_instansi ?? 'Default' }}</dd></div>
            </dl>
        </div>
    </div>

    {{-- Menu Modul --}}
    <div class="card" style="padding:0; overflow:hidden;">
        <div class="card-header">
            <div class="card-title">Menu Modul</div>
        </div>
        <div style="padding:14px; display:flex; flex-direction:column; gap:8px;">
            <a href="{{ route('magang.presensi.index', $magang) }}" class="module-card">
                <div class="module-card-icon" style="background:#F0FDF4;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:#0F172A;">Presensi</div>
                    <div style="font-size:12px; color:#64748B;">Input kehadiran harian</div>
                </div>
            </a>
            <a href="{{ route('magang.logbook.index', $magang) }}" class="module-card">
                <div class="module-card-icon" style="background:#FEF3C7;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D97706">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:#0F172A;">Logbook</div>
                    <div style="font-size:12px; color:#64748B;">Input kegiatan harian</div>
                </div>
            </a>
            <a href="{{ route('progress', ['magang' => $magang->id]) }}" class="module-card">
                <div class="module-card-icon" style="background:#EFF6FF;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:#0F172A;">Progress Magang</div>
                    <div style="font-size:12px; color:#64748B;">Progress berbobot & grafik mingguan</div>
                </div>
            </a>
            <a href="{{ route('magang.catatan.index', $magang) }}" class="module-card">
                <div class="module-card-icon" style="background:#EFF6FF;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:#0F172A;">Catatan Mingguan</div>
                    <div style="font-size:12px; color:#64748B;">Feedback per minggu</div>
                </div>
            </a>
            <a href="{{ route('magang.export.index', $magang) }}" class="module-card">
                <div class="module-card-icon" style="background:#F3E8FF;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#7C3AED">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:#0F172A;">Export Dokumen</div>
                    <div style="font-size:12px; color:#64748B;">Generate Word & PDF</div>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Recent Presensi & Logbook --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
    <div class="table-container">
        <div class="card-header">
            <div class="card-title">Presensi Terakhir</div>
            <a href="{{ route('magang.presensi.index', $magang) }}" class="btn btn-ghost btn-sm" style="font-size:12px;">Lihat semua →</a>
        </div>
        @forelse ($magang->presensi->sortByDesc('tanggal')->take(5) as $p)
            <div style="display:flex; justify-content:space-between; padding:11px 20px; border-bottom:1px solid #F1F5F9;">
                <div style="font-size:13px; color:#374151;">{{ $p->tanggal->translatedFormat('l, d M Y') }}</div>
                <div style="font-size:13px; color:#64748B; white-space:nowrap;">{{ $p->jam_masuk ?? '--:--' }} – {{ $p->jam_pulang ?? '--:--' }}</div>
            </div>
        @empty
            <div class="empty-state" style="padding:32px 16px;">
                <div class="empty-state-desc">Belum ada presensi.</div>
            </div>
        @endforelse
    </div>

    <div class="table-container">
        <div class="card-header">
            <div class="card-title">Logbook Terakhir</div>
            <a href="{{ route('magang.logbook.index', $magang) }}" class="btn btn-ghost btn-sm" style="font-size:12px;">Lihat semua →</a>
        </div>
        @forelse ($magang->logbooks->sortByDesc('tanggal')->take(5) as $l)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:11px 20px; border-bottom:1px solid #F1F5F9; gap:10px;">
                <div style="font-size:13px; color:#374151; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70%;">{{ $l->deskripsi_tugas }}</div>
                <span class="badge {{ $l->persentase_capaian >= 100 ? 'badge-success' : 'badge-info' }}" style="flex-shrink:0;">{{ $l->persentase_capaian }}%</span>
            </div>
        @empty
            <div class="empty-state" style="padding:32px 16px;">
                <div class="empty-state-desc">Belum ada entri logbook.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection