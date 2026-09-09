<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'magang_id',
    'tanggal',
    'jam_masuk',
    'jam_pulang',
    'keterangan',
    'ttd_confirmed',
])]
class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'ttd_confirmed' => 'boolean',
        ];
    }

    public function magang(): BelongsTo
    {
        return $this->belongsTo(Magang::class);
    }

    public function mingguKe(): int
    {
        return $this->magang->mingguKe($this->tanggal);
    }
}
