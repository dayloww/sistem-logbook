<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->string('judul_topik');
            $table->string('instansi');
            $table->string('lokasi');
            $table->string('bagian_unit')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lama_kegiatan')->nullable();
            $table->integer('total_jam')->nullable();
            $table->integer('jumlah_sks')->nullable();
            $table->string('dosen_pembimbing_1');
            $table->string('dosen_pembimbing_2')->nullable();
            $table->string('supervisor');
            $table->string('jabatan_supervisor')->nullable();
            $table->foreignId('kop_surats_id')->nullable()->constrained('kop_surats')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magang');
    }
};