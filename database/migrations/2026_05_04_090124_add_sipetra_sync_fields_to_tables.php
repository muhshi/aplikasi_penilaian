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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('avatar_url');
            }
            if (!Schema::hasColumn('users', 'identity_type')) {
                $table->string('identity_type')->nullable()->after('is_active');
            }
        });

        Schema::table('pegawai', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawai', 'sipetra_id')) {
                $table->string('sipetra_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('pegawai', 'nip_baru')) {
                $table->string('nip_baru', 18)->nullable()->after('nip');
            }
            if (!Schema::hasColumn('pegawai', 'sobat_id')) {
                $table->string('sobat_id', 10)->nullable()->after('nip_baru');
            }
            if (!Schema::hasColumn('pegawai', 'unit_kerja')) {
                $table->string('unit_kerja')->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('pegawai', 'kd_satker')) {
                $table->string('kd_satker', 5)->nullable()->after('unit_kerja');
            }
            if (!Schema::hasColumn('pegawai', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 1)->nullable()->after('kd_satker');
            }
            if (!Schema::hasColumn('pegawai', 'period')) {
                $table->string('period', 50)->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('pegawai', 'contract_start')) {
                $table->date('contract_start')->nullable()->after('period');
            }
            if (!Schema::hasColumn('pegawai', 'contract_end')) {
                $table->date('contract_end')->nullable()->after('contract_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'is_active', 'identity_type']);
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'sipetra_id', 
                'nip_baru', 
                'sobat_id', 
                'unit_kerja', 
                'kd_satker', 
                'jenis_kelamin', 
                'period', 
                'contract_start', 
                'contract_end'
            ]);
        });
    }
};
