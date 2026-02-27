<?php

namespace App\Filament\Resources\NilaiKipapps\Pages;

use App\Filament\Resources\NilaiKipapps\NilaiKipappResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNilaiKipapp extends EditRecord
{
    protected static string $resource = NilaiKipappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
