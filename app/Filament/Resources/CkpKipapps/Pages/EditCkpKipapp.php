<?php

namespace App\Filament\Resources\CkpKipapps\Pages;

use App\Filament\Resources\CkpKipapps\CkpKipappResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCkpKipapp extends EditRecord
{
    protected static string $resource = CkpKipappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
