<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nim' => fake()->unique()->numerify('##########'),
            'nama' => fake()->name(),
            'program_studi' => fake()->randomElement(['D4 Teknik Informatika', 'D3 Teknik Informatika']),
            'jurusan' => 'Teknologi Informatika',
            'perguruan_tinggi' => 'Politeknik Negeri Nusa Utara',
            'ttd_path' => null,
        ];
    }
}
