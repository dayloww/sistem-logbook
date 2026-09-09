<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'nim' => '212110023',
            'nama' => 'Budi Santoso',
            'program_studi' => 'D4 Teknik Informatika',
            'jurusan' => 'Teknologi Informatika',
            'perguruan_tinggi' => 'Politeknik Negeri Nusa Utara',
        ];
    }

    public function test_store_stores_ttd_image(): void
    {
        Storage::fake('public');

        $this->post(route('mahasiswa.store'), array_merge($this->payload(), [
            'ttd' => UploadedFile::fake()->image('ttd.png'),
        ]))->assertRedirect(route('mahasiswa.index'));

        $mahasiswa = Mahasiswa::where('nim', '212110023')->first();

        $this->assertNotNull($mahasiswa->ttd_path);
        Storage::disk('public')->assertExists($mahasiswa->ttd_path);
    }

    public function test_store_rejects_non_image_ttd(): void
    {
        Storage::fake('public');

        $this->post(route('mahasiswa.store'), array_merge($this->payload(), [
            'ttd' => UploadedFile::fake()->create('ttd.txt', 100),
        ]))->assertSessionHasErrors('ttd');

        $this->assertDatabaseMissing('mahasiswa', ['nim' => '212110023']);
    }

    public function test_update_replaces_ttd_image(): void
    {
        Storage::fake('public');

        $mahasiswa = Mahasiswa::factory()->create([
            'ttd_path' => 'ttd/old.png',
        ]);
        Storage::disk('public')->put('ttd/old.png', 'old');

        $this->put(route('mahasiswa.update', $mahasiswa), array_merge($this->payload(), [
            'nim' => $mahasiswa->nim,
            'ttd' => UploadedFile::fake()->image('new_ttd.png'),
        ]))->assertRedirect(route('mahasiswa.index'));

        $this->assertNotSame('ttd/old.png', $mahasiswa->fresh()->ttd_path);
        Storage::disk('public')->assertMissing('ttd/old.png');
        Storage::disk('public')->assertExists($mahasiswa->fresh()->ttd_path);
    }

    public function test_update_can_remove_ttd_image(): void
    {
        Storage::fake('public');

        $mahasiswa = Mahasiswa::factory()->create([
            'ttd_path' => 'ttd/old.png',
        ]);
        Storage::disk('public')->put('ttd/old.png', 'old');

        $this->put(route('mahasiswa.update', $mahasiswa), array_merge($this->payload(), [
            'nim' => $mahasiswa->nim,
            'hapus_ttd' => '1',
        ]))->assertRedirect(route('mahasiswa.index'));

        $this->assertNull($mahasiswa->fresh()->ttd_path);
        Storage::disk('public')->assertMissing('ttd/old.png');
    }
}
