<?php

namespace App\Filament\Resources\NilaiPegawais;

use App\Filament\Resources\NilaiPegawais\Pages\CreateNilaiPegawai;
use App\Filament\Resources\NilaiPegawais\Pages\EditNilaiPegawai;
use App\Filament\Resources\NilaiPegawais\Pages\ListNilaiPegawais;
use App\Filament\Resources\NilaiPegawais\Schemas\NilaiPegawaiForm;
use App\Filament\Resources\NilaiPegawais\Tables\NilaiPegawaisTable;
use App\Filament\Resources\NilaiPegawais\Widgets\NilaiPegawaiRekapWidget;
use App\Models\NilaiPegawai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    public static function getWidgets(): array
    {
        $user = auth()->user();

        // Sembunyikan widget rekap untuk pegawai (karena akan ditampilkan di tabel utama yang sudah diringkas)
        if ($user?->hasRole('pegawai')) {
            return [];
        }

        return [
            NilaiPegawaiRekapWidget::class,
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

        // 2. Ketua Tim hanya melihat data di mana mereka adalah penilainya
        if ($user?->hasRole('ketua_tim')) {
            return $query->where('penilai_id', $user->id);
        }

        // 3. Pegawai hanya bisa melihat rekap nilai milik mereka sendiri (Agregat)
        if ($user?->hasRole('pegawai')) {
            return $query->where('user_id', $user->id)
                ->select([
                    'user_id',
                    'bulan',
                    'tahun',
                    \Illuminate\Support\Facades\DB::raw('MAX(id) as id'), // Dummy ID for Filament
                    \Illuminate\Support\Facades\DB::raw('AVG(kualitas) as kualitas'),
                    \Illuminate\Support\Facades\DB::raw('AVG(kuantitas) as kuantitas'),
                    \Illuminate\Support\Facades\DB::raw('AVG(perilaku) as perilaku'),
                    \Illuminate\Support\Facades\DB::raw('AVG(nilai_akhir) as nilai_akhir'),
                ])
                ->groupBy('user_id', 'bulan', 'tahun');
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNilaiPegawais::route('/'),
            'create' => CreateNilaiPegawai::route('/create'),
            'edit' => EditNilaiPegawai::route('/{record}/edit'),
        ];
    }
}

