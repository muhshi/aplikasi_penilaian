<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPegawais extends ListRecords
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('sync_sipetra')
                ->label('Sync dari Sipetra')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    \App\Jobs\SyncUsersJob::dispatch();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Sinkronisasi Dimulai')
                        ->body('Proses berjalan di latar belakang. Data pegawai & mitra akan terupdate otomatis.')
                        ->info()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
