@extends('layouts.app')

@section('page-title', 'Preview Logbook')

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
    .preview-paper .appendix-heading { font-weight: bold; font-size: 10.5pt; margin: 0 0 2px; }
    .preview-paper .lampiran { font-style: italic; font-size: 10pt; margin: 8px 0 10px; }
    .preview-paper .identity-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .preview-paper .identity-table td { border: 1px solid #000; padding: 3px 6px; vertical-align: top; font-size: 10pt; }
    .preview-paper .identity-table td.label { width: 34%; }
    .preview-paper .week-heading { font-weight: bold; font-size: 12pt; margin: 12px 0 6px; }
    .preview-paper .logbook-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .preview-paper .logbook-table th, .preview-paper .logbook-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: middle; }
    .preview-paper .logbook-table th { font-weight: bold; font-size: 8.5pt; text-align: center; background: transparent; }
    .preview-paper .logbook-table td { font-size: 8.5pt; }
    .preview-paper .logbook-table td.no { text-align: center; width: 6%; }
    .preview-paper .logbook-table td.tanggal { text-align: center; width: 13%; }
    .preview-paper .logbook-table td.deskripsi { width: 21%; }
    .preview-paper .logbook-table td.target { width: 27%; }
    .preview-paper .logbook-table td.persen { text-align: center; width: 13%; }
    .preview-paper .logbook-table td.kendala { width: 20%; }
    .preview-paper .logbook-table tr.blank td { height: 30px; }
    .preview-paper .catatan-heading { font-weight: bold; font-size: 10pt; margin: 6px 0 4px; }
    .preview-paper .catatan-placeholder { font-style: italic; font-size: 9pt; margin: 0 0 4px; }
    .preview-paper .catatan-box { width: 100%; border: 1px solid #000; min-height: 3.2cm; margin-bottom: 14px; padding: 6px; font-size: 9pt; }
    .preview-paper .ttd-table { width: 100%; border-collapse: collapse; }
    .preview-paper .ttd-table td { text-align: center; font-size: 11pt; vertical-align: top; padding: 2px; width: 25%; }
    .preview-paper .ttd-space { height: 4.5cm; }
    .preview-paper .ttd-img { width: 2.8cm; height: auto; }
    .preview-paper .sign-spacer { width: 75%; }
    .preview-paper .sign-col { width: 25%; text-align: center; }
    .preview-paper .mahasiswa-space { height: 3.2cm; }

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
            <h1 class="page-title">Preview Logbook Kegiatan Magang</h1>
            <p class="page-subtitle">{{ $magang->mahasiswa->nama }} · {{ $magang->instansi }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('export.logbook.word', $magang) }}" class="btn btn-primary" style="background:#0284C7; border-color:#0284C7;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Word (.docx)
            </a>
            <a href="{{ route('export.logbook.pdf', $magang) }}" class="btn btn-danger">
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
        @php
            $mhs = $magang->mahasiswa;
            $ttdPath = $mhs->ttd_path && file_exists(storage_path('app/public/'.$mhs->ttd_path))
                ? asset('storage/'.$mhs->ttd_path)
                : null;
            $logSigned = $magang->logbooks->contains(fn ($l) => (bool) $l->ttd_confirmed);

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

        @foreach ($logbooksByWeek as $week)
            @php
                $items = $week['items'];
                $ct = $week['catatan'];
                $weekLabel = 'Minggu '.App\Support\RomanNumeral::convert($week['minggu_ke']);
                $dataCount = $items->count();
                $rowsToRender = max($dataCount, 10);
            @endphp
            <div style="page-break-inside: avoid; margin-bottom:8px;">
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

                <div class="catatan-heading">Catatan Minggu Ke : {{ App\Support\RomanNumeral::convert($week['minggu_ke']) }}{{ $ct && $ct->catatan ? '' : ' (dst..)' }}</div>
                @unless ($ct && $ct->catatan)
                    <div class="catatan-placeholder">Berisi komentar/masukan/catatan hal-hal khusus yang terjadi dalam waktu tertentu.</div>
                @endunless
                <div class="catatan-box">{{ $ct && $ct->catatan ? $ct->catatan : '' }}</div>
            </div>
        @endforeach

        <div style="height: 1.2cm;"></div>

        <table class="ttd-table">
            <tr>
                <td></td>
                <td>Dosen Pembimbing I</td>
                <td>Dosen Pembimbing II</td>
                <td>Supervisor</td>
            </tr>
            <tr>
                <td></td>
                <td class="ttd-space">( )</td>
                <td class="ttd-space">( )</td>
                <td class="ttd-space">( )</td>
            </tr>
            <tr>
                <td></td>
                <td>{{ $magang->dosen_pembimbing_1 ?: '......................................' }}</td>
                <td>{{ $magang->dosen_pembimbing_2 ?: '......................................' }}</td>
                <td>{{ $magang->supervisor ?: '......................................' }}</td>
            </tr>
        </table>

        <div style="height: 1.2cm;"></div>

        <table class="ttd-table">
            <tr>
                <td class="sign-spacer"></td>
                <td class="sign-col">Tempat, Tanggal / {{ \Carbon\Carbon::now()->translatedFormat('F') }} / {{ \Carbon\Carbon::now()->format('Y') }}</td>
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
    </div>
</div>

<div style="margin-top:24px; text-align:center;">
    <a href="{{ route('magang.show', $magang) }}" style="font-size:13px; color:#64748B; text-decoration:none;">&larr; Kembali ke Detail Magang</a>
</div>
@endsection
