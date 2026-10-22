@extends('layouts.app')

@section('page-title', 'Preview Presensi')

@section('content')
<style>
    .preview-stage {
        background: #F1F5F9;
        border: 1px solid #CBD5E1;
        border-radius: 12px;
        padding: 32px 16px;
        overflow-x: auto;
    }
    .preview-paper {
        background: #FFFFFF;
        width: 1120px;
        max-width: none;
        margin: 0 auto;
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.15), 0 4px 10px -3px rgba(0,0,0,0.08);
        padding: 14mm 16mm;
        border: 1px solid #CBD5E1;
        font-family: 'Times New Roman', Times, serif;
        color: #000000;
    }

    /* Kop Surat Styles */
    .preview-paper .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    .preview-paper .kop-logo { width: 2.6cm; vertical-align: middle; text-align: center; }
    .preview-paper .kop-text { text-align: center; vertical-align: middle; }
    .preview-paper .kop-spacer { width: 2.6cm; }
    .preview-paper .kop-ministry { font-size: 11pt; font-weight: bold; text-transform: uppercase; line-height: 1.25; }
    .preview-paper .kop-instansi { font-size: 12pt; font-weight: bold; text-transform: uppercase; line-height: 1.25; }
    .preview-paper .kop-jurusan { font-size: 12pt; font-weight: bold; text-transform: uppercase; line-height: 1.25; }
    .preview-paper .kop-contact { font-size: 9pt; font-weight: normal; line-height: 1.3; }
    .preview-paper .kop-gap { height: 4px; }
    .preview-paper .kop-title { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; line-height: 1.2; }
    .preview-paper .kop-title-first { margin-top: 2px; }
    .preview-paper .kop-border { border-bottom: 3px double #000000; margin-top: 6px; margin-bottom: 12px; font-size: 1px; line-height: 1px; }

    /* Meta & Document Title Styles */
    .preview-paper .lampiran { font-size: 10pt; line-height: 1.2; margin-bottom: 2px; }
    .preview-paper .presensi-title {
        text-align: center;
        font-weight: bold;
        font-size: 12pt;
        margin: 4px 0 12px;
        text-transform: uppercase;
    }
    .preview-paper .info-bio { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 10px; }
    .preview-paper .info-bio td { padding: 2px 3px; vertical-align: top; border: none; font-size: 10pt; }
    .preview-paper .col-label { width: 18%; font-weight: bold; }
    .preview-paper .col-value { width: 31%; }
    .preview-paper .col-gap { width: 2%; }

    /* Presensi Table Styles */
    .preview-paper .att { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; }
    .preview-paper .att th, .preview-paper .att td { border: 1px solid #000000; padding: 3px 2px; vertical-align: middle; word-wrap: break-word; }
    .preview-paper .att th { font-weight: bold; background-color: #F8FAFC; text-align: center; }
    .preview-paper .att td { font-size: 8pt; text-align: center; }
    .preview-paper .col-no { width: 4.2%; }
    .preview-paper .col-mg { width: 5.8%; }
    .preview-paper .t-no { font-size: 9.5pt; font-weight: bold; text-align: center; vertical-align: middle; }
    .preview-paper .t-mg { font-size: 9.5pt; font-weight: bold; text-align: center; vertical-align: middle; }
    .preview-paper .time-head { font-size: 7pt; font-weight: bold; text-align: center; background-color: #F8FAFC; }
    .preview-paper .ttd-cell { text-align: center; padding: 2px; height: 1.8cm; vertical-align: middle; }
    .preview-paper .week-ttd img { max-height: 1.4cm; max-width: 1.4cm; object-fit: contain; }

    /* Signature Block Styles */
    .preview-paper .ttd-container { margin-top: 28px; page-break-inside: avoid; }
    .preview-paper .ttd-mengetahui { text-align: center; font-size: 11pt; margin-bottom: 20px; }
    .preview-paper .ttd-table { width: 100%; border-collapse: collapse; table-layout: fixed; border: none; text-align: center; }
    .preview-paper .ttd-table td { border: none; vertical-align: top; padding: 0 10px; text-align: center; }
    .preview-paper .ttd-role { font-weight: bold; font-size: 11pt; margin-bottom: 50px; }
    .preview-paper .ttd-name { font-size: 10.5pt; font-weight: bold; text-decoration: underline; }
    .preview-paper .ttd-sub { font-size: 9.5pt; color: #333; margin-top: 2px; }

    @media print {
        body * { visibility: hidden; }
        .preview-paper, .preview-paper * { visibility: visible; }
        .preview-paper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            border: none;
            box-shadow: none;
        }
        .sidebar, .topbar, .page-header, .preview-stage {
            background: transparent !important;
            padding: 0 !important;
            border: none !important;
        }
    }

    @media (max-width: 1200px) {
        .preview-paper { width: 100%; min-width: 960px; }
    }
</style>

<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('magang.export.index', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Export Dokumen
                </a>
            </div>
            <h1 class="page-title">Preview Daftar Hadir / Presensi</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <button onclick="window.print()" class="btn btn-secondary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Dokumen
            </button>
            <a href="{{ route('export.presensi.word', $magang) }}" class="btn btn-primary" style="background:#0284C7; border-color:#0284C7;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Word (.docx)
            </a>
            <a href="{{ route('export.presensi.pdf', $magang) }}" class="btn btn-danger">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>
        </div>
    </div>
</div>

<div class="preview-stage">
    <div class="preview-paper">
        @if ($magang->kopSurat)
            @include('exports.partials.kop', ['kop' => $magang->kopSurat, 'documentType' => 'presensi', 'web' => true])
        @else
            <div class="lampiran">Lampiran 2. Presensi</div>
            <div class="presensi-title">DAFTAR HADIR MAHASISWA MAGANG MERDEKA BELAJAR KAMPUS MERDEKA</div>
        @endif

        @php
            $mhs = $magang->mahasiswa;
            $bioRows = [
                ['NIM' => $mhs->nim, 'Perguruan Tinggi' => $mhs->perguruan_tinggi],
                ['Nama' => $mhs->nama, 'Judul/Topik Magang' => $magang->judul_topik],
                ['Program Studi' => $mhs->program_studi, 'Nama Instansi/Lokasi' => implode(', ', array_filter([$magang->instansi, $magang->lokasi]))],
                ['Jurusan' => $mhs->jurusan, 'Bagian/Unit Kerja' => $magang->bagian_unit ?: '-'],
            ];
            $roman = \App\Support\RomanNumeral::class;
            $jumlahMinggu = $magang->jumlahMinggu();
            $presensi = $magang->presensi;
            $ttdPath = $mhs->ttd_path && file_exists(storage_path('app/public/'.$mhs->ttd_path))
                ? asset('storage/'.$mhs->ttd_path)
                : null;
            $start = $magang->tanggal_mulai;
        @endphp

        <table class="info-bio">
            @foreach ($bioRows as $row)
                <tr>
                    <td class="col-label">{{ array_key_first($row) }}</td>
                    <td class="col-value">: {{ reset($row) }}</td>
                    <td class="col-gap"></td>
                    <td class="col-label">{{ array_key_last($row) }}</td>
                    <td class="col-value">: {{ end($row) }}</td>
                </tr>
            @endforeach
        </table>

        <table class="att">
            <colgroup>
                <col class="col-no">
                <col class="col-mg">
                @for ($i = 0; $i < 6; $i++)
                    <col span="3" style="width: 5.0%;">
                @endfor
            </colgroup>
            <tbody>
                @foreach (range(1, $jumlahMinggu) as $week)
                    @php
                        $weekStart = $start->copy()->addDays(($week - 1) * 7);
                        $days = [];
                        for ($i = 0; $i < 6; $i++) {
                            $date = $weekStart->copy()->addDays($i);
                            $days[] = [
                                'date' => $date,
                                'day' => $date->translatedFormat('D'),
                                'display' => $date->format('d/m'),
                            ];
                        }
                        $slots = [];
                        foreach ($days as $day) {
                            $item = $presensi->first(fn ($p) => $p->tanggal->isSameDay($day['date']));
                            $slots[] = [
                                'masuk' => $item && $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '',
                                'pulang' => $item && $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '',
                                'confirmed' => $item ? (bool) $item->ttd_confirmed : false,
                            ];
                        }
                    @endphp

                    {{-- Row 1: Header Titles (No, Minggu Ke, Hari Tanggal) --}}
                    <tr>
                        <td rowspan="2" class="t-no" style="font-weight: bold; background-color: #F8FAFC; text-align: center;">No</td>
                        <td rowspan="2" class="t-mg" style="font-weight: bold; background-color: #F8FAFC; text-align: center;">Minggu Ke</td>
                        <td colspan="18" style="text-align: center; font-weight: bold; font-size: 8.5pt; background-color: #F8FAFC;">
                            Hari Tanggal
                        </td>
                    </tr>

                    {{-- Row 2: Days & Dates --}}
                    <tr>
                        @foreach ($days as $day)
                            <td colspan="3" style="text-align: center; font-weight: bold; font-size: 8pt; background-color: #F8FAFC;">
                                {{ $day['day'] }} {{ $day['display'] }}
                            </td>
                        @endforeach
                    </tr>

                    {{-- Row 3: Week Values & Time Subheaders --}}
                    <tr>
                        <td rowspan="2" class="t-no">{{ $week }}</td>
                        <td rowspan="2" class="t-mg">{{ $roman::convert($week) }}</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td class="time-head">Jam<br>Masuk</td>
                            <td class="time-head">Jam<br>Pulang</td>
                            <td class="time-head">TTD</td>
                        @endfor
                    </tr>

                    {{-- Row 4: Attendance Values --}}
                    <tr>
                        @foreach ($slots as $slot)
                            <td style="font-size: 8pt;">{{ $slot['masuk'] }}</td>
                            <td style="font-size: 8pt;">{{ $slot['pulang'] }}</td>
                            <td class="ttd-cell">
                                @if ($slot['confirmed'] && $ttdPath)
                                    <div class="week-ttd"><img src="{{ $ttdPath }}" alt="TTD"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ttd-container">
            <div class="ttd-mengetahui">Mengetahui</div>
            <table class="ttd-table">
                <tr>
                    <td style="width: 33.33%;">
                        <div class="ttd-role">Dosen Pembimbing I</div>
                        <div class="ttd-name">{{ $magang->dosen_pembimbing_1 ?: '................................................' }}</div>
                    </td>
                    <td style="width: 33.33%;">
                        <div class="ttd-role">Dosen Pembimbing II</div>
                        <div class="ttd-name">{{ $magang->dosen_pembimbing_2 ?: '................................................' }}</div>
                    </td>
                    <td style="width: 33.33%;">
                        <div class="ttd-role">Supervisor / Mentor</div>
                        <div class="ttd-name">{{ $magang->supervisor ?: '................................................' }}</div>
                        @if ($magang->jabatan_supervisor)
                            <div class="ttd-sub">{{ $magang->jabatan_supervisor }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div style="margin-top:24px; text-align:center;">
    <a href="{{ route('magang.show', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none;">&larr; Kembali ke Detail Magang</a>
</div>
@endsection