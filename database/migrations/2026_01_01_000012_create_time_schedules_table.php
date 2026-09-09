<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magang')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->unique()->constrained('tasks')->nullOnDelete();
            $table->unsignedSmallInteger('minggu_ke');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('kegiatan');
            $table->text('deskripsi')->nullable();
            $table->text('target')->nullable();
            $table->string('prioritas')->default('sedang');
            $table->string('status')->default('belum_dimulai');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['magang_id', 'status']);
            $table->index(['magang_id', 'tanggal_mulai']);
            $table->index(['magang_id', 'minggu_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_schedules');
    }
};
