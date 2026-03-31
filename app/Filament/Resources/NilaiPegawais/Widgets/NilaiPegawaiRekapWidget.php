<?php

namespace App\Filament\Resources\NilaiPegawais\Widgets;

use App\Models\NilaiPegawai;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NilaiPegawaiRekapWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Rekap Rata-rata Nilai Pegawai';

    public function getTableRecordKey(Model|array $record): string
    {
        return $record->user_id . '-' . $record->bulan . '-' . $record->tahun;
    }

    /**
     * Hitung jumlah total ketua tim (penilai yang diharapkan).
     */
    private static function getTotalPenilai(): int
    {
        return User::role('ketua_tim')->count();
    }

    public function table(Table $table): Table
    {
        $totalPenilai = self::getTotalPenilai();

        $query = NilaiPegawai::query()
            ->fromSub(function ($query) {
                $query->from('nilai_pegawai')
                    ->select(
                        DB::raw('MAX(id) as id'),
                        'user_id',
                        'bulan',
                        'tahun',
                        DB::raw('ROUND(AVG(kualitas), 2) as avg_kualitas'),
                        DB::raw('ROUND(AVG(kuantitas), 2) as avg_kuantitas'),
                        DB::raw('ROUND(AVG(perilaku), 2) as avg_perilaku'),
                        DB::raw('ROUND(AVG(nilai_akhir), 2) as avg_nilai_akhir'),
                        DB::raw('COUNT(DISTINCT penilai_id) as jumlah_penilai')
                    )
                    ->groupBy('user_id', 'bulan', 'tahun');
            }, 'nilai_pegawai');

        // Pegawai hanya bisa melihat rekap nilai milik mereka sendiri
        if (auth()->user()?->hasRole('pegawai')) {
            $query->where('user_id', auth()->id());
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bulan')
                    ->formatStateUsing(fn(int $state): string => match ($state) {
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
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('tahun')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('avg_kualitas')
                    ->label('Rata-rata Kualitas')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('avg_kuantitas')
                    ->label('Rata-rata Kuantitas')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('avg_perilaku')
                    ->label('Rata-rata Perilaku')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('avg_nilai_akhir')
                    ->label('Rata-rata Nilai Akhir')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('jumlah_penilai')
                    ->label('Progres')
                    ->formatStateUsing(fn($state) => $state . '/' . $totalPenilai)
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= $totalPenilai => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('status_finalisasi')
                    ->label('Status')
                    ->getStateUsing(function (Model $record) use ($totalPenilai) {
                        return $record->jumlah_penilai >= $totalPenilai ? 'Lengkap' : 'Belum Lengkap';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lengkap' => 'success',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->options(function () {
                        return \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray();
                    })
                    ->default(function () {
                        $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
                        return $active ? $active->tahun : null;
                    }),
                SelectFilter::make('bulan')
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
                    ]),
            ])
            ->defaultSort('user.name')
            ->paginated([5, 10, 25]);
    }
}
