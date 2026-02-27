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
        return [
            /**
             * Action untuk import data Nilai KIPAPP dari file Excel
             * 
             * Fitur:
             * - Upload file Excel/CSV
             * - Validasi format file
             * - Tahun dibaca langsung dari kolom "Tahun" di file Excel
             * - Import data menggunakan NilaiKipappImport class
             * - Notifikasi sukses/gagal
             */
            Action::make('import')
                ->label('Import Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->disk('local') // Simpan file upload ke disk 'local'
                        // Hanya terima file Excel (.xlsx, .xls) dan CSV
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        // Tambah batas waktu eksekusi untuk file Excel besar (5 menit)
                        set_time_limit(300);

                        // Import file Excel menggunakan NilaiKipappImport
                        // Tahun sekarang dibaca langsung dari kolom "Tahun" di Excel
                        $path = storage_path('app/private/' . $data['file']);
                        Excel::import(new NilaiKipappImport(), $path);

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
                    }
                }),
            Action::make('export')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(
                        new \App\Exports\NilaiKipappExport,
                        'nilai-kipapp-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
            CreateAction::make(),
        ];
    }
}
