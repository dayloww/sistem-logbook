<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'magang_id',
    'task_id',
    'minggu_ke',
    'tanggal_mulai',
    'tanggal_selesai',
    'kegiatan',
    'deskripsi',
    'target',
    'prioritas',
    'status',
    'catatan',
])]
class TimeSchedule extends Model
{
    use HasFactory;

    protected $table = 'time_schedules';

    protected function casts(): array
    {
        return [
            'minggu_ke' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function magang(): BelongsTo
    {
        return $this->belongsTo(Magang::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeActiveOn(Builder $query, string $tanggal): Builder
    {
        return $query
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'berjalan' => 'Berjalan',
            'selesai' => 'Selesai',
            'ditunda' => 'Ditunda',
            default => 'Belum Dimulai',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'berjalan' => 'badge-info',
            'selesai' => 'badge-success',
            'ditunda' => 'badge-warning',
            default => 'badge-gray',
        };
    }

    public function getPrioritasLabelAttribute(): string
    {
        return match ($this->prioritas) {
            'tinggi' => 'Tinggi',
            'rendah' => 'Rendah',
            default => 'Sedang',
        };
    }
}
