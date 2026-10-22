@extends('layouts.app')

@section('page-title', 'Data Mahasiswa')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <h1 class="page-title">Data Mahasiswa</h1>
            <p class="page-subtitle">Kelola identitas mahasiswa yang digunakan di seluruh dokumen resmi</p>
        </div>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Mahasiswa
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <form method="GET" action="{{ route('mahasiswa.index') }}" class="table-search" role="search">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
            </svg>
            <input name="q" value="{{ $search }}" placeholder="Cari nama, NIM, prodi..." aria-label="Cari mahasiswa">
            @if ($search !== '')
                <a href="{{ route('mahasiswa.index') }}" class="search-clear">Reset</a>
            @endif
        </form>
        <span class="table-count">{{ $mahasiswa->total() }} mahasiswa</span>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Mahasiswa</th>
                    <th>Program Studi</th>
                    <th>Jurusan</th>
                    <th>Perguruan Tinggi</th>
                    <th style="text-align:center;">Magang</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mahasiswa as $m)
                    <tr>
                        <td>
                            <span style="font-family:monospace; font-size:12.5px; color:#2563EB; font-weight:700; background:#EFF6FF; padding:3px 8px; border-radius:6px; border:1px solid #BFDBFE;">{{ $m->nim }}</span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:#EFF6FF; color:#2563EB; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; border:1px solid #BFDBFE;">
                                    {{ mb_strimwidth($m->nama, 0, 1) }}
                                </div>
                                <div style="font-weight:600; color:#0F172A; font-size:13.5px;">{{ $m->nama }}</div>
                            </div>
                        </td>
                        <td style="color:#374151;">{{ $m->program_studi }}</td>
                        <td style="color:#64748B;">{{ $m->jurusan }}</td>
                        <td style="color:#64748B;">{{ $m->perguruan_tinggi }}</td>
                        <td style="text-align:center;">
                            <span class="badge {{ $m->magang_count > 0 ? 'badge-info' : 'badge-gray' }}">{{ $m->magang_count }} kegiatan</span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('mahasiswa.show', $m) }}" class="action-link action-link-view" style="margin-right:12px;">Lihat</a>
                            <a href="{{ route('mahasiswa.edit', $m) }}" class="action-link action-link-edit" style="margin-right:12px;">Edit</a>
                            <form action="{{ route('mahasiswa.destroy', $m) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus data mahasiswa {{ addslashes($m->nama) }}?')">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">{{ $search !== '' ? 'Mahasiswa tidak ditemukan' : 'Belum ada data mahasiswa' }}</div>
                                <div class="empty-state-desc">{{ $search !== '' ? 'Coba gunakan kata kunci pencarian lain.' : 'Tambahkan data mahasiswa pertama untuk memulai.' }}</div>
                                @if ($search === '')
                                    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">+ Tambah Mahasiswa</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($mahasiswa->hasPages())
    <div style="margin-top:16px; display:flex; justify-content:flex-end;">
        {{ $mahasiswa->links() }}
    </div>
@endif
@endsection
