@extends('layouts.app')

@section('page-title', 'Preview Presensi')

@section('content')
<style>
    .preview-stage {
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 32px 16px;
        overflow-x: auto;
    }
    .preview-paper {
        background: #fff;
        width: 1122px;
        max-width: none;
        margin: 0 auto;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.12);
        padding: 40px 46px;
        border: 1px solid #E2E8F0;
        font-family: 'Times New Roman', Times, serif;
        color: #000;
    }
    .preview-paper .lampiran { font-size: 10pt; line-height: 1.2; }
    .preview-paper .presensi-title { text-align: center; font-weight: bold; font-size: 12pt; margin: 2px 0 8px; }
    .preview-paper .info-bio { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .preview-paper .info-bio td { padding: 1px 2px; vertical-align: top; border: 0; font-size: 10pt; }
    .preview-paper .col-label { width: 16%; }
    .preview-paper .col-value { width: 33%; }
    .preview-paper .col-gap { width: 2%; }
    .preview-paper .att { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 8px; }
    .preview-paper .att th, .preview-paper .att td { border: 1px solid #000; padding: 2px 1px; vertical-align: middle; word-wrap: break-word; }
    .preview-paper .att th { font-weight: bold; }
    .preview-paper .att thead tr:nth-child(1) th { text-align: center; }
    .preview-paper .col-no { width: 3.4%; }
    .preview-paper .col-mg { width: 4.8%; }
    .preview-paper .att .t-no { font-size: 9pt; font-weight: bold; text-align: center; }
    .preview-paper .att .t-mg { font-size: 9pt; font-weight: bold; text-align: center; }
    .preview-paper .att .time-head { font-size: 6.5pt; text-align: center; }
    .preview-paper .att td { font-size: 8pt; text-align: center; }
    .preview-paper .att .ttd-cell { text-align: center; padding: 4px 1px; height: 2.2cm; vertical-align: middle; }
    .preview-paper .att .week-ttd img { width: 1.2cm; height: auto; }
    .preview-paper .ttd { margin-top: 34px; }
    .preview-paper .ttd .mengetahui { text-align: center; font-size: 11pt; margin-bottom: 34px; }
    .preview-paper .ttd .row { display: flex; text-align: center; font-size: 11pt; }
    .preview-paper .ttd .col { flex: 1; }

    @media (max-width: 1200px) {
        .preview-paper { width: 100%; min-width: 900px; }
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
        <div style="display:flex; gap:8px;">
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
        <div class="lampiran">Lampiran 2. Presensi</div>
        <div class="presensi-title">DAFTAR HADIR MAHASISWA MAGANG MERDEKA BELAJAR KAMPUS MERDEKA</div>

        @php
            $mhs = $magang->mahasiswa;
            $bioRows = [
                ['NIM' => $mhs->nim, 'Perguruan Tinggi' => $mhs->perguruan_tinggi],
                ['Nama' => $mhs->nama, 'Judul/Topik Magang' => $magang->judul_topik],
                ['Program Studi' => $mhs->program_studi, 'Nama Instansi/Lokasi' => $magang->instansi.', '.$magang->lokasi],
                ['Jurusan' => $mhs->jurusan, 'Bagian/Unit Kerja' => $magang->bagian_unit],
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
                    <col span="3" style="width: 5.1%;">
                @endfor
                <col>
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
                    <tr>
                        <td class="t-no">No.</td>
                        <td class="t-mg">Minggu Ke</td>
                        <td colspan="18" style="text-align: center; font-weight: bold; font-size: 8pt;">Hari Tanggal</td>
                    </tr>
                    <tr>
                        @foreach ($days as $day)
                            <td colspan="3" style="font-weight: bold; font-size: 8pt;">{{ $day['day'] }} {{ $day['display'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="t-no">{{ $week }}</td>
                        <td class="t-mg">{{ $roman::convert($week) }}</td>
                        @for ($i = 0; $i < 6; $i++)
                            <td class="time-head">Jam<br>Masuk</td><td class="time-head">Jam<br>Pulang</td><td class="time-head">TTD</td>
                        @endfor
                    </tr>
                    <tr>
                        @foreach ($slots as $slot)
                            <td>{{ $slot['masuk'] }}</td>
                            <td>{{ $slot['pulang'] }}</td>
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

        <div class="ttd">
            <div class="mengetahui">Mengetahui</div>
            <div class="row">
                <div class="col">Dosen Pembimbing I</div>
                <div class="col">Dosen Pembimbing II</div>
                <div class="col">Supervisor/Mentor</div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:24px; text-align:center;">
    <a href="{{ route('magang.show', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none;">&larr; Kembali ke Detail Magang</a>
</div>
@endsection