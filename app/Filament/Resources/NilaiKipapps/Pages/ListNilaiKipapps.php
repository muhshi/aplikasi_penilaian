<?php

namespace App\Filament\Resources\NilaiKipapps\Pages;

use App\Filament\Resources\NilaiKipapps\NilaiKipappResource;
use App\Imports\NilaiKipappImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListNilaiKipapps extends ListRecords
{
    protected static string $resource = NilaiKipappResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        return array_filter([
            $user?->hasRole('super_admin') ? Action::make('import')
                ->label('Import Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->disk('local')
                        ->directory('imports')
                        // Hanya terima file Excel (.xlsx, .xls) dan CSV
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $disk = \Illuminate\Support\Facades\Storage::disk('local');
                    $filePath = $disk->path($data['file']);

                    try {
                        // Tambah batas waktu eksekusi untuk file Excel besar (5 menit)
                        set_time_limit(300);

                        // Import file Excel menggunakan NilaiKipappImport
                        // Tahun sekarang dibaca langsung dari kolom "Tahun" di Excel
                        Excel::import(new NilaiKipappImport(), $filePath);

                        // Tampilkan notifikasi sukses
                        Notification::make()
                            ->title('Import Berhasil')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Tampilkan notifikasi error jika import gagal
                        Notification::make()
                            ->title('Import Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        // Hapus file temporary setelah import
                        $disk->delete($data['file']);
                    }
                }) : null,
            Action::make('export')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn() => !$user?->hasRole('pegawai'))
                ->action(function () {
                    return Excel::download(
                        new \App\Exports\NilaiKipappExport,
                        'nilai-kipapp-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
            CreateAction::make()->visible(fn() => !$user?->hasRole('pegawai')),
        ]);
    }
}
