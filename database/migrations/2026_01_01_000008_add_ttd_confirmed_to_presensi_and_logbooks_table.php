<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->boolean('ttd_confirmed')->default(false)->after('keterangan');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->boolean('ttd_confirmed')->default(false)->after('kendala');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn('ttd_confirmed');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropColumn('ttd_confirmed');
        });
    }
};
