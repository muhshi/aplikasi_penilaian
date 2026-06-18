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
                    ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'ketua_tim'])),
                TextColumn::make('bulan')
                    ->label('Periode')
                    ->formatStateUsing(fn($state): string => match ((string) $state) {
                        '1', '01' => 'Januari',
                        '2', '02' => 'Februari',
                        '3', '03' => 'Maret',
                        '4', '04' => 'April',
                        '5', '05' => 'Mei',
                        '6', '06' => 'Juni',
                        '7', '07' => 'Juli',
                        '8', '08' => 'Agustus',
                        '9', '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                        'T01' => 'Triwulan 1',
                        'T02' => 'Triwulan 2',
                        'T03' => 'Triwulan 3',
                        'T04' => 'Triwulan 4',
                        default => (string) $state,
                    })
                    ->sortable(),
                TextColumn::make('tahun')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('kualitas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                TextColumn::make('kuantitas')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                TextColumn::make('perilaku')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                TextColumn::make('nilai_akhir')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
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
                    ->label('Periode')
                    ->getTitleFromRecordUsing(fn($record): string => match ((string) $record->bulan) {
                        '1', '01' => 'Januari',
                        '2', '02' => 'Februari',
                        '3', '03' => 'Maret',
                        '4', '04' => 'April',
                        '5', '05' => 'Mei',
                        '6', '06' => 'Juni',
                        '7', '07' => 'Juli',
                        '8', '08' => 'Agustus',
                        '9', '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                        'T01' => 'Triwulan 1',
                        'T02' => 'Triwulan 2',
                        'T03' => 'Triwulan 3',
                        'T04' => 'Triwulan 4',
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
