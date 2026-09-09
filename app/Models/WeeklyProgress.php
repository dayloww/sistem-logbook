<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'magang_id',
    'minggu_ke',
    'tanggal_mulai',
    'tanggal_selesai',
    'progress_awal',
    'progress_akhir',
    'perkembangan',
])]
class WeeklyProgress extends Model
{
    use HasFactory;

    protected $table = 'weekly_progresses';

    protected function casts(): array
    {
        return [
            'minggu_ke' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'progress_awal' => 'float',
            'progress_akhir' => 'float',
            'perkembangan' => 'float',
        ];
    }

    public function magang(): BelongsTo
    {
        return $this->belongsTo(Magang::class);
    }
}