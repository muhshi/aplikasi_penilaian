<?php

namespace App\Filament\Resources\NilaiPegawais\Pages;

use App\Filament\Resources\NilaiPegawais\NilaiPegawaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNilaiPegawai extends EditRecord
{
    protected static string $resource = NilaiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
