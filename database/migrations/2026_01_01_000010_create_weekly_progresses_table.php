<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magang')->cascadeOnDelete();
            $table->unsignedSmallInteger('minggu_ke');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('progress_awal', 5, 1)->default(0);
            $table->decimal('progress_akhir', 5, 1)->default(0);
            $table->decimal('perkembangan', 5, 1)->default(0);
            $table->timestamps();

            $table->unique(['magang_id', 'minggu_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_progresses');
    }
};