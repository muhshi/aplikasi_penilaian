<?php

namespace App\Filament\Resources\CkpKipapps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class CkpKipappsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_file')
                    ->label('File')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ? '📄 Dokumen Tersedia' : 'Kosong')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->url(fn ($record) => $record->nama_file ? route('file.preview', ['path' => $record->nama_file]) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('keterangan')
                    ->label('Keterangan / Jabatan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('bulan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                TextColumn::make('tahun')
                    ->numeric(thousandsSeparator: '')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
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
                SelectFilter::make('bulan')
                    ->options(fn () => \App\Models\CkpKipapp::select('bulan')
                        ->whereNotNull('bulan')
                        ->distinct()
                        ->pluck('bulan', 'bulan')
                        ->toArray()
                    )
                    ->label('Filter Bulan / Periode'),
                SelectFilter::make('tahun')
                    ->options(fn () => \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray())
                    ->label('Filter Tahun'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('download_zip')
                        ->label('Download ZIP')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $zipFileName = 'ckp_kipapp_' . now()->format('Y_m_d_His') . '.zip';
                            $zipPath = storage_path('app/public/' . $zipFileName);
                            
                            $zip = new \ZipArchive();
                            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                                foreach ($records as $record) {
                                    if ($record->nama_file && Storage::disk('public')->exists($record->nama_file)) {
                                        $fileAbsPath = Storage::disk('public')->path($record->nama_file);
                                        
                                        $userName = $record->user ? str_replace(' ', '_', $record->user->name) : 'User';
                                        $newFileName = sprintf('%s_%s_%s.pdf', $userName, $record->bulan, $record->tahun);
                                        
                                        $zip->addFile($fileAbsPath, $newFileName);
                                    }
                                }
                                $zip->close();
                                
                                return response()->download($zipPath)->deleteFileAfterSend(true);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
