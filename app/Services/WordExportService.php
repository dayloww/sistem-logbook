<?php

namespace App\Services;

use App\Models\KopSurat;
use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Support\RomanNumeral;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\VerticalJc;

class WordExportService
{
    protected function newDocument(): PhpWord
    {
        Settings::setOutputEscapingEnabled(true);

        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        return $phpWord;
    }

    protected function newSection(PhpWord $phpWord, array $margins = []): Section
    {
        return $phpWord->addSection(array_merge([
            'orientation' => 'landscape',
            'pageSizeW' => round(Converter::cmToTwip(29.7)),
            'pageSizeH' => round(Converter::cmToTwip(21)),
            'marginTop' => round(Converter::cmToTwip(1.4)),
            'marginBottom' => round(Converter::cmToTwip(1.5)),
            'marginLeft' => round(Converter::cmToTwip(1.6)),
            'marginRight' => round(Converter::cmToTwip(1.6)),
        ], $margins));
    }

    protected function addKopSurat(Section $section, Magang $magang, string $documentType = 'konsultasi'): void
    {
        $kop = $magang->kopSurat;

        if (! $kop) {
            return;
        }

        $table = $section->addTable([
            'cellMarginLeft' => 0,
            'cellMarginRight' => 0,
            'cellMarginTop' => 0,
            'cellMarginBottom' => 0,
        ]);

        $table->addRow();

        $logoCell = $table->addCell(3000, ['valign' => VerticalJc::CENTER]);
        if ($logoPath = $this->resolvedLogoPath($kop)) {
            $logoCell->addTextRun(['alignment' => Jc::CENTER])
                ->addImage($logoPath, ['width' => round(Converter::cmToPoint(2.3))]);
        }

        $textCell = $table->addCell(9000, ['valign' => VerticalJc::CENTER]);
        foreach ($this->kopLines($kop) as $line) {
            $textCell->addText(
                $line['text'],
                $line['font'],
                ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0, 'lineHeight' => 1.15]
            );
        }

        $table->addCell(3000, ['valign' => VerticalJc::CENTER]);

