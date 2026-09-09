<?php

namespace Database\Factories;

use App\Models\Magang;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
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
            'nama_kegiatan' => fake()->sentence(3),
            'deskripsi' => fake()->optional()->sentence(),
            'bobot' => fake()->numberBetween(0, 30),
            'progress' => fake()->numberBetween(0, 100),
            'status' => 'berjalan',
            'tanggal_mulai' => function (array $attributes) {
                $magang = Magang::find($attributes['magang_id']);

                return $magang ? $magang->tanggal_mulai->format('Y-m-d') : now()->toDateString();
            },
            'tanggal_selesai' => function (array $attributes) {
                $magang = Magang::find($attributes['magang_id']);

                return $magang ? $magang->tanggal_selesai->format('Y-m-d') : now()->toDateString();
            },
        ];
    }
}