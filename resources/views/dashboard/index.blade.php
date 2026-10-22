@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
@php
    $hasSelection = $selected !== null;
    $progressPercent = $progress['percent'] ?? 0;
    $completedDays = $progress['completed_days'] ?? 0;
    $targetDays = $progress['target_days'] ?? 0;
    $remainingDays = $progress['remaining_days'] ?? 0;
    $status = $hasSelection ? $displayStatus : ['key' => 'none', 'label' => 'Belum ada data'];
@endphp

<div class="dash">

    {{-- 1. PAGE HEADER --}}
    <section class="dash-welcome">
        <div>
            <p class="eyebrow">OVERVIEW SISTEM</p>
            <h1>Selamat datang kembali <span aria-hidden="true">👋</span></h1>
            <p>Berikut ringkasan aktivitas magang dan perkembangan logbook hari ini.</p>
        </div>
        <time datetime="{{ now()->toDateString() }}" class="dash-date">{{ now()->translatedFormat('l, d F Y') }}</time>
    </section>

    {{-- 2. INTERNSHIP OVERVIEW / HERO --}}
    <section class="dash-hero">
        @if ($hasSelection)
            <div class="dash-hero-copy">
                <p class="eyebrow">MAGANG AKTIF</p>
                <h2>{{ $selected->mahasiswa->nama }}</h2>
                <p class="dash-hero-place">{{ $selected->instansi }}</p>
                @if ($selected->judul_topik)
                    <p class="dash-hero-topic">{{ $selected->judul_topik }}</p>
                @endif
                <div class="dash-hero-period">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $selected->tanggal_mulai->translatedFormat('d M Y') }} — {{ $selected->tanggal_selesai->translatedFormat('d M Y') }}</span>
                </div>
                @if ($magangList->count() > 1)
                    <form method="GET" action="{{ route('dashboard') }}" class="dash-switcher">
                        <label for="dashboard-magang">Lihat magang lain</label>
                        <select id="dashboard-magang" name="magang" onchange="this.form.submit()">
                            @foreach ($magangList as $m)
                                <option value="{{ $m->id }}" @selected($m->id === $selected->id)>
                                    {{ $m->mahasiswa->nama }} · {{ $m->instansi }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            <div class="dash-hero-progress">
                <div class="dash-hero-percent">{{ $progressPercent }}%</div>
                <div class="dash-hero-bar" aria-label="Progress logbook {{ $progressPercent }} persen">
                    <span style="width: {{ $progressPercent }}%;"></span>
                </div>
                <p class="dash-hero-days">{{ $completedDays }} dari {{ $targetDays }} hari <span class="dash-hero-days-sub">tercatat</span></p>
                <p class="dash-hero-remaining"><span>Sisa Aktivitas:</span> {{ $remainingDays }} hari tersisa</p>
                <span class="dash-status dash-status--{{ $status['key'] }}">
                    <span class="dash-status-dot" aria-hidden="true"></span>{{ $status['label'] }}
                </span>
                <a href="{{ route('progress', ['magang' => $selected->id]) }}" class="btn btn-primary btn-sm dash-hero-cta">
                    Lihat Progress <span aria-hidden="true">→</span>
                </a>
            </div>
        @else
            <div class="dash-hero-copy">
                <p class="eyebrow">MAGANG AKTIF</p>
                <h2>Belum ada data magang</h2>
                <p class="dash-hero-topic">Tambahkan data magang untuk melihat ringkasan perkembangan logbook.</p>
                <a href="{{ route('magang.create') }}" class="btn btn-primary">Tambah Data Magang</a>
            </div>
        @endif
    </section>

    {{-- 3. QUICK METRICS --}}
    <section class="dash-metrics" aria-label="Ringkasan data">
        <x-dashboard-metric-card
            href="{{ route('mahasiswa.index') }}"
            icon="users" accent="blue"
            label="Mahasiswa" value="{{ $stats['mahasiswa'] }}" caption="terdaftar" />
        <x-dashboard-metric-card
            href="{{ route('magang.index') }}"
            icon="briefcase" accent="violet"
            label="Magang" value="{{ $stats['magang'] }}" caption="kegiatan tercatat" />
        <x-dashboard-metric-card
            icon="check" accent="emerald"
            label="Presensi" value="{{ $stats['presensi'] }}" caption="kehadiran masuk" />
        <x-dashboard-metric-card
            icon="doc" accent="amber"
            label="Logbook" value="{{ $stats['logbook'] }}" caption="Total Aktivitas" />
    </section>

    {{-- 4. PROGRESS ANALYTICS + 5. RECENT ACTIVITIES --}}
    <section class="analytics-layout">
        <div class="analytics-chart">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">ANALITIK</p>
                    <h2>Progress Trend</h2>
                    <p class="section-sub">Perkembangan capaian logbook berdasarkan data existing.</p>
                </div>
                @if ($hasSelection)
                    <span class="dash-status dash-status--{{ $status['key'] }}">
                        <span class="dash-status-dot" aria-hidden="true"></span>{{ $status['label'] }}
                    </span>
                @endif
            </div>
            <x-dashboard-progress-trend :trend="$trend" :target="$targetDays" />
        </div>

        <aside class="activity-feed">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">TERKINI</p>
                    <h2>Aktivitas Terbaru</h2>
                </div>
            </div>
            <div class="dash-timeline">
                @forelse ($recentLogbook as $entry)
                    <a href="{{ route('magang.logbook.index', $entry->magang) }}" class="dash-timeline-item">
                        <span class="dash-timeline-dot {{ $entry->persentase_capaian >= 100 ? 'is-done' : '' }}" aria-hidden="true"></span>
                        <span class="dash-timeline-body">
                            <span class="dash-timeline-date">{{ $entry->tanggal->translatedFormat('l, d/m/Y') }}</span>
                            <span class="dash-timeline-text">{{ $entry->deskripsi_tugas }}</span>
                        </span>
                        <span class="badge {{ $entry->persentase_capaian >= 100 ? 'badge-success' : 'badge-info' }}">
                            {{ $entry->persentase_capaian }}%
                        </span>
                    </a>
                @empty
                    <div class="compact-empty">Belum ada aktivitas logbook.</div>
                @endforelse
            </div>
        </aside>
    </section>

    {{-- 6. PRESENSI TERBARU + 7. MAGANG AKTIF --}}
    <section class="dashboard-bottom">
        <div class="dash-card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">KEGIATAN BERJALAN</p>
                    <h2>Magang Aktif</h2>
                </div>
                <a href="{{ route('magang.index') }}">Semua data</a>
            </div>
            <div class="dash-intern-list">
                @forelse ($magangAktif as $m)
                    <a href="{{ route('magang.show', $m) }}" class="dash-intern-row">
                        <span class="initial-avatar">{{ mb_strimwidth($m->mahasiswa->nama, 0, 1) }}</span>
                        <span class="dash-intern-main">
                            <strong>{{ $m->mahasiswa->nama }}</strong>
                            <small>{{ $m->instansi }}</small>
                            <small class="dash-intern-period">{{ $m->tanggal_mulai->translatedFormat('d M Y') }} — {{ $m->tanggal_selesai->translatedFormat('d M Y') }}</small>
                        </span>
                        <span class="badge badge-success">
                            <span class="dash-dot" aria-hidden="true"></span>Berjalan
                        </span>
                    </a>
                @empty
                    <div class="compact-empty">Tidak ada magang yang sedang berjalan.</div>
                @endforelse
            </div>
        </div>

        <div class="dash-card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">KEHADIRAN</p>
                    <h2>Presensi Terbaru</h2>
                </div>
                @if ($hasSelection)
                    <a href="{{ route('magang.presensi.index', $selected) }}">Lihat semua →</a>
                @endif
            </div>
            <div class="dash-attendance">
                @forelse ($recentPresensi as $p)
                    <div class="dash-attendance-row">
                        <span class="dash-attendance-check" aria-hidden="true">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <span class="dash-attendance-date">{{ $p->tanggal->translatedFormat('d M Y') }}</span>
                        <span class="dash-attendance-time">{{ $p->jam_masuk ?? '--:--' }} <i>—</i> {{ $p->jam_pulang ?? '--:--' }}</span>
                    </div>
                @empty
                    <div class="compact-empty">Belum ada presensi.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection