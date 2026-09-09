<?php

namespace Database\Factories;

use App\Models\Magang;
use App\Models\Presensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
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
            'tanggal' => function (array $attributes) {
                $magang = Magang::find($attributes['magang_id']);

                return $magang
                    ? fake()->dateTimeBetween($magang->tanggal_mulai, $magang->tanggal_selesai)->format('Y-m-d')
                    : now()->toDateString();
            },
            'jam_masuk' => '08:00',
            'jam_pulang' => '17:00',
            'keterangan' => null,
            'ttd_confirmed' => false,
        ];
    }
}
