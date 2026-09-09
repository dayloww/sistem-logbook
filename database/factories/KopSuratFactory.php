<?php

namespace Database\Factories;

use App\Models\KopSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KopSurat>
 */
class KopSuratFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis' => fake()->randomElement(['kampus', 'instansi']),
            'nama_instansi' => fake()->company(),
            'alamat' => fake()->address(),
            'telepon' => fake()->numerify('(####) ######'),
            'website' => fake()->domainName(),
            'logo_path' => null,
        ];
    }
}
