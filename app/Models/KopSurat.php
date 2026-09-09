<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'jenis',
    'nama_instansi',
    'alamat',
    'telepon',
    'website',
    'logo_path',
])]
class KopSurat extends Model
{
    use HasFactory;

    public function magang(): HasMany
    {
        return $this->hasMany(Magang::class);
    }
}
