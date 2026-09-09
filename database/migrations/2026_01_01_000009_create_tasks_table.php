<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magang')->cascadeOnDelete();
            $table->string('nama_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('bobot')->default(0);
            $table->unsignedSmallInteger('progress')->default(0);
            $table->string('status')->default('belum_dimulai');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->index(['magang_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};