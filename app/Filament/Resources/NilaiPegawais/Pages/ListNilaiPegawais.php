<?php

namespace App\Filament\Resources\NilaiPegawais\Pages;

use App\Filament\Resources\NilaiPegawais\NilaiPegawaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNilaiPegawais extends ListRecords
{
    protected static string $resource = NilaiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
        ];
    }
}
