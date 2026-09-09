<?php

namespace Tests\Feature;

use App\Models\KopSurat;
use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Presensi;
use App\Services\WordExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpWord\IOFactory;
use Tests\TestCase;

class WordExportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        @unlink(storage_path('app/public/ttd/_test_ttd.png'));

        parent::tearDown();
    }

    private function docxText(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'docx_test');
        file_put_contents($path, $content);

        $zip = new \ZipArchive;
        $zip->open($path);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        unlink($path);

        $text = str_replace('</w:p>', "\n", $xml ?: '');

        return trim((string) preg_replace('/<[^>]+>/', '', $text));
    }

    private function documentXml(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'docx_xml');
        file_put_contents($path, $content);

        $zip = new \ZipArchive;
        $zip->open($path);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        unlink($path);

        return $xml ?: '';
    }

    private function magangDuaMinggu(): Magang
    {
        return Magang::factory()->create([
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-18',
        ]);
    }

    private function ttdFile(): string
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $path = storage_path('app/public/ttd/_test_ttd.png');

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }

        file_put_contents($path, $png);

        return $path;
    }

    public function test_logbook_export_renders_every_week_even_when_empty(): void
    {
        $magang = $this->magangDuaMinggu();

        $content = app(WordExportService::class)->logbookWord($magang);
        $text = $this->docxText($content);

        $this->assertStringStartsWith('PK', $content);
        $this->assertStringContainsString('Minggu I', $text);
        $this->assertStringContainsString('Minggu II', $text);
    }

    public function test_logbook_uses_a4_landscape(): void
    {
        $magang = Magang::factory()->create();

        $content = app(WordExportService::class)->logbookWord($magang);
        $text = $this->docxText($content);
        $xml = $this->documentXml($content);

        $this->assertStringContainsString('LAMPIRAN', $text);
        $this->assertStringContainsString('Lampiran 1. Form Logbook Kegiatan Magang Mahasiswa', $text);
        $this->assertStringNotContainsString('FORM LOGBOOK KEGIATAN MAGANG MAHASISWA', $text);
        $this->assertStringNotContainsString('LEMBAR KONSULTASI DOSEN PEMBIMBING', $text);
        $this->assertStringNotContainsString('MAGANG MERDEKA BELAJAR KAMPUS MERDEKA', $text);

        $this->assertStringContainsString('w:orient="landscape"', $xml);
        $this->assertStringContainsString('w:w="16838"', $xml);
        $this->assertStringContainsString('w:h="11906"', $xml);
    }

    public function test_no_kop_is_rendered_on_logbook(): void
    {
        $kop = KopSurat::factory()->create(['nama_instansi' => 'Politeknik Negeri Nusa Utara']);
        $magang = Magang::factory()->create(['kop_surats_id' => $kop->id]);

        $text = $this->docxText(app(WordExportService::class)->logbookWord($magang));

        $this->assertStringNotContainsString($kop->nama_instansi, $text);
        $this->assertStringNotContainsString('PERGURUAN TINGGI', $text);
        $this->assertStringNotContainsString('TEKNOLOGI INFORMATIKA', $text);
        $this->assertStringContainsString('LAMPIRAN', $text);
    }

    public function test_generated_docx_can_be_read_back_by_phpword(): void
    {
        $magang = $this->magangDuaMinggu();

        $content = app(WordExportService::class)->logbookWord($magang);

        $xml = $this->documentXml($content);

        $this->assertStringContainsString('Lampiran 1. Form Logbook Kegiatan Magang Mahasiswa', $xml);
        $this->assertStringContainsString('LAMPIRAN', $xml);

        $path = tempnam(sys_get_temp_dir(), 'docx_roundtrip');
        file_put_contents($path, $content);

        try {
            $doc = IOFactory::load($path);
            $this->assertGreaterThan(0, count($doc->getSections()));
        } finally {
            unlink($path);
        }
    }

    public function test_presensi_uses_reference_form_structure(): void
    {
        $kop = KopSurat::factory()->create(['nama_instansi' => 'Politeknik Negeri Nusa Utara']);
        $magang = $this->magangDuaMinggu();
        $magang->update(['kop_surats_id' => $kop->id]);
        Presensi::factory()->create(['magang_id' => $magang->id, 'tanggal' => '2026-01-05']);
        Presensi::factory()->create(['magang_id' => $magang->id, 'tanggal' => '2026-01-12']);

        $content = app(WordExportService::class)->presensiWord($magang);
        $text = $this->docxText($content);
        $xml = $this->documentXml($content);

        $this->assertStringContainsString('Lampiran 2. Presensi', $text);
        $this->assertStringContainsString('DAFTAR HADIR MAHASISWA MAGANG MERDEKA BELAJAR KAMPUS MERDEKA', $text);
        $this->assertStringNotContainsString('LEMBAR KONSULTASI DOSEN PEMBIMBING', $text);

        $this->assertStringContainsString('Hari Tanggal', $text);
        $this->assertStringContainsString('Minggu Ke', $text);
        $this->assertStringContainsString('TTD', $text);

        $this->assertStringContainsString('05/01', $text);
        $this->assertStringContainsString('08:00', $text);
        $this->assertStringContainsString('17:00', $text);

        $this->assertStringContainsString('Dosen Pembimbing I', $text);
        $this->assertStringContainsString('Dosen Pembimbing II', $text);
        $this->assertStringContainsString('Supervisor/Mentor', $text);
        $this->assertStringContainsString('Mengetahui', $text);

        $this->assertStringContainsString('II', $text);
        $this->assertMatchesRegularExpression('/Jam Masuk\nJam Pulang\nTTD/', $text);
        $this->assertMatchesRegularExpression('/08:00\n17:00/', $text);

        $this->assertStringContainsString('w:gridSpan w:val="18"', $xml);
        $this->assertStringContainsString('w:gridSpan w:val="3"', $xml);
        $this->assertStringContainsString('w:val="restart"', $xml);
        $this->assertStringContainsString('w:val="continue"', $xml);
        $this->assertStringContainsString('w:orient="landscape"', $xml);
    }

    public function test_logbook_word_embeds_mahasiswa_ttd_only_when_confirmed(): void
    {
        $this->ttdFile();
        $magang = $this->magangDuaMinggu();
        $magang->mahasiswa->update(['ttd_path' => 'ttd/_test_ttd.png']);

        $withoutConfirmation = $this->documentXml(app(WordExportService::class)->logbookWord($magang));
        $this->assertStringNotContainsString('v:imagedata', $withoutConfirmation);

        Logbook::factory()->create([
            'magang_id' => $magang->id,
            'tanggal' => '2026-01-06',
            'ttd_confirmed' => true,
        ]);

        $withConfirmation = $this->documentXml(app(WordExportService::class)->logbookWord($magang));
        $this->assertStringContainsString('v:imagedata', $withConfirmation);
    }

    public function test_presensi_word_embeds_ttd_only_in_confirmed_week(): void
    {
        $this->ttdFile();
        $magang = $this->magangDuaMinggu();
        $magang->mahasiswa->update(['ttd_path' => 'ttd/_test_ttd.png']);

        Presensi::factory()->create([
            'magang_id' => $magang->id,
            'tanggal' => '2026-01-05',
            'ttd_confirmed' => true,
        ]);
        Presensi::factory()->create([
            'magang_id' => $magang->id,
            'tanggal' => '2026-01-12',
            'ttd_confirmed' => false,
        ]);

        $xml = $this->documentXml(app(WordExportService::class)->presensiWord($magang));

        $this->assertSame(1, preg_match_all('/<v:imagedata/', $xml));
    }
}
