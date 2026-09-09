@extends('layouts.app')

@section('page-title', 'Presensi Magang')

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
            <h1 class="page-title">Presensi Magang</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('preview.presensi', $magang) }}" class="btn btn-secondary" target="_blank">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview
            </a>
            <a href="{{ route('magang.presensi.create', $magang) }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Presensi
            </a>
        </div>
    </div>
</div>

{{-- Info Bar --}}
<div class="card" style="padding:14px 20px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:6px; font-size:13.5px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#64748B">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span style="color:#64748B;">Periode:</span>
        <span style="font-weight:600; color:#0F172A;">{{ $magang->tanggal_mulai->format('d M Y') }} – {{ $magang->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div style="font-size:13.5px; color:#64748B;">
        <span style="font-weight:600; color:#2563EB;">{{ $presensi->count() }}</span> hari tercatat dari <span style="font-weight:600;">{{ $jumlahMinggu }}</span> minggu
    </div>
</div>

@foreach ($presensiByWeek as $week)
    <div class="table-container" style="margin-bottom:16px;">
        <div class="card-header" style="background:#F8FAFC;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="week-badge">{{ $week['minggu_ke'] }}</span>
                <span style="font-size:13.5px; font-weight:600; color:#0F172A;">Minggu Ke-{{ $week['minggu_ke'] }}</span>
            </div>
            <span style="font-size:12px; color:#94A3B8;">{{ count($week['items']) }} entri</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:48px; text-align:center;">No</th>
                        <th>Tanggal</th>
                        <th style="text-align:center;">Jam Masuk</th>
                        <th style="text-align:center;">Jam Pulang</th>
                        <th>Keterangan</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($week['items'] as $i => $p)
                        <tr>
                            <td style="text-align:center; color:#94A3B8; font-size:12px;">{{ $i + 1 }}</td>
                            <td style="font-weight:500; color:#0F172A; white-space:nowrap;">{{ $p->tanggal->translatedFormat('l, d/m/Y') }}</td>
                            <td style="text-align:center;">
                                @if ($p->jam_masuk)
                                    <span class="badge badge-info">{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}</span>
                                @else
                                    <span style="color:#CBD5E1;">-</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if ($p->jam_pulang)
                                    <span class="badge badge-gray">{{ \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') }}</span>
                                @else
                                    <span style="color:#CBD5E1;">-</span>
                                @endif
                            </td>
                            <td style="color:#64748B; font-size:13px;">{{ $p->keterangan ?? '-' }}</td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('presensi.edit', $p) }}" class="action-link action-link-edit" style="margin-right:12px;">Edit</a>
                                <form action="{{ route('presensi.destroy', $p) }}" method="POST" class="inline"
                                      onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus presensi tanggal {{ $p->tanggal->format('d M Y') }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-link action-link-delete" style="background:none; border:none; padding:0; cursor:pointer;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

@if ($presensiByWeek->isEmpty())
    <div class="table-container">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="empty-state-title">Belum ada data presensi</div>
            <div class="empty-state-desc">Mulai input kehadiran harian untuk magang ini.</div>
            <a href="{{ route('magang.presensi.create', $magang) }}" class="btn btn-primary">Mulai Input Presensi</a>
        </div>
    </div>
@endif
@endsection