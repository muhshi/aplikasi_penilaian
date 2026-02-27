<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Carbon;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1) // Root schema 1 kolom
            ->schema([
                Grid::make(2) // Grid internal 2 kolom untuk rasio 1:1
                    ->schema([
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                return \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray();
                            })
                            ->default(function () {
                                $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
                                return $active ? $active->tahun : Carbon::now()->year;
                            })
                            ->searchable()
                            ->extraInputAttributes(['class' => '!rounded-lg !bg-gray-50 !border-gray-200 !shadow-sm !text-center !p-2.5 focus:!ring-1 focus:!ring-primary-500']),

                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])
                            ->default(Carbon::now()->month)
                            ->extraInputAttributes(['class' => '!rounded-lg !bg-gray-50 !border-gray-200 !shadow-sm !text-center !p-2.5 focus:!ring-1 focus:!ring-primary-500']),
                    ])
                    ->columnSpan('full') // Paksa grid mengisi seluruh lebar
                    ->extraAttributes([
                        'style' => 'width: 100% !important; max-width: none !important;',
                    ]),
            ])
            ->extraAttributes([
                'class' => 'w-full mb-6',
                'style' => 'width: 100% !important; max-width: none !important; display: block;'
            ]);
    }
}
