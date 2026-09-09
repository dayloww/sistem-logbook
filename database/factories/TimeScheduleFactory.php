<?php

namespace Database\Factories;

use App\Models\Magang;
use App\Models\TimeSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeSchedule>
 */
class TimeScheduleFactory extends Factory
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
            'minggu_ke' => 1,
            'tanggal_mulai' => function (array $attributes) {
                $magang = Magang::find($attributes['magang_id']);

                return $magang ? $magang->tanggal_mulai->format('Y-m-d') : now()->toDateString();
            },
            'tanggal_selesai' => function (array $attributes) {
                $magang = Magang::find($attributes['magang_id']);

                return $magang
                    ? $magang->tanggal_mulai->copy()->addDays(6)->format('Y-m-d')
                    : now()->addDays(6)->toDateString();
            },
            'kegiatan' => fake()->sentence(3),
            'deskripsi' => fake()->optional()->sentence(),
            'target' => fake()->optional()->sentence(),
            'prioritas' => 'sedang',
            'status' => 'belum_dimulai',
            'catatan' => fake()->optional()->sentence(),
        ];
    }
}
