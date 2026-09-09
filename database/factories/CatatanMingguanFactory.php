<?php

namespace Database\Factories;

use App\Models\CatatanMingguan;
use App\Models\Magang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatatanMingguan>
 */
class CatatanMingguanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'magang_id' => Magang::factory(),
            'minggu_ke' => 1,
            'catatan' => fake()->sentence(),
        ];
    }
}
