<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_mingguan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magang')->cascadeOnDelete();
            $table->integer('minggu_ke');
            $table->text('catatan');
            $table->timestamps();

            $table->unique(['magang_id', 'minggu_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_mingguan');
    }
};