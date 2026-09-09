<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir Presensi Magang - {{ $magang->mahasiswa->nama }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 1.1cm 1.2cm 1.2cm 1.2cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .lampiran { font-size: 10pt; line-height: 1.2; }
        .presensi-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 2px 0 8px;
        }
        .info-bio { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .info-bio td { padding: 1px 2px; vertical-align: top; border: 0; }
        .col-label { width: 16%; }
        .col-value { width: 33%; }
        .col-gap { width: 2%; }
        .att { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 8px; }
        .att th, .att td { border: 1px solid #000; padding: 2px 1px; vertical-align: middle; word-wrap: break-word; }
        .att th { font-weight: bold; }
        .att thead tr:nth-child(1) th { text-align: center; }
        .col-no { width: 3.4%; }
        .col-mg { width: 4.8%; }
        .att .t-no { font-size: 9pt; font-weight: bold; text-align: center; }
        .att .t-mg { font-size: 9pt; font-weight: bold; text-align: center; }
        .att .time-head { font-size: 6.5pt; text-align: center; }
        .att td { font-size: 8pt; text-align: center; }
        .att .ttd-cell { text-align: center; padding: 4px 1px; height: 2.2cm; vertical-align: middle; }
        .att .week-ttd img { width: 1.2cm; height: auto; }
        .ttd { margin-top: 34px; }
        .ttd .mengetahui { text-align: center; font-size: 11pt; margin-bottom: 34px; }
        .ttd .row { display: flex; text-align: center; font-size: 11pt; }
        .ttd .col { flex: 1; }
    </style>
</head>
<body>
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
</body>
</html>