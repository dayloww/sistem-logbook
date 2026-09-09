<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'magang_id',
    'minggu_ke',
    'catatan',
])]
class CatatanMingguan extends Model
{
    use HasFactory;

    protected $table = 'catatan_mingguan';

    protected function casts(): array
    {
        return [
            'minggu_ke' => 'integer',
        ];
    }

    public function magang(): BelongsTo
    {
        return $this->belongsTo(Magang::class);
    }
}
