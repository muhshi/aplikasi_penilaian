<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\NilaiPegawai;
use App\Models\CkpKipapp;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Checking NilaiPegawai for April (4) ---\n";
$nilaiApril = NilaiPegawai::where('bulan', 4)->get();
echo "Count: " . $nilaiApril->count() . "\n";
foreach ($nilaiApril as $n) {
    echo "User ID: {$n->user_id}, Nilai Akhir: {$n->nilai_akhir}\n";
}
echo "Avg Nilai: " . $nilaiApril->avg('nilai_akhir') . "\n";

echo "\n--- Checking CkpKipapp for Januari (1) ---\n";
$ckpJan = CkpKipapp::where('bulan', 1)->count();
$ckpJanStr = CkpKipapp::where('bulan', 'Januari')->count();
echo "Count with bulan=1: $ckpJan\n";
echo "Count with bulan='Januari': $ckpJanStr\n";

echo "\n--- Sample CkpKipapp record ---\n";
$sample = CkpKipapp::first();
if ($sample) {
    echo "Bulan: " . var_export($sample->bulan, true) . " (Type: " . gettype($sample->bulan) . ")\n";
} else {
    echo "No records found in ckp_kipapp\n";
}
