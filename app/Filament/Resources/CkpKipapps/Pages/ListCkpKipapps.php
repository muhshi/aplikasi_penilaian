<?php

namespace App\Filament\Resources\CkpKipapps\Pages;

use App\Filament\Resources\CkpKipapps\CkpKipappResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCkpKipapps extends ListRecords
{
    protected static string $resource = CkpKipappResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
