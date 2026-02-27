<?php

namespace App\Filament\Resources\PeriodeTahuns;

use App\Filament\Resources\PeriodeTahuns\Pages\ManagePeriodeTahuns;
use App\Models\PeriodeTahun;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;

class PeriodeTahunResource extends Resource
{
    protected static ?string $model = PeriodeTahun::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Sistem';
    protected static ?string $pluralModelLabel = 'Pengaturan Sistem';
    protected static ?string $modelLabel = 'Periode Tahun';
    protected static \UnitEnum|string|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'tahun';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minValue(2000)
                    ->maxValue(2100),
                Toggle::make('is_active')
                    ->label('Tahun Aktif (Default)')
                    ->helperText('Hanya boleh ada 1 tahun yang aktif pada satu waktu.')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tahun')
            ->columns([
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePeriodeTahuns::route('/'),
        ];
    }
}
