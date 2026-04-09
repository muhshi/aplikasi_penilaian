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
        $user = auth()->user();

        return array_filter([
            $user?->hasRole('super_admin') ? \Filament\Actions\Action::make('import')
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
                        ->required(),
                        //->helperText('Format: nip, bulan, tahun, kualitas, kuantitas, perilaku.'),
                    \Filament\Forms\Components\Placeholder::make('download_template')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="font-size: 0.875rem; color: rgb(107 114 128);">
                                Download template: 
                                <a href="' . route('download.template.nilai_pegawai') . '" 
                                   style="color: rgb(59 130 246); text-decoration: none;"
                                   onmouseover="this.style.textDecoration=\'underline\'"
                                   onmouseout="this.style.textDecoration=\'none\'">
                                    template-nilai-pegawai.xlsx
                                </a>
                            </div>
                        ')),
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
                }) : null,
            \Filament\Actions\Action::make('export')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn() => !$user?->hasRole('pegawai'))
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\NilaiPegawaiExport,
                        'nilai-pegawai-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
            CreateAction::make()->visible(fn() => !$user?->hasRole('pegawai')),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NilaiPegawaiRekapWidget::class,
        ];
    }
}
