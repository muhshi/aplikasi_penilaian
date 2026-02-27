<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nilai_kipapp', function (Blueprint $table) {
            // Kolom untuk menyimpan path file PDF dokumen Nilai KIPAPP
            $table->string('nama_file')->nullable()->after('nilai_prestasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_kipapp', function (Blueprint $table) {
            $table->dropColumn('nama_file');
        });
    }
};