        if ($documentType === 'none') {
            $this->addKopDivider($section);
        } else {
            $this->addKopDocumentTitle($section, $documentType);
        }
    }

    protected function addKopDivider(Section $section): void
    {
        $section->addText('', [], [
            'borderBottomSize' => 6,
            'borderBottomColor' => '000000',
            'borderBottomStyle' => 'single',
            'spaceBefore' => 20,
            'spaceAfter' => 60,
        ]);
    }

    protected function addKopDocumentTitle(Section $section, string $documentType): void
    {
        $isPresensi = $documentType === 'presensi';
        $isLogbook = $documentType === 'logbook';

        $title1 = $isPresensi
            ? 'DAFTAR HADIR / PRESENSI MAHASISWA MAGANG'
            : ($isLogbook
                ? 'FORM LOGBOOK KEGIATAN MAGANG MAHASISWA'
                : 'LEMBAR KONSULTASI DOSEN PEMBIMBING');

        $section->addText('', [], ['spaceAfter' => 40]);

        $section->addText(
            $title1,
            ['bold' => true, 'size' => 12],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 80, 'lineHeight' => 1.1]
        );

        if (! $isLogbook) {
            $title2 = $isPresensi
                ? 'MERDEKA BELAJAR KAMPUS MERDEKA'
                : 'MAGANG MERDEKA BELAJAR KAMPUS MERDEKA';

            $section->addText(
                $title2,
                ['bold' => true, 'size' => 12],
                ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 80, 'lineHeight' => 1.1]
            );
        }

        $section->addText('', [], [
            'borderBottomSize' => 8,
            'borderBottomColor' => '000000',
            'borderBottomStyle' => 'single',
            'spaceBefore' => 0,
            'spaceAfter' => 120,
        ]);
    }

    protected function kopLines(KopSurat $kop): array
    {
        $isKampus = $kop->jenis === 'kampus';

        $lines = $isKampus ? [
            ['text' => 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS,', 'font' => ['bold' => true, 'size' => 12]],
            ['text' => 'DAN TEKNOLOGI', 'font' => ['bold' => true, 'size' => 12]],
        ] : [];

        $lines[] = ['text' => $kop->nama_instansi, 'font' => ['bold' => true, 'size' => 12]];
        $lines[] = [
            'text' => ($isKampus ? 'JURUSAN' : 'UNIT').' TEKNOLOGI INFORMATIKA',
            'font' => ['bold' => true, 'size' => 13],
        ];

        if ($kop->alamat) {
            $lines[] = ['text' => $kop->alamat, 'font' => ['size' => 9.5]];
        }

        if ($kop->telepon) {
            $lines[] = ['text' => 'Telepon '.$kop->telepon, 'font' => ['size' => 9.5]];
        }

        if ($kop->website) {
            $lines[] = ['text' => 'Laman : '.$this->websiteUrl($kop->website), 'font' => ['size' => 9.5]];
        }

        return $lines;
    }

    protected function websiteUrl(string $url): string
    {
        return preg_match('#^https?://#i', $url) ? $url : 'http://'.$url;
    }

    protected function resolvedLogoPath(KopSurat $kop): ?string
    {
        if (! $kop->logo_path) {
            return null;
        }

        $path = storage_path('app/public/'.$kop->logo_path);

        return file_exists($path) ? $path : null;
    }

    protected function resolvedTtdPath(Mahasiswa $mhs): ?string
    {
        if (! $mhs->ttd_path) {
            return null;
        }

        $path = storage_path('app/public/'.$mhs->ttd_path);

        return file_exists($path) ? $path : null;
    }

    protected function documentTitle(Section $section, string $title): void
    {
        $section->addText(
            $title,
            ['bold' => true, 'size' => 12],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 120]
        );
    }

    protected function identityBlock(Section $section, Magang $magang): void
    {
        $mhs = $magang->mahasiswa;

        $section->addText(
            'Lampiran 1. Form Logbook Kegiatan Magang Mahasiswa',
            ['italic' => true, 'size' => 10.5],
            ['spaceBefore' => 60, 'spaceAfter' => 120]
        );

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

        $table = $section->addTable([
            'borderColor' => '000000',
            'borderSize' => 4,
            'cellMargin' => 40,
            'width' => 100,
            'unit' => TblWidth::PERCENT,
        ]);

        foreach ($rows as $label => $value) {
            $table->addRow();
            $labelCell = $table->addCell(3400);
            $labelCell->addText($label, ['size' => 10], ['spaceAfter' => 0, 'spaceBefore' => 0]);
            $valueCell = $table->addCell(6600);
            $valueRun = $valueCell->addTextRun(['spaceAfter' => 0, 'spaceBefore' => 0]);
            $valueRun->addText(': ', ['size' => 10]);

            if ($label === 'Dosen Pembimbing') {
                foreach (explode("\n", $value) as $i => $line) {
                    if ($i > 0) {
                        $valueRun->addTextBreak();
                    }
                    $valueRun->addText($line, ['size' => 10]);
                }
            } else {
                $valueRun->addText($value, ['size' => 10]);
            }
        }
    }

    protected function addPresensiIdentity(Section $section, Magang $magang): void
    {
        $mhs = $magang->mahasiswa;

        $rows = [
            ['label' => 'NIM', 'value' => $mhs->nim, 'labelR' => 'Perguruan Tinggi', 'valueR' => $mhs->perguruan_tinggi],
            ['label' => 'Nama', 'value' => $mhs->nama, 'labelR' => 'Judul/Topik Magang', 'valueR' => $magang->judul_topik],
            ['label' => 'Program Studi', 'value' => $mhs->program_studi, 'labelR' => 'Nama Instansi/Lokasi', 'valueR' => $magang->instansi.', '.$magang->lokasi],
            ['label' => 'Jurusan', 'value' => $mhs->jurusan, 'labelR' => 'Bagian/Unit Kerja', 'valueR' => $magang->bagian_unit],
        ];

        $table = $section->addTable([
            'cellMarginLeft' => 0,
            'cellMarginRight' => 0,
            'cellMarginTop' => 0,
            'cellMarginBottom' => 0,
        ]);

        foreach ($rows as $row) {
            $table->addRow();
            $table->addCell(2100)->addText($row['label'], ['size' => 10]);
            $table->addCell(5100)->addText(': '.$row['value'], ['size' => 10]);
            $table->addCell(350)->addText('', ['size' => 10]);
            $table->addCell(2100)->addText($row['labelR'], ['size' => 10]);
            $table->addCell(5100)->addText(': '.$row['valueR'], ['size' => 10]);
        }
    }

    protected function addPresensiTable(Section $section, Magang $magang, ?string $ttdPath = null): void
    {
        $noWidth = 500;
        $mingguWidth = 700;
        $timeWidth = 750;
        $ttdWidth = 750;
        $dayGroupWidth = ($timeWidth * 2) + $ttdWidth;
        $cellStyle = ['valign' => VerticalJc::CENTER];
        $center = ['alignment' => Jc::CENTER];

        $table = $section->addTable([
            'borderColor' => '000000',
            'borderSize' => 4,
            'cellMarginTop' => 20,
            'cellMarginBottom' => 20,
            'cellMarginLeft' => 30,
            'cellMarginRight' => 30,
        ]);

        $amount = $magang->jumlahMinggu();
        $presensi = $magang->presensi;

        foreach (range(1, $amount) as $week) {
            $days = $this->weekPresensiDays($magang, $week);
            $slots = $this->weeklySlots($presensi, $days);

            $table->addRow();
            $noHeader = $table->addCell($noWidth, ['vMerge' => 'restart'] + $cellStyle);
            $noHeader->addText('No.', ['bold' => true, 'size' => 8], $center);
            $mingguHeader = $table->addCell($mingguWidth, ['vMerge' => 'restart'] + $cellStyle);
            $mingguHeader->addText('Minggu Ke', ['bold' => true, 'size' => 8], $center);
            $hariHeader = $table->addCell($dayGroupWidth * 6, ['gridSpan' => 18] + $cellStyle);
            $hariHeader->addText('Hari Tanggal', ['bold' => true, 'size' => 8], $center);

            $table->addRow();
            $table->addCell($noWidth, ['vMerge' => 'continue'] + $cellStyle);
            $table->addCell($mingguWidth, ['vMerge' => 'continue'] + $cellStyle);
            foreach ($days as $day) {
                $cell = $table->addCell($dayGroupWidth, ['gridSpan' => 3] + $cellStyle);
                $cell->addText($day['day'], ['bold' => true, 'size' => 8], $center);
                $run = $cell->addTextRun($center);
                $run->addText($day['display'], ['bold' => true, 'size' => 8]);
            }

            $table->addRow();
            $noValue = $table->addCell($noWidth, ['vMerge' => 'restart'] + $cellStyle);
            $noValue->addText((string) $week, ['bold' => true, 'size' => 8], $center);
            $mingguValue = $table->addCell($mingguWidth, ['vMerge' => 'restart'] + $cellStyle);
            $mingguValue->addText(RomanNumeral::convert($week), ['bold' => true, 'size' => 8], $center);
            foreach (range(0, 5) as $_) {
                $table->addCell($timeWidth, $cellStyle)->addText('Jam Masuk', ['bold' => true, 'size' => 6.5], $center);
                $table->addCell($timeWidth, $cellStyle)->addText('Jam Pulang', ['bold' => true, 'size' => 6.5], $center);
                $table->addCell($ttdWidth, $cellStyle)->addText('TTD', ['bold' => true, 'size' => 6.5], $center);
            }

            $table->addRow(round(Converter::cmToTwip(2.2)));
            $table->addCell($noWidth, ['vMerge' => 'continue'] + $cellStyle);
            $table->addCell($mingguWidth, ['vMerge' => 'continue'] + $cellStyle);
            foreach ($slots as $slot) {
                $table->addCell($timeWidth, $cellStyle)->addText($slot['masuk'], ['size' => 8], $center);
                $table->addCell($timeWidth, $cellStyle)->addText($slot['pulang'], ['size' => 8], $center);
                $ttdCell = $table->addCell($ttdWidth, $cellStyle);
                if ($slot['confirmed'] && $ttdPath) {
                    $run = $ttdCell->addTextRun($center);
                    $run->addImage($ttdPath, ['width' => round(Converter::cmToPoint(1.2))]);
                } else {
                    $ttdCell->addText('', ['size' => 8]);
                }
            }
        }
    }

    protected function weekPresensiDays(Magang $magang, int $week): array
    {
        $base = $magang->tanggal_mulai->copy()->addDays(($week - 1) * 7);
        $days = [];

        for ($i = 0; $i < 6; $i++) {
            $date = $base->copy()->addDays($i);
            $days[] = [
                'date' => $date,
                'day' => $date->translatedFormat('D'),
                'display' => $date->format('d/m'),
            ];
        }

        return $days;
    }

    protected function weeklySlots($items, array $days): array
    {
        $slots = [];

        foreach ($days as $day) {
            $item = $items->first(fn ($p) => $p->tanggal->isSameDay($day['date']));

            $slots[] = [
                'masuk' => $item && $item->jam_masuk ? Carbon::parse($item->jam_masuk)->format('H:i') : '',
                'pulang' => $item && $item->jam_pulang ? Carbon::parse($item->jam_pulang)->format('H:i') : '',
                'confirmed' => $item ? (bool) $item->ttd_confirmed : false,
            ];
        }

        return $slots;
    }

    protected function addSignatureBlock(Section $section, array $columns, bool $addSpace = true, string $mengetahui = 'Mengetahui'): void
    {
        if ($addSpace) {
            $section->addTextBreak(1);
        }

        $section->addText($mengetahui, ['size' => 11], ['alignment' => Jc::CENTER, 'spaceAfter' => 140]);

        $table = $section->addTable([
            'alignment' => Jc::CENTER,
            'width' => 100,
            'unit' => TblWidth::PERCENT,
        ]);

        $width = intdiv(10000, max(count($columns), 1));

        $table->addRow();
        foreach ($columns as $col) {
            $table->addCell($width)->addText(
                $col['role'] ?? '',
                ['bold' => true, 'size' => 11],
                ['alignment' => Jc::CENTER]
            );
        }

        $table->addRow(round(Converter::cmToTwip(2.2)));
        foreach ($columns as $col) {
            $cell = $table->addCell($width, ['valign' => VerticalJc::CENTER]);
            if (! empty($col['ttd']) && file_exists($col['ttd'])) {
                $run = $cell->addTextRun(['alignment' => Jc::CENTER]);
                $run->addImage($col['ttd'], ['width' => round(Converter::cmToPoint(2.5))]);
            } else {
                $cell->addText('', ['size' => 11]);
            }
        }

        $table->addRow();
        foreach ($columns as $col) {
            $nameText = ! empty($col['name']) ? $col['name'] : '........................................';
            $isUnderline = ! empty($col['name']);
            $table->addCell($width)->addText(
                $nameText,
                ['bold' => true, 'size' => 10.5, 'underline' => $isUnderline ? 'single' : 'none'],
                ['alignment' => Jc::CENTER]
            );
        }
    }

    protected function styleTable(): array
    {
        return [
            'borderColor' => '000000',
            'borderSize' => 6,
            'cellMargin' => 60,
            'unit' => TblWidth::PERCENT,
            'width' => 100,
        ];
    }

    protected function headerCellWidths(int $colCount): array
    {
        $total = 10000;
        $avg = intdiv($total, $colCount);

        return array_fill(0, $colCount, $avg);
    }

    public function logbookWord(Magang $magang): string
    {
        $magang->load(['mahasiswa', 'logbooks' => fn ($q) => $q->orderBy('tanggal'), 'catatanMingguan' => fn ($q) => $q->orderBy('minggu_ke'), 'kopSurat']);

        $phpWord = $this->newDocument();
        $section = $this->newSection($phpWord);

        $section->addText('LAMPIRAN', ['bold' => true, 'size' => 10.5], ['spaceBefore' => 0, 'spaceAfter' => 0]);

        $this->identityBlock($section, $magang);

        $amount = $magang->jumlahMinggu();
        $log = $magang->logbooks;
        $catatan = $magang->catatanMingguan;
        $no = 1;

        foreach (range(1, $amount) as $week) {
            if ($week > 1) {
                $section->addPageBreak();
            }

            $section->addTextBreak(1);
            $weekLabel = 'Minggu '.RomanNumeral::convert($week);
            $section->addText($weekLabel, ['bold' => true, 'size' => 12], ['spaceAfter' => 60]);

            $items = $log->filter(fn ($l) => $l->mingguKe() === $week)->values();

            $this->addLogbookTable($section, $items, $no);
            $no += $items->count();

            $ct = $catatan->firstWhere('minggu_ke', $week);
            $this->addLogbookCatatanBox($section, $week, $ct?->catatan);
        }

        $this->addLogbookSignatureBlock($section, $magang);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'logbook');

        try {
            $writer->save($tempFile);

            return file_get_contents($tempFile);
        } finally {
            @unlink($tempFile);
        }
    }

    protected function addLogbookTable(Section $section, $items, int &$no): void
    {
        $widths = [600, 1300, 2100, 2700, 1300, 2000];
        $headers = ['No', 'H/TGL/BLN', 'Deskripsi Tugas', 'Target', null, 'Kendala'];
        $headerSub = [null, null, null, null, 'Capaian', null];

        $table = $section->addTable([
            'borderColor' => '000000',
            'borderSize' => 4,
            'cellMargin' => 40,
            'unit' => TblWidth::PERCENT,
            'width' => 100,
        ]);

        $table->addRow();
        foreach ($headers as $i => $h) {
            $cell = $table->addCell($widths[$i], ['valign' => VerticalJc::CENTER]);
            if ($h === null) {
                $run = $cell->addTextRun(['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
                $run->addText('Presentasi', ['bold' => true, 'size' => 8.5]);
                $run->addTextBreak();
                $run->addText('Capaian', ['bold' => true, 'size' => 8.5]);
            } else {
                $cell->addText($h, ['bold' => true, 'size' => 8.5], ['alignment' => Jc::CENTER]);
            }
        }

        $dataCount = $items->count();
        $rowsToRender = max($dataCount, 10);

        for ($i = 0; $i < $rowsToRender; $i++) {
            $item = $items[$i] ?? null;

            // Kosongkan baris dengan tinggi tetap agar rapi dan siap isi manual.
            $rowHeight = $item ? null : round(Converter::cmToTwip(0.85));
            $table->addRow($rowHeight);

            $noCell = $table->addCell($widths[0], ['valign' => VerticalJc::CENTER]);
            $noCell->addText((string) ($no + $i), ['size' => 8.5], ['alignment' => Jc::CENTER]);

            $tanggalCell = $table->addCell($widths[1], ['valign' => VerticalJc::CENTER]);
            $tanggalCell->addText(
                $item ? $item->tanggal->translatedFormat('l, d/m/Y') : '',
                ['size' => 8.5],
                ['alignment' => Jc::CENTER]
            );

            $table->addCell($widths[2], ['valign' => VerticalJc::CENTER])
                ->addText($item ? ($item->deskripsi_tugas ?: '') : '', ['size' => 8.5]);

            $table->addCell($widths[3], ['valign' => VerticalJc::CENTER])
                ->addText($item ? ($item->target ?? '') : '', ['size' => 8.5]);

            $capCell = $table->addCell($widths[4], ['valign' => VerticalJc::CENTER]);
            $capCell->addText($item ? $item->persentase_capaian.'%' : '', ['size' => 8.5], ['alignment' => Jc::CENTER]);

            $table->addCell($widths[5], ['valign' => VerticalJc::CENTER])
                ->addText($item ? ($item->kendala ?? '') : '', ['size' => 8.5]);
        }
    }

    protected function addLogbookCatatanBox(Section $section, int $mingguKe, ?string $catatan): void
    {
        $section->addTextBreak(1);

        $heading = 'Catatan Minggu Ke : '.RomanNumeral::convert($mingguKe).($catatan ? '' : ' (dst..)');
        $section->addText($heading, ['bold' => true, 'size' => 10], ['spaceAfter' => 40]);

        if (! $catatan) {
            $section->addText(
                'Berisi komentar/masukan/catatan hal-hal khusus yang terjadi dalam waktu tertentu.',
                ['italic' => true, 'size' => 9],
                ['spaceAfter' => 40]
            );
        }

        $box = $section->addTable([
            'borderColor' => '000000',
            'borderSize' => 4,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => TblWidth::PERCENT,
        ]);
        $box->addRow(round(Converter::cmToTwip(3.2)));

        $boxCell = $box->addCell(10000);
        if ($catatan) {
            $boxCell->addText($catatan, ['size' => 9]);
        } else {
            $boxCell->addText('', ['size' => 9]);
        }
    }

    protected function addLogbookSignatureBlock(Section $section, Magang $magang): void
    {
        $section->addPageBreak();

        $section->addTextBreak(2);

        $roles = [
            'Dosen Pembimbing I',
            'Dosen Pembimbing II',
            'Supervisor',
        ];

        $names = [
            $magang->dosen_pembimbing_1 ?: '......................................',
            $magang->dosen_pembimbing_2 ?: '......................................',
            $magang->supervisor ?: '......................................',
        ];

        $table = $section->addTable([
            'alignment' => Jc::LEFT,
            'width' => 100,
            'unit' => TblWidth::PERCENT,
        ]);

        $cols = [2500, 2500, 2500, 2500];

        $table->addRow();
        $table->addCell($cols[0])->addText('', ['size' => 11]);
        for ($i = 1; $i < 4; $i++) {
            $table->addCell($cols[$i])->addText($roles[$i - 1], ['size' => 11], ['alignment' => Jc::CENTER]);
        }

        $table->addRow(round(Converter::cmToTwip(4.5)));
        $table->addCell($cols[0])->addText('', ['size' => 11]);
        for ($i = 1; $i < 4; $i++) {
            $cell = $table->addCell($cols[$i]);
            $run = $cell->addTextRun(['alignment' => Jc::CENTER]);
            foreach (range(1, 6) as $_) {
                $run->addTextBreak();
            }
            $run->addText('( )', ['size' => 11]);
        }

        $table->addRow();
        $table->addCell($cols[0])->addText('', ['size' => 11]);
        for ($i = 1; $i < 4; $i++) {
            $table->addCell($cols[$i])->addText($names[$i - 1], ['size' => 11], ['alignment' => Jc::CENTER]);
        }

        $table->addRow(round(Converter::cmToTwip(1)));
        for ($i = 0; $i < 4; $i++) {
            $table->addCell($cols[$i])->addText('', ['size' => 11]);
        }

        $sign = $section->addTable([
            'alignment' => Jc::LEFT,
            'width' => 100,
            'unit' => TblWidth::PERCENT,
        ]);

        $sign->addRow();
        $sign->addCell(7500)->addText('', ['size' => 11]);
        $dateCell = $sign->addCell(2500);
        $dateCell->addText(
            $this->signatureDateText(),
            ['size' => 11],
            ['alignment' => Jc::CENTER]
        );

        $sign->addRow(round(Converter::cmToTwip(3.2)));
        $sign->addCell(7500)->addText('', ['size' => 11]);
        $mahasiswaCell = $sign->addCell(2500);

        $ttdPath = $magang->logbooks->contains(fn ($l) => (bool) $l->ttd_confirmed)
            ? $this->resolvedTtdPath($magang->mahasiswa)
            : null;

        if ($ttdPath) {
            $run = $mahasiswaCell->addTextRun(['alignment' => Jc::CENTER]);
            $run->addImage($ttdPath, ['width' => round(Converter::cmToPoint(2.8))]);
        } else {
            $run = $mahasiswaCell->addTextRun(['alignment' => Jc::CENTER]);
            foreach (range(1, 6) as $_) {
                $run->addTextBreak();
            }
            $run->addText('( )', ['size' => 11]);
        }

        $sign->addRow();
        $sign->addCell(7500)->addText('', ['size' => 11]);
        $sign->addCell(2500)->addText('Mahasiswa Magang', ['size' => 11], ['alignment' => Jc::CENTER]);

        $sign->addRow();
        $sign->addCell(7500)->addText('', ['size' => 11]);
        $sign->addCell(2500)->addText(
            $magang->mahasiswa->nama,
            ['size' => 11],
            ['alignment' => Jc::CENTER]
        );
    }

    protected function signatureDateText(): string
    {
        $now = Carbon::now();

        return 'Tempat, Tanggal / '.$now->translatedFormat('F').' / '.$now->format('Y');
    }

    public function presensiWord(Magang $magang): string
    {
        $magang->load(['mahasiswa', 'presensi' => fn ($q) => $q->orderBy('tanggal'), 'kopSurat']);

        $phpWord = $this->newDocument();
        $section = $this->newSection($phpWord, [
            'marginTop' => round(Converter::cmToTwip(1.1)),
            'marginBottom' => round(Converter::cmToTwip(1.2)),
            'marginLeft' => round(Converter::cmToTwip(1.2)),
            'marginRight' => round(Converter::cmToTwip(1.2)),
        ]);

        $section->addText('Lampiran 2. Presensi', ['size' => 10], ['spaceBefore' => 0, 'spaceAfter' => 20]);

        $section->addText(
            'DAFTAR HADIR MAHASISWA MAGANG MERDEKA BELAJAR KAMPUS MERDEKA',
            ['bold' => true, 'size' => 12],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 120]
        );

        $this->addPresensiIdentity($section, $magang);

        $section->addTextBreak(1);
        $this->addPresensiTable($section, $magang, $this->resolvedTtdPath($magang->mahasiswa));

        $this->addSignatureBlock($section, [
            ['role' => 'Dosen Pembimbing I', 'name' => $magang->dosen_pembimbing_1],
            ['role' => 'Dosen Pembimbing II', 'name' => $magang->dosen_pembimbing_2],
            ['role' => 'Supervisor/Mentor', 'name' => $magang->supervisor],
        ], true, 'Mengetahui');

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'presensi');

        try {
            $writer->save($tempFile);

            return file_get_contents($tempFile);
        } finally {
            @unlink($tempFile);
        }
    }
}
