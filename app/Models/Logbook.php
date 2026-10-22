<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'magang_id',
    'tanggal',
    'deskripsi_tugas',
    'target',
    'persentase_capaian',
    'kendala',
    'ttd_confirmed',
])]
class Logbook extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'persentase_capaian' => 'integer',
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
