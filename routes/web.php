<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// Route khusus untuk preview PDF agar aman dan tidak terblokir 403 Forbidden oleh server config
Route::get('/preview/{path}', function (string $path, \Illuminate\Http\Request $request) {
    // Cegah path traversal: pastikan file valid dan berada di dalam storage/app/public
    $basePath = realpath(storage_path('app/public'));
    $targetPath = realpath(storage_path('app/public/' . ltrim($path, '/\\')));

    if (! $basePath || ! $targetPath || ! str_starts_with($targetPath, $basePath) || ! file_exists($targetPath)) {
        abort(404, 'File tidak ditemukan.');
    }

    // Jika ada query ?name=..., gunakan sebagai nama tampilan file di browser
    $displayName = $request->query('name');
    if ($displayName) {
        return response()->file($targetPath, [
            'Content-Disposition' => 'inline; filename="' . basename($displayName) . '"',
        ]);
    }

    return response()->file($targetPath);
})->where('path', '.*')->middleware('auth')->name('file.preview');

Route::get('/download/template-nilai-pegawai', function () {
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\NilaiPegawaiTemplateExport,
        'template-nilai-pegawai.xlsx'
    );
})->name('download.template.nilai_pegawai');
 
use App\Http\Controllers\Auth\SsoController;
Route::get('/auth/sipetra/redirect', [SsoController::class, 'redirect'])->name('sipetra.login');
Route::get('/auth/sipetra/callback', [SsoController::class, 'callback']);

// Define default login route for Laravel middleware & SsoController redirects
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

