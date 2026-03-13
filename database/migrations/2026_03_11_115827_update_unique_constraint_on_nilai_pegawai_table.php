<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nilai_pegawai', function (Blueprint $table) {
            // Drop constraint lama (user_id, bulan, tahun)
            $table->dropUnique(['user_id', 'bulan', 'tahun']);
            
            // Tambahkan constraint baru yang mengikutsertakan penilai_id
            $table->unique(['user_id', 'penilai_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_pegawai', function (Blueprint $table) {
            // Revert ke constraint lama
            $table->dropUnique(['user_id', 'penilai_id', 'bulan', 'tahun']);
            $table->unique(['user_id', 'bulan', 'tahun']);
        });
    }
};
