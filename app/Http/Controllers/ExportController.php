<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Services\WordExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    private function sanitizeFilename(string $value): string
    {
        $ascii = Str::ascii($value);

        return trim((string) preg_replace('/[^A-Za-z0-9_-]+/', '_', $ascii), '_-');
    }

    private function pdfOptions(): array
    {
        return [
            'isRemoteEnabled' => true,
            'dpi' => 96,
            'chroot' => [base_path()],
        ];
    }

    public function index(Magang $magang)
    {
        $magang->load('mahasiswa');

        return view('export.index', compact('magang'));
    }

    public function logbookWord(Magang $magang, WordExportService $service)
    {
        $content = $service->logbookWord($magang);

        $filename = 'Logbook_'.$this->sanitizeFilename($magang->mahasiswa->nama).'_'.$this->sanitizeFilename($magang->instansi).'.docx';

        return response($content, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function presensiWord(Magang $magang, WordExportService $service)
    {
        $content = $service->presensiWord($magang);

        $filename = 'Presensi_'.$this->sanitizeFilename($magang->mahasiswa->nama).'_'.$this->sanitizeFilename($magang->instansi).'.docx';

        return response($content, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function logbookPdf(Magang $magang)
    {
        $magang->load([
            'mahasiswa',
            'logbooks' => fn ($q) => $q->orderBy('tanggal'),
            'catatanMingguan' => fn ($q) => $q->orderBy('minggu_ke'),
            'kopSurat',
        ]);

        $filename = 'Logbook_'.$this->sanitizeFilename($magang->mahasiswa->nama).'_'.$this->sanitizeFilename($magang->instansi).'.pdf';

        $pdf = Pdf::loadView('exports.logbook-pdf', compact('magang'))
            ->setPaper('a4', 'landscape')
            ->setOptions($this->pdfOptions());

        return $pdf->download(trim($filename));
    }

    public function presensiPdf(Magang $magang)
    {
        $magang->load([
            'mahasiswa',
            'presensi' => fn ($q) => $q->orderBy('tanggal'),
            'kopSurat',
        ]);

        $filename = 'Presensi_'.$this->sanitizeFilename($magang->mahasiswa->nama).'_'.$this->sanitizeFilename($magang->instansi).'.pdf';

        $pdf = Pdf::loadView('exports.presensi-pdf', compact('magang'))
            ->setPaper('a4', 'landscape')
            ->setOptions($this->pdfOptions());

        return $pdf->download(trim($filename));
    }
}
