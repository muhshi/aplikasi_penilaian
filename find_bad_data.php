<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\NilaiPegawai;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Mencari data Nilai Pegawai > 100 ---\n";
$badData = NilaiPegawai::where('nilai_akhir', '>', 100)->get();

if ($badData->isEmpty()) {
    echo "Tidak ditemukan data > 100 di tabel nilai_pegawai.\n";
} else {
    echo "Ditemukan " . $badData->count() . " record:\n";
    foreach ($badData as $row) {
        echo "ID: {$row->id}, User ID: {$row->user_id}, Bulan: {$row->bulan}, Tahun: {$row->tahun}, Nilai Akhir: {$row->nilai_akhir}\n";
    }
}

echo "\n--- Mencari data Nilai KIPAPP > 100 ---\n";
$badKipapp = \App\Models\NilaiKipapp::where('nilai_rata_rata', '>', 100)->get();
if ($badKipapp->isEmpty()) {
    echo "Tidak ditemukan data > 100 di tabel nilai_kipapp.\n";
} else {
    echo "Ditemukan " . $badKipapp->count() . " record:\n";
    foreach ($badKipapp as $row) {
        echo "ID: {$row->id}, NIP: {$row->nip_lama}, Bulan: {$row->bulan}, Tahun: {$row->tahun}, Nilai Rata-rata: {$row->nilai_rata_rata}\n";
    }
}
