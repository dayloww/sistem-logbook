<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mahasiswa_id',
    'judul_topik',
    'instansi',
    'lokasi',
    'bagian_unit',
    'tanggal_mulai',
    'tanggal_selesai',
    'lama_kegiatan',
    'total_jam',
    'jumlah_sks',
    'dosen_pembimbing_1',
    'dosen_pembimbing_2',
    'supervisor',
    'jabatan_supervisor',
    'kop_surats_id',
])]
class Magang extends Model
{
    use HasFactory;

    protected $table = 'magang';

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function kopSurat(): BelongsTo
    {
        return $this->belongsTo(KopSurat::class, 'kop_surats_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function catatanMingguan(): HasMany
    {
        return $this->hasMany(CatatanMingguan::class);
    }

    public function jumlahMinggu(): int
    {
        return $this->tanggal_mulai->diffInWeeks($this->tanggal_selesai) + 1;
    }

    public function mingguKe(CarbonInterface|string $tanggal): int
    {
        $start = $this->tanggal_mulai->startOfDay();
        $day = is_string($tanggal) ? Carbon::parse($tanggal)->startOfDay() : $tanggal->startOfDay();
        $diff = $start->diffInDays($day);

        return intdiv($diff, 7) + 1;
    }
}
