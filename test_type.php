<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "\nTYPE: " . \Illuminate\Support\Facades\Schema::getColumnType('nilai_pegawai', 'bulan') . "\n";
$data = \Illuminate\Support\Facades\DB::table('nilai_pegawai')->take(1)->get();
echo "DATA: " . json_encode($data) . "\n";
