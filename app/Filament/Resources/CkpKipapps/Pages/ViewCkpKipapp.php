<?php

namespace App\Filament\Resources\CkpKipapps\Pages;

use App\Filament\Resources\CkpKipapps\CkpKipappResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCkpKipapp extends ViewRecord
{
    protected static string $resource = CkpKipappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
