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
            $table->string('bulan')->change();
        });

        Schema::table('nilai_kipapp', function (Blueprint $table) {
            $table->string('bulan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_pegawai', function (Blueprint $table) {
            $table->tinyInteger('bulan')->change();
        });

        Schema::table('nilai_kipapp', function (Blueprint $table) {
            $table->tinyInteger('bulan')->change();
        });
    }
};
