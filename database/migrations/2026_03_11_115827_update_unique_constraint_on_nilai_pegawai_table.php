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
            // Drop foreign key dulu karena MySQL butuh index untuk foreign key
            $table->dropForeign(['user_id']);

            // Drop constraint lama (user_id, bulan, tahun)
            $table->dropUnique(['user_id', 'bulan', 'tahun']);
            
            // Tambahkan constraint baru yang mengikutsertakan penilai_id
            $table->unique(['user_id', 'penilai_id', 'bulan', 'tahun']);

            // Buat ulang foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_pegawai', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['user_id']);

            // Revert ke constraint lama
            $table->dropUnique(['user_id', 'penilai_id', 'bulan', 'tahun']);
            $table->unique(['user_id', 'bulan', 'tahun']);

            // Buat ulang foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
