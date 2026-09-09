<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nim',
    'nama',
    'program_studi',
    'jurusan',
    'perguruan_tinggi',
    'ttd_path',
])]
class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    public function magang(): HasMany
    {
        return $this->hasMany(Magang::class);
    }
}
