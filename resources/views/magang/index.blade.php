@extends('layouts.app')

@section('page-title', 'Data Magang')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <h1 class="page-title">Data Magang</h1>
            <p class="page-subtitle">Kelola informasi kegiatan magang mahasiswa dan alokasi tempat magang</p>
        </div>
        <a href="{{ route('magang.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Magang
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <form method="GET" action="{{ route('magang.index') }}" class="table-search" role="search">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
            </svg>
            <input name="q" value="{{ $search }}" placeholder="Cari mahasiswa, instansi, topik..." aria-label="Cari data magang">
            @if ($search !== '')
                <a href="{{ route('magang.index') }}" class="search-clear">Reset</a>
            @endif
        </form>
        <span class="table-count">{{ $magang->total() }} kegiatan</span>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul / Topik</th>
                    <th>Instansi</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($magang as $m)
                    @php
                        $isActive = $m->tanggal_mulai <= today() && $m->tanggal_selesai >= today();
                        $isSelesai = $m->tanggal_selesai < today();
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:50%; background:#EFF6FF; color:#2563EB; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; border:1px solid #BFDBFE;">
                                    {{ mb_strimwidth($m->mahasiswa->nama, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:#0F172A; font-size:13.5px;">{{ $m->mahasiswa->nama }}</div>
                                    <div style="font-size:11.5px; color:#2563EB; font-family:monospace; font-weight:600;">{{ $m->mahasiswa->nim }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="max-width:220px;">
                            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; color:#0F172A; font-weight:500;">{{ $m->judul_topik }}</div>
                        </td>
                        <td style="color:#475569; font-weight:500;">{{ $m->instansi }}</td>
                        <td style="white-space:nowrap; color:#64748B; font-size:13px;">
                            <span style="color:#0F172A; font-weight:500;">{{ $m->tanggal_mulai->format('d M Y') }}</span><br>
                            <span style="color:#94A3B8; font-size:12px;">s.d. {{ $m->tanggal_selesai->format('d M Y') }}</span>
                        </td>
                        <td>
                            @if ($isActive)
                                <span class="badge badge-success">Berjalan</span>
                            @elseif ($isSelesai)
                                <span class="badge badge-gray">Selesai</span>
                            @else
                                <span class="badge badge-info">Akan Datang</span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('magang.show', $m) }}" class="action-link action-link-view" style="margin-right:12px;">Detail</a>
                            <a href="{{ route('magang.edit', $m) }}" class="action-link action-link-edit" style="margin-right:12px;">Edit</a>
                            <form action="{{ route('magang.destroy', $m) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus data magang {{ addslashes($m->mahasiswa->nama) }} berikut presensi dan logbook?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link action-link-delete" style="background:none; border:none; padding:0; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">{{ $search !== '' ? 'Data magang tidak ditemukan' : 'Belum ada data magang' }}</div>
                                <div class="empty-state-desc">{{ $search !== '' ? 'Coba gunakan kata kunci pencarian lain.' : 'Tambahkan kegiatan magang pertama untuk mulai.' }}</div>
                                @if ($search === '')
                                    <a href="{{ route('magang.create') }}" class="btn btn-primary">+ Tambah Magang</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($magang->hasPages())
    <div style="margin-top:16px; display:flex; justify-content:flex-end;">
        {{ $magang->links() }}
    </div>
@endif
@endsection
