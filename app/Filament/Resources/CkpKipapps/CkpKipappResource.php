<?php

namespace App\Filament\Resources\CkpKipapps;

use App\Filament\Resources\CkpKipapps\Pages\CreateCkpKipapp;
use App\Filament\Resources\CkpKipapps\Pages\EditCkpKipapp;
use App\Filament\Resources\CkpKipapps\Pages\ListCkpKipapps;
use App\Filament\Resources\CkpKipapps\Pages\ViewCkpKipapp;
use App\Filament\Resources\CkpKipapps\Schemas\CkpKipappForm;
use App\Filament\Resources\CkpKipapps\Tables\CkpKipappsTable;
use App\Models\CkpKipapp;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class CkpKipappResource extends Resource
{
    protected static ?string $model = CkpKipapp::class;

    protected static ?string $modelLabel = 'Ckp Kipapp';

    protected static ?string $pluralModelLabel = 'Ckp Kipapp';

    protected static ?string $navigationLabel = 'Ckp Kipapp';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 2;


    public static function form(Schema $schema): Schema
    {
        return CkpKipappForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CkpKipappsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Laporan CKP')
                    ->icon('heroicon-m-document-check')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nama Pegawai'),
                                TextEntry::make('bulan')
                                    ->label('Periode Bulan'),
                                TextEntry::make('tahun')
                                    ->label('Tahun'),
                            ]),
                    ]),

                Section::make('Pratinjau Dokumen')
                    ->icon('heroicon-m-eye')
                    ->schema([
                        TextEntry::make('nama_file')
                            ->label('Buka File Langsung')
                            ->formatStateUsing(fn() => 'Klik untuk Membuka PDF')
                            ->url(fn($record) => asset('storage/' . $record->nama_file), true)
                            ->color('primary')
                            ->icon('heroicon-m-arrow-top-right-on-square'),
                        PdfViewerEntry::make('nama_file')
                            ->label('Dokumen Terlampir')
                            ->minHeight('1000px')
                            ->fileUrl(fn($record) => '/storage/' . $record->nama_file),
                    ]),
            ]);
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
            'index' => ListCkpKipapps::route('/'),
            'create' => CreateCkpKipapp::route('/create'),
            'view' => ViewCkpKipapp::route('/{record}'),
            'edit' => EditCkpKipapp::route('/{record}/edit'),
        ];
    }
}
