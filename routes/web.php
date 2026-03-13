<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// Route khusus untuk preview PDF agar tidak terblokir 403 Forbidden oleh server config
Route::get('/preview/{path}', function (string $path) {
    // Pastikan path merujuk ke storage/app/public
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404, 'File tidak ditemukan.');
    }

    return response()->file($fullPath);
})->where('path', '.*')->name('file.preview');

Route::get('/download/template-nilai-pegawai', function () {
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\NilaiPegawaiTemplateExport,
        'template-nilai-pegawai.xlsx'
    );
})->name('download.template.nilai_pegawai');
