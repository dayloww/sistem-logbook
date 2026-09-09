<?php

namespace Database\Factories;

use App\Models\Magang;
use App\Models\WeeklyProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeeklyProgress>
 */
class WeeklyProgressFactory extends Factory
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
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(6)->toDateString(),
            'progress_awal' => 0,
            'progress_akhir' => 0,
            'perkembangan' => 0,
        ];
    }
}