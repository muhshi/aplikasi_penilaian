<?php

namespace App\Filament\Resources\NilaiKipapps;

use App\Filament\Resources\NilaiKipapps\Pages\CreateNilaiKipapp;
use App\Filament\Resources\NilaiKipapps\Pages\EditNilaiKipapp;
use App\Filament\Resources\NilaiKipapps\Pages\ListNilaiKipapps;
use App\Filament\Resources\NilaiKipapps\Schemas\NilaiKipappForm;
use App\Filament\Resources\NilaiKipapps\Tables\NilaiKipappsTable;
use App\Models\NilaiKipapp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NilaiKipappResource extends Resource
{
    protected static ?string $model = NilaiKipapp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return NilaiKipappForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NilaiKipappsTable::configure($table);
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
            'index' => ListNilaiKipapps::route('/'),
            'create' => CreateNilaiKipapp::route('/create'),
            'edit' => EditNilaiKipapp::route('/{record}/edit'),
        ];
    }
}
