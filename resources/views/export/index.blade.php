@extends('layouts.app')

@section('page-title', 'Export Dokumen')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.show', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $magang->mahasiswa->nama }}
                </a>
            </div>
            <h1 class="page-title">Export Dokumen</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(360px, 1fr)); gap:24px; max-width:880px;">
    {{-- Logbook Document Card --}}
    <div class="card" style="padding:24px; display:flex; flex-direction:column;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:14px;">
            <div style="width:44px; height:44px; border-radius:10px; background:#F0FDF4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:16px; font-weight:700; color:#0F172A;">Logbook Kegiatan Magang</div>
                <div style="font-size:12px; color:#64748B; margin-top:2px;">Lampiran 1: Identitas, tabel mingguan, capaian & TTD</div>
            </div>
        </div>

        <p style="font-size:13.5px; color:#64748B; line-height:1.5; margin-bottom:24px; flex:1;">
            Dokumen resmi logbook berisi identitas mahasiswa, pembagian kegiatan per minggu, target capaian, kendala, dan lembar pengesahan tanda tangan.
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
            <a href="{{ route('preview.logbook', $magang) }}" target="_blank" class="btn btn-secondary btn-sm" style="height:38px; font-size:13px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview
            </a>
            <a href="{{ route('export.logbook.word', $magang) }}" class="btn btn-primary btn-sm" style="height:38px; font-size:13px; background:#0284C7; border-color:#0284C7;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Word (.docx)
            </a>
            <a href="{{ route('export.logbook.pdf', $magang) }}" class="btn btn-danger btn-sm" style="height:38px; font-size:13px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                PDF
            </a>
        </div>
    </div>

    {{-- Attendance Document Card --}}
    <div class="card" style="padding:24px; display:flex; flex-direction:column;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:14px;">
            <div style="width:44px; height:44px; border-radius:10px; background:#EFF6FF; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#2563EB">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:16px; font-weight:700; color:#0F172A;">Daftar Hadir / Presensi</div>
                <div style="font-size:12px; color:#64748B; margin-top:2px;">Lampiran 2: Tabel absensi harian per minggu & TTD</div>
            </div>
        </div>

        <p style="font-size:13.5px; color:#64748B; line-height:1.5; margin-bottom:24px; flex:1;">
            Dokumen resmi daftar presensi berisi tanggal, jam masuk, jam pulang, paraf kehadiran harian, serta kolom pengesahan pembimbing dan supervisor.
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
            <a href="{{ route('preview.presensi', $magang) }}" target="_blank" class="btn btn-secondary btn-sm" style="height:38px; font-size:13px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview
            </a>
            <a href="{{ route('export.presensi.word', $magang) }}" class="btn btn-primary btn-sm" style="height:38px; font-size:13px; background:#0284C7; border-color:#0284C7;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Word (.docx)
            </a>
            <a href="{{ route('export.presensi.pdf', $magang) }}" class="btn btn-danger btn-sm" style="height:38px; font-size:13px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                PDF
            </a>
        </div>
    </div>
</div>
@endsection