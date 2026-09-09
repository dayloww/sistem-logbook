<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'magang_id',
    'nama_kegiatan',
    'deskripsi',
    'bobot',
    'progress',
    'status',
    'tanggal_mulai',
    'tanggal_selesai',
])]
class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected function casts(): array
    {
        return [
            'bobot' => 'integer',
            'progress' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function magang(): BelongsTo
    {
        return $this->belongsTo(Magang::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'Selesai',
            'berjalan' => 'Berjalan',
            default => 'Belum Dimulai',
        };
    }
}