<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CkpKipapp;

echo "Daftar bulan di CkpKipapp dan jumlahnya:\n";
$data = CkpKipapp::selectRaw('bulan, tahun, count(*) as total')
    ->groupBy('bulan', 'tahun')
    ->get();

foreach ($data as $row) {
    echo "Bulan: '{$row->bulan}', Tahun: {$row->tahun}, Total: {$row->total}\n";
}

echo "\nPeriodeTahun data:\n";
$periodes = \App\Models\PeriodeTahun::all();
foreach ($periodes as $p) {
    echo "ID: {$p->id}, Tahun: {$p->tahun}, IsActive: {$p->is_active}\n";
}
