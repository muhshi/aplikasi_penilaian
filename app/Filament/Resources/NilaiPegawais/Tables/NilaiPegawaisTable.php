<?php

namespace App\Filament\Resources\NilaiPegawais\Tables;

use App\Models\NilaiPegawai;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class NilaiPegawaisTable
{
    public static function configure(Table $table): Table
    {
        $totalPenilai = User::role('ketua_tim')->count();

        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('penilai.name')
                    ->label('Penilai')
                    ->searchable()
                    ->sortable()
                    ->default('-')
                    ->visible(fn() => !auth()->user()?->hasRole('pegawai')),
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
                TextColumn::make('kualitas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kuantitas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('perilaku')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nilai_akhir')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('progres_penilai')
                    ->label('Progres')
                    ->getStateUsing(function ($record) use ($totalPenilai) {
                        $jumlah = NilaiPegawai::where('user_id', $record->user_id)
                            ->where('bulan', $record->bulan)
                            ->where('tahun', $record->tahun)
                            ->distinct('penilai_id')
                            ->count('penilai_id');
                        return $jumlah . '/' . $totalPenilai;
                    })
                    ->badge()
                    ->color(function ($record) use ($totalPenilai) {
                        $jumlah = NilaiPegawai::where('user_id', $record->user_id)
                            ->where('bulan', $record->bulan)
                            ->where('tahun', $record->tahun)
                            ->distinct('penilai_id')
                            ->count('penilai_id');

                        if ($jumlah >= $totalPenilai) return 'success';
                        if ($jumlah > 0) return 'warning';
                        return 'danger';
                    }),
                TextColumn::make('status_final')
                    ->label('Status')
                    ->getStateUsing(function ($record) use ($totalPenilai) {
                        $jumlah = NilaiPegawai::where('user_id', $record->user_id)
                            ->where('bulan', $record->bulan)
                            ->where('tahun', $record->tahun)
                            ->distinct('penilai_id')
                            ->count('penilai_id');
                        return $jumlah >= $totalPenilai ? 'Lengkap' : 'Belum Lengkap';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lengkap' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('tahun')
                    ->label('Tahun')
                    ->collapsible(),
                Group::make('bulan')
                    ->label('Bulan')
                    ->getTitleFromRecordUsing(fn($record): string => match ((int) $record->bulan) {
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
                        default => (string) $record->bulan,
                    })
                    ->collapsible(),
            ])
            ->defaultSort('user.name')
            ->filters([
                //
            ])
            ->actions(
                auth()->user()?->hasAnyRole(['super_admin', 'ketua_tim']) ? [
                    EditAction::make(),
                ] : []
            )
            ->bulkActions(
                auth()->user()?->hasAnyRole(['super_admin', 'ketua_tim']) ? [
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ] : []
            );
    }
}
