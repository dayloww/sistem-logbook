<?php

namespace Database\Seeders;

use App\Models\KopSurat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        KopSurat::updateOrCreate(
            ['jenis' => 'kampus', 'nama_instansi' => 'Politeknik Negeri Nusa Utara'],
            [
                'nama_instansi' => 'Politeknik Negeri Nusa Utara',
                'alamat' => 'Jalan Kampung Taloarane, Taloarane 1, Manganitu 95853',
                'telepon' => '(0432) 24745',
                'website' => 'polnustar.ac.id',
            ]
        );
    }
}