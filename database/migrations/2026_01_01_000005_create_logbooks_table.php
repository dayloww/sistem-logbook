<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magang')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('deskripsi_tugas');
            $table->text('target')->nullable();
            $table->integer('persentase_capaian')->default(0);
            $table->text('kendala')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};