<?php

namespace App\Filament\Resources\NilaiPegawais\Pages;

use App\Filament\Resources\NilaiPegawais\NilaiPegawaiResource;
use App\Filament\Resources\NilaiPegawais\Widgets\NilaiPegawaiRekapWidget;
use App\Models\NilaiPegawai;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNilaiPegawais extends ListRecords
{
    protected static string $resource = NilaiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        // Pegawai hanya bisa melihat, tidak bisa import/export/create
        if (auth()->user()?->hasRole('pegawai')) {
            return [];
        }

        return array_filter([
            \Filament\Actions\Action::make('import')
                ->label('Import CSV')
                ->color('warning')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('File CSV/Excel')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'text/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->required()
                        ->helperText('Format: nip, bulan, tahun, kualitas, kuantitas, perilaku. Download template untuk memudahkan pengisian.'),
                ])
                ->action(function (array $data) {
                    $disk = \Illuminate\Support\Facades\Storage::disk('local');
                    $filePath = $disk->path($data['file']);

                    try {
                        \Maatwebsite\Excel\Facades\Excel::import(
                            new \App\Imports\NilaiPegawaiImport,
                            $filePath
                        );

                        Notification::make()
                            ->title('Import berhasil!')
                            ->body('Data nilai pegawai berhasil diimport.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import gagal')
                            ->body('Terjadi kesalahan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        // Hapus file temporary setelah import
                        $disk->delete($data['file']);
                    }
                }),
            \Filament\Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->color('info')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\NilaiPegawaiTemplateExport,
                        'template-nilai-pegawai.xlsx'
                    );
                }),
            \Filament\Actions\Action::make('export')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\NilaiPegawaiExport,
                        'nilai-pegawai-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
            CreateAction::make(),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NilaiPegawaiRekapWidget::class,
        ];
    }
}
