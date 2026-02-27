<?php

namespace App\Filament\Resources\NilaiPegawais;

use App\Filament\Resources\NilaiPegawais\Pages\CreateNilaiPegawai;
use App\Filament\Resources\NilaiPegawais\Pages\EditNilaiPegawai;
use App\Filament\Resources\NilaiPegawais\Pages\ListNilaiPegawais;
use App\Filament\Resources\NilaiPegawais\Schemas\NilaiPegawaiForm;
use App\Filament\Resources\NilaiPegawais\Tables\NilaiPegawaisTable;
use App\Models\NilaiPegawai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NilaiPegawaiResource extends Resource
{
    protected static ?string $model = NilaiPegawai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return NilaiPegawaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NilaiPegawaisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNilaiPegawais::route('/'),
            //'create' => CreateNilaiPegawai::route('/create'),
            //'edit' => EditNilaiPegawai::route('/{record}/edit'),
        ];
    }
}
