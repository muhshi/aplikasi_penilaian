<?php

namespace App\Filament\Resources\NilaiKipapps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NilaiKipappsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip_lama')
                    ->searchable(),
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
                TextColumn::make('rata_rata_hasil_kerja')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rata_rata_perilaku')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nilai_rata_rata')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('predikat_kinerja')
                    ->searchable(),
                TextColumn::make('nilai_prestasi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
