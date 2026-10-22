@extends('layouts.app')

@section('page-title', 'Pengaturan Kop Surat')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <h1 class="page-title">Pengaturan Kop Surat</h1>
            <p class="page-subtitle">Kelola kop kampus/program studi dan instansi mitra untuk tata letak dokumen resmi</p>
        </div>
        <a href="{{ route('kop-surat.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kop Surat
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(380px, 1fr)); gap:24px;">
    @forelse ($kopSurats as $k)
        <div class="card" style="display:flex; flex-direction:column; overflow:hidden;">
            <div class="card-header" style="background:#F8FAFC; border-bottom:1px solid #E2E8F0;">
                <span class="badge {{ $k->jenis === 'kampus' ? 'badge-info' : 'badge-amber' }}">
                    {{ $k->jenis === 'kampus' ? 'Kampus / Prodi' : 'Instansi Mitra' }}
                </span>
                <div style="display:flex; gap:12px; align-items:center;">
                    <a href="{{ route('kop-surat.edit', $k) }}" class="action-link action-link-edit">Edit</a>
                    <form action="{{ route('kop-surat.destroy', $k) }}" method="POST" class="inline"
                          onsubmit="event.preventDefault(); confirmDelete(this, 'Hapus kop surat {{ addslashes($k->nama_instansi) }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link action-link-delete" style="background:none; border:none; padding:0; cursor:pointer;">Hapus</button>
                    </form>
                </div>
            </div>
            
            {{-- DOCUMENT PREVIEW BOX --}}
            <div style="padding:20px; background:#FFFFFF; flex:1; display:flex; flex-direction:column; gap:16px;">
                <div style="border:1px solid #E2E8F0; border-radius:10px; padding:18px 16px; background:#F8FAFC; box-shadow:inset 0 1px 2px rgba(0,0,0,0.02);">
                    <div style="font-size:10px; font-weight:800; color:#94A3B8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:12px;">PREVIEW KOP DOKUMEN</div>
                    <div style="display:flex; align-items:center; gap:16px; border-bottom:2px solid #0F172A; padding-bottom:12px;">
                        @if ($k->logo_path)
                            <img src="{{ asset('storage/'.$k->logo_path) }}" alt="Logo"
                                 style="height:48px; max-width:80px; object-fit:contain; flex-shrink:0;"
                                 onerror="this.style.display='none';">
                        @else
                            <div style="width:44px; height:44px; border-radius:8px; background:#E2E8F0; display:flex; align-items:center; justify-content:center; color:#64748B; font-size:10px; font-weight:800; flex-shrink:0;">
                                KOP
                            </div>
                        @endif
                        <div style="min-width:0; flex:1; text-align:center;">
                            <div style="font-size:13px; font-weight:800; color:#0F172A; text-transform:uppercase; line-height:1.2;">
                                {{ $k->nama_instansi }}
                            </div>
                            @if ($k->alamat)
                                <div style="font-size:10.5px; color:#475569; margin-top:3px; line-height:1.3;">
                                    {{ $k->alamat }}
                                </div>
                            @endif
                            @if ($k->telepon || $k->website)
                                <div style="font-size:10px; color:#64748B; margin-top:2px;">
                                    {{ implode(' · ', array_filter([$k->telepon ? 'Telp: '.$k->telepon : null, $k->website])) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; gap:6px; font-size:12.5px; color:#64748B; margin-top:auto;">
                    @if ($k->alamat)
                        <div style="display:flex; gap:8px; align-items:flex-start;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#94A3B8" style="flex-shrink:0; margin-top:2px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $k->alamat }}</span>
                        </div>
                    @endif
                    @if ($k->telepon)
                        <div style="display:flex; gap:8px; align-items:center;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#94A3B8" style="flex-shrink:0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $k->telepon }}</span>
                        </div>
                    @endif
                    @if ($k->website)
                        <div style="display:flex; gap:8px; align-items:center;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#94A3B8" style="flex-shrink:0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <span>{{ $k->website }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column: 1 / -1; padding:20px;">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum ada data kop surat</div>
                <div class="empty-state-desc">Tambahkan kop surat kampus atau instansi mitra untuk tata letak cetak dokumen resmi.</div>
                <a href="{{ route('kop-surat.create') }}" class="btn btn-primary">+ Tambah Kop Surat</a>
            </div>
        </div>
    @endforelse
</div>
@endsection