<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Form Logbook Kegiatan Magang - {{ $magang->mahasiswa->nama }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 1.4cm 1.6cm 1.5cm 1.6cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .appendix-heading {
            font-weight: bold;
            font-size: 10.5pt;
            margin: 0 0 2px;
        }

        .lampiran {
            font-style: italic;
            font-size: 10pt;
            margin: 8px 0 10px;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .identity-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            vertical-align: top;
            font-size: 10pt;
        }
        .identity-table td.label { width: 34%; }

        .week-heading {
            font-weight: bold;
            font-size: 12pt;
            margin: 12px 0 6px;
        }

        .logbook-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .logbook-table th, .logbook-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }
        .logbook-table th {
            font-weight: bold;
            font-size: 8.5pt;
            text-align: center;
        }
        .logbook-table td {
            font-size: 8.5pt;
        }
        .logbook-table td.no { text-align: center; width: 6%; }
        .logbook-table td.tanggal { text-align: center; width: 13%; }
        .logbook-table td.deskripsi { width: 21%; }
        .logbook-table td.target { width: 27%; }
        .logbook-table td.persen { text-align: center; width: 13%; }
        .logbook-table td.kendala { width: 20%; }
        .logbook-table tr.blank td { height: 30px; }

        .catatan-heading {
            font-weight: bold;
            font-size: 10pt;
            margin: 6px 0 4px;
        }
        .catatan-placeholder {
            font-style: italic;
            font-size: 9pt;
            margin: 0 0 4px;
        }
        .catatan-box {
            width: 100%;
            border: 1px solid #000;
            height: 3.2cm;
            margin-bottom: 14px;
        }

        .ttd-table { width: 100%; border-collapse: collapse; }
        .ttd-table td { text-align: center; font-size: 11pt; vertical-align: top; padding: 2px; }
        .ttd-table td.col1 { width: 25%; }
        .ttd-table td.col2 { width: 25%; }
        .ttd-table td.col3 { width: 25%; }
        .ttd-table td.col4 { width: 25%; }
        .ttd-space { height: 4.5cm; }
        .ttd-img { width: 2.8cm; height: auto; }

        .sign-date { font-size: 11pt; }
        .sign-spacer { width: 75%; }
        .sign-col { width: 25%; text-align: center; }
        .mahasiswa-space { height: 3.2cm; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @php
        $mhs = $magang->mahasiswa;

        $dosenPembimbing = '1. '.($magang->dosen_pembimbing_1 ?: '');
        if ($magang->dosen_pembimbing_2) {
            $dosenPembimbing .= "\n2. ".$magang->dosen_pembimbing_2;
        }

        $rows = [
            'NIM' => $mhs->nim,
            'Nama' => $mhs->nama,
            'Program Studi' => $mhs->program_studi,
            'Jurusan' => $mhs->jurusan,
            'Judul / Topik Magang' => $magang->judul_topik,
            'Lama Kegiatan' => $magang->lama_kegiatan ?: '-',
            'Total Jam' => $magang->total_jam ? $magang->total_jam.' Jam' : '-',
            'Jumlah SKS' => $magang->jumlah_sks ? $magang->jumlah_sks.' SKS' : '-',
            'Dosen Pembimbing' => $dosenPembimbing,
        ];

        $no = 1;
        $jumlahMinggu = $magang->jumlahMinggu();
        $log = $magang->logbooks;
        $ttdPath = $mhs->ttd_path && file_exists(storage_path('app/public/'.$mhs->ttd_path))
            ? storage_path('app/public/'.$mhs->ttd_path)
            : null;
        $logSigned = $log->contains(fn ($l) => (bool) $l->ttd_confirmed);
    @endphp

    <div class="appendix-heading">LAMPIRAN</div>
    <div class="lampiran">Lampiran 1. Form Logbook Kegiatan Magang Mahasiswa</div>

    <table class="identity-table">
        @foreach ($rows as $label => $value)
            <tr>
                <td class="label">{{ $label }}</td>
                <td>: {!! nl2br(e($value)) !!}</td>
            </tr>
        @endforeach
    </table>

    @foreach (range(1, $jumlahMinggu) as $week)
        @php
            $items = $log->filter(fn ($l) => $l->mingguKe() === $week)->values();
            $ct = $magang->catatanMingguan->firstWhere('minggu_ke', $week);
            $weekLabel = 'Minggu '.App\Support\RomanNumeral::convert($week);
        @endphp
        @if ($loop->iteration > 1)
            <div class="page-break"></div>
        @endif
        <div class="week-heading">{{ $weekLabel }}</div>
        <table class="logbook-table">
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th class="tanggal">H/TGL/BLN</th>
                    <th class="deskripsi">Deskripsi Tugas</th>
                    <th class="target">Target</th>
                    <th class="persen">Presentasi<br>Capaian</th>
                    <th class="kendala">Kendala</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dataCount = $items->count();
                    $rowsToRender = max($dataCount, 10);
                @endphp
                @for ($i = 0; $i < $rowsToRender; $i++)
                    @php $item = $items[$i] ?? null; @endphp
                    <tr class="{{ $item ? '' : 'blank' }}">
                        <td class="no">{{ $no++ }}</td>
                        <td class="tanggal">{{ $item ? $item->tanggal->format('d/m/Y') : '' }}</td>
                        <td class="deskripsi">{{ $item ? $item->deskripsi_tugas : '' }}</td>
                        <td class="target">{{ $item ? ($item->target ?? '') : '' }}</td>
                        <td class="persen">{{ $item ? $item->persentase_capaian.'%' : '' }}</td>
                        <td class="kendala">{{ $item ? ($item->kendala ?? '') : '' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="catatan-heading">Catatan Minggu Ke : {{ App\Support\RomanNumeral::convert($week) }}{{ $ct && $ct->catatan ? '' : ' (dst..)' }}</div>
        @unless ($ct && $ct->catatan)
            <div class="catatan-placeholder">Berisi komentar/masukan/catatan hal-hal khusus yang terjadi dalam waktu tertentu.</div>
        @endunless
        <div class="catatan-box">{{ $ct && $ct->catatan ? $ct->catatan : '' }}</div>
    @endforeach

    <div class="page-break"></div>

    <table class="ttd-table">
        <tr>
            <td class="col1"></td>
            <td class="col2">Dosen Pembimbing I</td>
            <td class="col3">Dosen Pembimbing II</td>
            <td class="col4">Supervisor</td>
        </tr>
        <tr>
            <td class="col1"></td>
            <td class="col2 ttd-space">( )</td>
            <td class="col3 ttd-space">( )</td>
            <td class="col4 ttd-space">( )</td>
        </tr>
        <tr>
            <td class="col1"></td>
            <td class="col2">{{ $magang->dosen_pembimbing_1 ?: '......................................' }}</td>
            <td class="col3">{{ $magang->dosen_pembimbing_2 ?: '......................................' }}</td>
            <td class="col4">{{ $magang->supervisor ?: '......................................' }}</td>
        </tr>
    </table>

    <div style="height: 1.2cm;"></div>

    <table class="ttd-table">
        <tr>
            <td class="sign-spacer"></td>
            <td class="sign-col sign-date">Tempat, Tanggal / {{ \Carbon\Carbon::now()->translatedFormat('F') }} / {{ \Carbon\Carbon::now()->format('Y') }}</td>
        </tr>
        <tr>
            <td class="sign-spacer"></td>
            <td class="sign-col mahasiswa-space">
                @if ($logSigned && $ttdPath)
                    <img class="ttd-img" src="{{ $ttdPath }}" alt="TTD">
                @else
                    ( )
                @endif
            </td>
        </tr>
        <tr>
            <td class="sign-spacer"></td>
            <td class="sign-col">Mahasiswa Magang</td>
        </tr>
        <tr>
            <td class="sign-spacer"></td>
            <td class="sign-col">{{ $mhs->nama }}</td>
        </tr>
    </table>
</body>
</html>
