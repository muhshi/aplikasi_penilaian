<?php

namespace App\Filament\Resources\CkpKipapps\Pages;

use App\Filament\Resources\CkpKipapps\CkpKipappResource;
use App\Imports\LapkinBulkImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListCkpKipapps extends ListRecords
{
    protected static string $resource = CkpKipappResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        return array_filter([
            $user?->hasRole('super_admin') ? Action::make('bulkImport')
                ->label('Bulk Import GDrive')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('info')
                ->form([
                    FileUpload::make('attachment')
                        ->label('File Excel (xlsx)')
                        ->required()
                        ->disk('local')
                        ->directory('temp-imports')
                ])
                ->action(function (array $data) {
                    $file = Storage::disk('local')->path($data['attachment']);
                    
                    if (!file_exists($file)) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('File tidak ditemukan di server.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Excel::import(new LapkinBulkImport, $file);
                    
                    Notification::make()
                        ->title('Proses Import Dimulai')
                        ->body('Sistem sedang memproses data dan mendownload file dari Google Drive di latar belakang.')
                        ->success()
                        ->send();
                }) : null,
            CreateAction::make(),
        ]);
    }
}
