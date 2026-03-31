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
                    ->searchable(),
                TextColumn::make('bulan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tahun')
                    ->numeric(thousandsSeparator: '')
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
                SelectFilter::make('bulan')
                    ->options([
                        'Januari' => 'Januari',
                        'Februari' => 'Februari',
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'Desember' => 'Desember',
                        'Tahunan Penetapan' => 'Tahunan Penetapan',
                        'Tahunan Penilaian' => 'Tahunan Penilaian',
                        'Tahunan Dokumen Evaluasi' => 'Tahunan Dokumen Evaluasi',
                    ])
                    ->label('Filter Bulan'),
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
            ]);
    }
}
