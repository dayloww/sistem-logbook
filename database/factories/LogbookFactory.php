<?php

namespace Database\Factories;

use App\Models\Logbook;
use App\Models\Magang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Logbook>
 */
class LogbookFactory extends Factory
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
            'task_id' => null,
            'tanggal' => function (array $attributes) {
                $magang = Magang::find($attributes['magang_id']);

                return $magang
                    ? fake()->dateTimeBetween($magang->tanggal_mulai, $magang->tanggal_selesai)->format('Y-m-d')
                    : now()->toDateString();
            },
            'deskripsi_tugas' => fake()->sentence(),
            'target' => fake()->sentence(),
            'persentase_capaian' => fake()->numberBetween(0, 100),
            'kendala' => fake()->optional()->sentence(),
            'ttd_confirmed' => false,
        ];
    }
}
