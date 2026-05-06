<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('erd-generate', function () {
    $this->info('Generating Interactive Web ERD...');
    $this->call('erd:generate');
})->purpose('Generate interactive web ERD');
