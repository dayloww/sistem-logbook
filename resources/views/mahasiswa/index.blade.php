@extends('layouts.app')

@section('page-title', 'Data Mahasiswa')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <h1 class="page-title">Data Mahasiswa</h1>
            <p class="page-subtitle">Kelola identitas mahasiswa yang digunakan di seluruh dokumen</p>
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
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
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
                            <span style="font-family:monospace; font-size:13px; color:#2563EB; font-weight:600;">{{ $m->nim }}</span>
                        </td>
                        <td style="font-weight:600; color:#0F172A;">{{ $m->nama }}</td>
                        <td style="color:#374151;">{{ $m->program_studi }}</td>
                        <td style="color:#64748B;">{{ $m->jurusan }}</td>
                        <td style="color:#64748B;">{{ $m->perguruan_tinggi }}</td>
                        <td style="text-align:center;">
                            <span class="badge {{ $m->magang_count > 0 ? 'badge-info' : 'badge-gray' }}">{{ $m->magang_count }}</span>
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
                                <div class="empty-state-title">Belum ada data mahasiswa</div>
                                <div class="empty-state-desc">Tambahkan mahasiswa pertama untuk memulai.</div>
                                <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">+ Tambah Mahasiswa</a>
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