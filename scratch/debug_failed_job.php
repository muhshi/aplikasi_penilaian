<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$failed = \Illuminate\Support\Facades\DB::table('failed_jobs')->latest()->first();
if ($failed) {
    echo "ID: " . $failed->id . "\n";
    echo "Exception:\n" . $failed->exception . "\n";
} else {
    echo "No failed jobs found.\n";
}
