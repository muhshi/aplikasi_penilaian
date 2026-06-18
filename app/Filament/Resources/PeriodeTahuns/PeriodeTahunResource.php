<?php

namespace App\Filament\Resources\PeriodeTahuns;

use App\Filament\Resources\PeriodeTahuns\Pages\ManagePeriodeTahuns;
use App\Models\PeriodeTahun;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                Select::make('periode_aktif')
                    ->label('Periode Aktif')
                    ->multiple()
                    ->options(fn () => \App\Models\MasterPeriode::pluck('nama', 'nama')->toArray())
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('nama')
                            ->label('Nama Periode Baru')
                            ->required()
                            ->rule('unique:master_periodes,nama')
                    ])
                    ->createOptionUsing(function (array $data) {
                        $record = \App\Models\MasterPeriode::create($data);
                        return $record->nama;
                    })
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
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
                
                TextColumn::make('periode_aktif')
                    ->label('Isian Per Tahun (Periode)')
                    ->badge()
                    ->separator(', ')
                    ->action(
                        \Filament\Tables\Actions\Action::make('editPeriode')
                            ->modalHeading('Edit Periode Aktif')
                            ->form([
                                Select::make('periode_aktif')
                                    ->label('Periode Aktif')
                                    ->multiple()
                                    ->options(fn () => \App\Models\MasterPeriode::pluck('nama', 'nama')->toArray())
                                    ->createOptionForm([
                                        \Filament\Forms\Components\TextInput::make('nama')
                                            ->label('Nama Periode Baru')
                                            ->required()
                                            ->rule('unique:master_periodes,nama')
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $record = \App\Models\MasterPeriode::create($data);
                                        return $record->nama;
                                    })
                                    ->searchable()
                                    ->required()
                            ])
                            ->action(function (\App\Models\PeriodeTahun $record, array $data): void {
                                $record->update(['periode_aktif' => $data['periode_aktif']]);
                            })
                    )
                    ->tooltip('Klik untuk mengedit periode'),

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
