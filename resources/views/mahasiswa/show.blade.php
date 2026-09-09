@extends('layouts.app')

@section('page-title', $mahasiswa->nama)

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('mahasiswa.index') }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Data Mahasiswa
                </a>
            </div>
            <h1 class="page-title">{{ $mahasiswa->nama }}</h1>
            <p class="page-subtitle">{{ $mahasiswa->nim }} · {{ $mahasiswa->program_studi }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('magang.create').'?mahasiswa_id='.$mahasiswa->id }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Data Magang
            </a>
            <a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-secondary">Edit</a>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:320px 1fr; gap:20px; align-items:start;">
    {{-- Identitas Card --}}
    <div class="card" style="padding:24px;">
        <div style="text-align:center; margin-bottom:20px;">
            <div style="width:64px; height:64px; border-radius:50%; background:#EFF6FF; color:#2563EB; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; margin:0 auto 12px;">
                {{ mb_strimwidth($mahasiswa->nama, 0, 1) }}
            </div>
            <div style="font-size:15px; font-weight:700; color:#0F172A;">{{ $mahasiswa->nama }}</div>
            <div style="font-size:13px; color:#64748B; margin-top:2px;">{{ $mahasiswa->nim }}</div>
        </div>
        <hr class="divider">
        <dl style="display:flex; flex-direction:column; gap:14px;">
            <div class="info-row">
                <dt>Program Studi</dt>
                <dd>{{ $mahasiswa->program_studi }}</dd>
            </div>
            <div class="info-row">
                <dt>Jurusan</dt>
                <dd>{{ $mahasiswa->jurusan }}</dd>
            </div>
            <div class="info-row">
                <dt>Perguruan Tinggi</dt>
                <dd>{{ $mahasiswa->perguruan_tinggi }}</dd>
            </div>
            @if ($mahasiswa->ttd_path)
                <div class="info-row">
                    <dt>Tanda Tangan</dt>
                    <dd>
                        <img src="{{ asset('storage/'.$mahasiswa->ttd_path) }}" alt="TTD {{ $mahasiswa->nama }}"
                             style="max-width:140px; height:56px; object-fit:contain; border:1px solid #E2E8F0; border-radius:6px; padding:4px; margin-top:4px;">
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Riwayat Magang --}}
    <div class="table-container">
        <div class="card-header">
            <div>
                <div class="card-title">Riwayat Magang</div>
                <div style="font-size:12px; color:#94A3B8; margin-top:2px;">{{ $magang->count() }} kegiatan</div>
            </div>
        </div>
        @forelse ($magang as $m)
            @php
                $isActive = $m->tanggal_mulai <= today() && $m->tanggal_selesai >= today();
                $isSelesai = $m->tanggal_selesai < today();
            @endphp
            <a href="{{ route('magang.show', $m) }}"
               style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; text-decoration:none; border-bottom:1px solid #F1F5F9; transition:background 120ms;"
               onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                <div style="min-width:0;">
                    <div style="font-size:13.5px; font-weight:600; color:#0F172A;">{{ $m->judul_topik }}</div>
                    <div style="font-size:12px; color:#64748B; margin-top:2px;">{{ $m->instansi }}, {{ $m->lokasi }}</div>
                    <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">
                        {{ ($m->presensi_count ?? 0) }} presensi · {{ ($m->logbooks_count ?? 0) }} logbook
                    </div>
                </div>
                <div style="text-align:right; flex-shrink:0; margin-left:16px;">
                    @if ($isActive)
                        <span class="badge badge-success">Berjalan</span>
                    @elseif ($isSelesai)
                        <span class="badge badge-gray">Selesai</span>
                    @else
                        <span class="badge badge-info">Akan Datang</span>
                    @endif
                    <div style="font-size:11px; color:#94A3B8; margin-top:4px;">
                        {{ $m->tanggal_mulai->format('d M Y') }}<br>{{ $m->tanggal_selesai->format('d M Y') }}
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum ada riwayat magang</div>
                <div class="empty-state-desc">Mahasiswa ini belum memiliki data kegiatan magang.</div>
                <a href="{{ route('magang.create').'?mahasiswa_id='.$mahasiswa->id }}" class="btn btn-primary">+ Buat Data Magang</a>
            </div>
        @endforelse
    </div>
</div>
@endsection