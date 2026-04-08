<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CkpKipapp;

$data = CkpKipapp::selectRaw('bulan, tahun, count(*) as total')
    ->groupBy('bulan', 'tahun')
    ->get();

$out = [];
foreach ($data as $row) {
    $out[] = ["Bulan" => $row->bulan, "Tahun" => $row->tahun, "Total" => $row->total];
}

file_put_contents('ckp_out.json', json_encode($out, JSON_PRETTY_PRINT));

$periodes = \App\Models\PeriodeTahun::all();
$pOut = [];
foreach ($periodes as $p) {
    $pOut[] = ["ID" => $p->id, "Tahun" => $p->tahun, "IsActive" => $p->is_active];
}
file_put_contents('periode_out.json', json_encode($pOut, JSON_PRETTY_PRINT));
