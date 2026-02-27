<?php

namespace App\Filament\Resources\PeriodeTahuns\Pages;

use App\Filament\Resources\PeriodeTahuns\PeriodeTahunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePeriodeTahuns extends ManageRecords
{
    protected static string $resource = PeriodeTahunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
