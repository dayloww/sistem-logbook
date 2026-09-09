<?php

namespace Tests\Feature;

use App\Models\KopSurat;
use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Presensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        @unlink(storage_path('app/public/ttd/_export_ttd.png'));

        parent::tearDown();
    }

    public function test_logbook_pdf_downloads_successfully(): void
    {
        $magang = $this->magangDenganKop();

        $this->get(route('export.logbook.pdf', $magang))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_uses_a4_landscape(): void
    {
        $magang = $this->magangDenganKop();

        $pdf = $this->get(route('export.logbook.pdf', $magang))->getContent();

        $this->assertMatchesRegularExpression(
            '/MediaBox\s*\[\s*[\d.]+\s+[\d.]+\s+([\d.]+)\s+([\d.]+)\s*\]/',
            $pdf,
            'MediaBox not found in PDF output'
        );

        preg_match('/MediaBox\s*\[\s*[\d.]+\s+[\d.]+\s+([\d.]+)\s+([\d.]+)\s*\]/', $pdf, $m);
        $this->assertGreaterThan($m[2], (float) $m[1]);
    }

    public function test_presensi_pdf_downloads_successfully(): void
    {
        $magang = $this->magangDenganKop();

        $this->get(route('export.presensi.pdf', $magang))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_logbook_pdf_uses_appendix_title_not_consultation(): void
    {
        $magang = $this->magangDenganKop();

        $view = view('exports.logbook-pdf', compact('magang'))->render();

        $this->assertStringContainsString('LAMPIRAN', $view);
        $this->assertStringContainsString('Lampiran 1. Form Logbook Kegiatan Magang Mahasiswa', $view);
        $this->assertStringNotContainsString('FORM LOGBOOK KEGIATAN MAGANG MAHASISWA', $view);
        $this->assertStringNotContainsString('LEMBAR KONSULTASI DOSEN PEMBIMBING', $view);
        $this->assertStringNotContainsString('MAGANG MERDEKA BELAJAR KAMPUS MERDEKA', $view);
        $this->assertStringNotContainsString('KEMENTERIAN PENDIDIKAN TINGGI', $view);
        $this->assertStringNotContainsString('Politeknik Negeri Nusa Utara', $view);
    }

    public function test_logbook_pdf_embeds_mahasiswa_ttd_when_confirmed(): void
    {
        $magang = $this->magangSigned();
        $logbook = $magang->logbooks()->first();
        $logbook->update(['ttd_confirmed' => true]);

        $pdf = $this->get(route('export.logbook.pdf', $magang))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('/Image', $pdf);
    }

    public function test_logbook_pdf_has_no_image_without_confirmation(): void
    {
        $magang = $this->magangSigned();

        $pdf = $this->get(route('export.logbook.pdf', $magang))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('/Image', $pdf);
    }

    public function test_presensi_pdf_embeds_mahasiswa_ttd_when_confirmed(): void
    {
        $magang = $this->magangSigned();
        $presensi = $magang->presensi()->first();
        $presensi->update(['ttd_confirmed' => true]);

        $pdf = $this->get(route('export.presensi.pdf', $magang))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('/Image', $pdf);
    }

    private function magangDenganKop(): Magang
    {
        $kop = KopSurat::factory()->create(['nama_instansi' => 'Politeknik Negeri Nusa Utara']);

        $magang = Magang::factory()->create([
            'kop_surats_id' => $kop->id,
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-18',
        ]);

        Logbook::factory()->create(['magang_id' => $magang->id, 'tanggal' => '2026-01-06']);
        Presensi::factory()->create(['magang_id' => $magang->id, 'tanggal' => '2026-01-05']);

        return $magang;
    }

    private function magangSigned(): Magang
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $path = storage_path('app/public/ttd/_export_ttd.png');

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }

        file_put_contents($path, $png);

        $magang = $this->magangDenganKop();
        $magang->mahasiswa->update(['ttd_path' => 'ttd/_export_ttd.png']);

        return $magang;
    }
}
