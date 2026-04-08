<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CkpKipapp;
use App\Models\User;

$output = "=== ALL CKP UPLOADS ===\n";
$allCkp = CkpKipapp::with('user')->get();
foreach ($allCkp as $ckp) {
    $output .= "User ID: {$ckp->user_id} ({$ckp->user?->name}) | Bulan: '{$ckp->bulan}' | Tahun: {$ckp->tahun}\n";
}

$output .= "\n=== TESTING CURRENT WIDGET LOGIC (Bulan: 1, Tahun: 2026) ===\n";
$bulan = 1;
$tahun = 2026;
$bulanMap = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
    4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
$namaBulan = $bulanMap[$bulan];

$output .= "Target: '{$namaBulan}' (from int {$bulan}) in {$tahun}\n";

$users = User::whereHas('pegawai')->get();
foreach ($users as $user) {
    $hasCkp = $user->ckpKipapps()->where('bulan', $namaBulan)->where('tahun', $tahun)->exists();
    if ($hasCkp) {
        $output .= "[OK] {$user->name} HAS CKP\n";
    } else {
        // Log what they DO have
        $other = $user->ckpKipapps()->pluck('bulan')->toArray();
        $output .= "[MISSING] {$user->name} (Available: " . implode(', ', $other) . ")\n";
    }
}

file_put_contents(__DIR__ . '/debug_ckp.txt', $output);
echo "Done!";
