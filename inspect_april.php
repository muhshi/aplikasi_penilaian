<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\NilaiPegawai;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bulan = 4;
$tahun = 2026;

echo "--- Data Nilai Pegawai April 2026 ---\n";
$data = NilaiPegawai::where('bulan', $bulan)->where('tahun', $tahun)->get();
echo "Total Records: " . $data->count() . "\n";
foreach ($data as $row) {
    echo "ID: {$row->id}, Nilai: {$row->nilai_akhir} (Raw: " . var_export($row->getRawOriginal('nilai_akhir'), true) . ")\n";
}

$avg = NilaiPegawai::where('bulan', $bulan)->where('tahun', $tahun)->avg('nilai_akhir');
echo "Average: " . $avg . "\n";
