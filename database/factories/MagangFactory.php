<?php

namespace Database\Factories;

use App\Models\Magang;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Magang>
 */
class MagangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'judul_topik' => fake()->sentence(3),
            'instansi' => fake()->company(),
            'lokasi' => fake()->city(),
            'bagian_unit' => fake()->randomElement(['IT', 'HRD', 'Keuangan']),
            'tanggal_mulai' => now()->subDays(30)->toDateString(),
            'tanggal_selesai' => now()->addDays(60)->toDateString(),
            'lama_kegiatan' => '3 Bulan',
            'total_jam' => fake()->numberBetween(300, 720),
            'jumlah_sks' => fake()->numberBetween(15, 25),
            'dosen_pembimbing_1' => fake()->name(),
            'dosen_pembimbing_2' => fake()->name(),
            'supervisor' => fake()->name(),
            'jabatan_supervisor' => fake()->jobTitle(),
            'kop_surats_id' => null,
        ];
    }
}
