<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir Presensi Magang - {{ $magang->mahasiswa->nama }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 1.0cm 1.2cm 1.2cm 1.2cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            color: #000000;
            background: #ffffff;
        }

        /* Kop Surat Styles */
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-logo { width: 2.6cm; vertical-align: middle; text-align: center; }
        .kop-text { text-align: center; vertical-align: middle; }
        .kop-spacer { width: 2.6cm; }
        .kop-ministry { font-size: 11pt; font-weight: bold; text-transform: uppercase; line-height: 1.25; }
        .kop-instansi { font-size: 12pt; font-weight: bold; text-transform: uppercase; line-height: 1.25; }
        .kop-jurusan { font-size: 12pt; font-weight: bold; text-transform: uppercase; line-height: 1.25; }
        .kop-contact { font-size: 9pt; font-weight: normal; line-height: 1.3; }
        .kop-gap { height: 4px; }
        .kop-title { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; line-height: 1.2; }
        .kop-title-first { margin-top: 2px; }
        .kop-border { border-bottom: 3px double #000000; margin-top: 6px; margin-bottom: 12px; font-size: 1px; line-height: 1px; }

        /* Meta & Document Title Styles */
        .lampiran { font-size: 10pt; line-height: 1.2; margin-bottom: 2px; }
        .presensi-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 4px 0 12px;
            text-transform: uppercase;
        }
        .info-bio { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 10px; }
        .info-bio td { padding: 2px 3px; vertical-align: top; border: none; font-size: 10pt; }
        .col-label { width: 18%; font-weight: bold; }
        .col-value { width: 31%; }
        .col-gap { width: 2%; }

        /* Presensi Table Styles */
        .att { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; page-break-inside: auto; }
        .att th, .att td { border: 1px solid #000000; padding: 3px 2px; vertical-align: middle; word-wrap: break-word; }
        .att th { font-weight: bold; background-color: #F8FAFC; text-align: center; }
        .att td { font-size: 8pt; text-align: center; }
        .col-no { width: 4.2%; }
        .col-mg { width: 5.8%; }
        .t-no { font-size: 9.5pt; font-weight: bold; text-align: center; vertical-align: middle; }
        .t-mg { font-size: 9.5pt; font-weight: bold; text-align: center; vertical-align: middle; }
        .time-head { font-size: 7pt; font-weight: bold; text-align: center; background-color: #F8FAFC; }
        .ttd-cell { text-align: center; padding: 2px; height: 1.8cm; vertical-align: middle; }
        .week-ttd img { max-height: 1.4cm; max-width: 1.4cm; object-fit: contain; }

        /* Signature Block Styles */
        .ttd-container { margin-top: 24px; page-break-inside: avoid; }
        .ttd-mengetahui { text-align: center; font-size: 11pt; margin-bottom: 20px; }
        .ttd-table { width: 100%; border-collapse: collapse; table-layout: fixed; border: none; text-align: center; }
        .ttd-table td { border: none; vertical-align: top; padding: 0 10px; text-align: center; }
        .ttd-role { font-weight: bold; font-size: 11pt; margin-bottom: 50px; }
        .ttd-name { font-size: 10.5pt; font-weight: bold; text-decoration: underline; }
        .ttd-sub { font-size: 9.5pt; color: #333; margin-top: 2px; }
    </style>
</head>
<body>
    @if ($magang->kopSurat)
        @include('exports.partials.kop', ['kop' => $magang->kopSurat, 'documentType' => 'presensi', 'web' => false])
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
            ? storage_path('app/public/'.$mhs->ttd_path)
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
</body>
</html>