@extends('layouts.app')

@section('page-title', 'Progress Magang')

@section('content')
@php
    $has = $selected !== null;
    $logbooks = $has ? $selected->logbooks()->orderByDesc('tanggal')->get() : collect();
    $totalLogbook = $logbooks->count();
    $rataCapaian = round($logbooks->avg('persentase_capaian') ?? 0);
    $jumlahMinggu = $has ? $selected->jumlahMinggu() : 0;
    $percent = $progress['percent'] ?? 0;
@endphp

@if (!$has)
    <section class="journey-empty">
        <p class="eyebrow">INTERNSHIP JOURNEY</p>
        <h1>Progress Magang</h1>
        <p>Belum ada data magang yang dapat ditampilkan sebagai perjalanan progress.</p>
        <a href="{{ route('magang.create') }}" class="btn btn-primary">Tambah Data Magang</a>
    </section>
@else
    {{-- 1. CONTEXT --}}
    <header class="journey-context">
        <div>
            <p class="eyebrow">INTERNSHIP PROGRESS JOURNEY</p>
            <h1>Progress Magang</h1>
            <p>{{ $selected->mahasiswa->nama }} <span>·</span> {{ $selected->instansi }}</p>
        </div>
        <div class="journey-period">
            <span>PERIODE MAGANG</span>
            <strong>{{ $selected->tanggal_mulai->translatedFormat('d M Y') }} — {{ $selected->tanggal_selesai->translatedFormat('d M Y') }}</strong>
            @if ($magangList->count() > 1)
                <form method="GET" action="{{ route('progress') }}">
                    <select name="magang" onchange="this.form.submit()" aria-label="Pilih magang">
                        @foreach ($magangList as $m)
                            <option value="{{ $m->id }}" @selected($m->id === $selected->id)>
                                {{ $m->mahasiswa->nama }} · {{ $m->instansi }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </header>

    {{-- 2. HERO PROGRESS --}}
    <section class="journey-hero" style="--journey-progress: {{ $percent }}%;">
        <div class="journey-hero-copy">
            <p class="eyebrow">OVERALL PROGRESS</p>
            <div class="journey-percent">{{ $percent }}<small>%</small></div>
            <p class="journey-status">{{ $progress['status']['label'] }}</p>
            <div class="journey-progress-line"><span></span></div>
            <div class="journey-hero-meta">
                <span><strong>{{ $progress['completed_days'] }}</strong> aktivitas selesai</span>
                <span><strong>{{ $progress['target_days'] }}</strong> target hari</span>
                <span><strong>{{ $progress['remaining_days'] }}</strong> sisa hari</span>
            </div>
        </div>
        <div class="journey-hero-visual">
            <div class="journey-orbit">
                <span>{{ $progress['completed_days'] }}</span>
                <small>hari tercatat</small>
            </div>
            <p>Setiap entri logbook yang tersimpan membentuk timeline perjalanan perkembangan magang.</p>
        </div>
    </section>

    {{-- 3. METRICS --}}
    <section class="journey-metrics">
        <div>
            <span>Total Logbook</span>
            <strong>{{ $totalLogbook }}</strong>
        </div>
        <div>
            <span>Rata-rata Capaian</span>
            <strong>{{ $rataCapaian }}%</strong>
        </div>
        <div>
            <span>Total Minggu</span>
            <strong>{{ $jumlahMinggu }}</strong>
        </div>
    </section>

    {{-- 4. PROGRESS TREND --}}
    <section class="journey-trend">
        <div class="journey-section-title">
            <div>
                <p class="eyebrow">PROGRESS TREND</p>
                <h2>Perkembangan Progres Logbook</h2>
                <p>Grafik tren kumulatif dari minggu ke minggu berdasarkan data logbook existing.</p>
            </div>
        </div>
        <x-logbook-progress-chart :progress="$progress" />
    </section>

    {{-- 5. ACTIVITY JOURNEY --}}
    <section class="activity-journey">
        <div class="journey-section-title">
            <div>
                <p class="eyebrow">ACTIVITY JOURNEY</p>
                <h2>Langkah Perjalanan Magang</h2>
                <p>Timeline urutan kronologis aktivitas logbook mahasiswa.</p>
            </div>
        </div>
        <div class="journey-timeline">
            @forelse ($logbooks as $activity)
                @php
                    $level = match (true) {
                        $activity->persentase_capaian >= 90 => ['Sangat Baik', 'excellent'],
                        $activity->persentase_capaian >= 70 => ['Baik', 'good'],
                        $activity->persentase_capaian >= 40 => ['Dalam Proses', 'process'],
                        default => ['Perlu Ditingkatkan', 'improve']
                    };
                @endphp
                <article class="journey-step {{ $level[1] }}">
                    <time>{{ $activity->tanggal->translatedFormat('l, d/m/Y') }}</time>
                    <div class="journey-node"></div>
                    <div class="journey-step-content">
                        <div class="journey-step-heading">
                            <span class="journey-level {{ $level[1] }}">{{ $level[0] }}</span>
                            <strong>{{ $activity->persentase_capaian }}%</strong>
                        </div>
                        <h3>{{ $activity->deskripsi_tugas }}</h3>
                        @if ($activity->target)
                            <p><b>Target:</b> {{ $activity->target }}</p>
                        @endif
                        @if ($activity->kendala)
                            <p class="journey-obstacle"><b>Kendala:</b> {{ $activity->kendala }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="journey-empty-inline">Belum ada aktivitas logbook untuk membentuk perjalanan magang.</div>
            @endforelse
        </div>
    </section>

    {{-- 6. FULL LOGBOOK TABLE --}}
    <section class="journey-logbook">
        <div class="journey-section-title">
            <div>
                <p class="eyebrow">SEMUA LOGBOOK</p>
                <h2>Data Logbook Lengkap</h2>
            </div>
            <span>{{ $totalLogbook }} entri</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Aktivitas Logbook</th>
                        <th>Target</th>
                        <th>Kendala</th>
                        <th style="text-align:right">Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logbooks as $entry)
                        <tr>
                            <td style="white-space:nowrap; font-weight:600;">{{ $entry->tanggal->translatedFormat('l, d/m/Y') }}</td>
                            <td>{{ $entry->deskripsi_tugas }}</td>
                            <td>{{ $entry->target ?? '—' }}</td>
                            <td>{{ $entry->kendala ?? '—' }}</td>
                            <td style="text-align:right">
                                <span class="badge {{ $entry->persentase_capaian >= 90 ? 'badge-success' : ($entry->persentase_capaian >= 40 ? 'badge-info' : 'badge-warning') }}">
                                    {{ $entry->persentase_capaian }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="compact-empty">Belum ada entri logbook.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endif
@endsection
