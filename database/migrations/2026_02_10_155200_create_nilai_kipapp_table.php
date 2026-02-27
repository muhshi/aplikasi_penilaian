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
        Schema::create('nilai_kipapp', function (Blueprint $table) {
            $table->id();
            $table->string('nip_lama')->index();
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->decimal('rata_rata_hasil_kerja', 8, 2)->nullable();
            $table->decimal('rata_rata_perilaku', 8, 2)->nullable();
            $table->decimal('nilai_rata_rata', 8, 2)->nullable();
            $table->string('predikat_kinerja')->nullable();
            $table->decimal('nilai_prestasi', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['nip_lama', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kipapp');
    }
};
