<?php

namespace App\Filament\Resources\NilaiKipapps;

use App\Filament\Resources\NilaiKipapps\Pages\CreateNilaiKipapp;
use App\Filament\Resources\NilaiKipapps\Pages\EditNilaiKipapp;
use App\Filament\Resources\NilaiKipapps\Pages\ListNilaiKipapps;
use App\Filament\Resources\NilaiKipapps\Schemas\NilaiKipappForm;
use App\Filament\Resources\NilaiKipapps\Tables\NilaiKipappsTable;
use App\Models\NilaiKipapp;
use App\Models\Pegawai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Super Admin bisa melihat semua data
        if ($user?->hasRole('super_admin')) {
            return $query;
        }

        // 2. Ketua Tim hanya melihat data pegawai yang dibimbingnya
        if ($user?->hasRole('ketua_tim')) {
            return $query->whereHas('pegawai', function ($q) use ($user) {
                $q->where('penilai_id', $user->id);
            });
        }

        // 3. Pegawai hanya bisa melihat nilai KIPAPP milik mereka sendiri
        if ($user?->hasRole('pegawai')) {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                $query->where('nip_lama', $pegawai->nip_lama);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
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
