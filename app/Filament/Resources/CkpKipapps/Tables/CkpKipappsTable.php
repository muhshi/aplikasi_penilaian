<?php

namespace App\Filament\Resources\CkpKipapps\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use iio\libmergepdf\Merger;

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
                    ->formatStateUsing(fn($state) => $state ? '📄 Dokumen Tersedia' : 'Kosong')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->url(function ($record) {
                        if (!$record->nama_file) return null;
                        $userName = $record->user ? str_replace(' ', '_', $record->user->name) : 'User';
                        $displayName = sprintf('%s_%s_%s.pdf', $userName, $record->bulan, $record->tahun);
                        return route('file.preview', ['path' => $record->nama_file]) . '?name=' . urlencode($displayName);
                    })
                    ->openUrlInNewTab(),
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
                    ->options(
                        fn() => \App\Models\CkpKipapp::select('bulan')
                            ->whereNotNull('bulan')
                            ->distinct()
                            ->pluck('bulan', 'bulan')
                            ->toArray()
                    )
                    ->label('Filter Bulan / Periode'),
                SelectFilter::make('tahun')
                    ->options(fn() => \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray())
                    ->label('Filter Tahun'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {
                        if ($record->nama_file && Storage::disk('public')->exists($record->nama_file)) {
                            $fileAbsPath = Storage::disk('public')->path($record->nama_file);
                            $userName = $record->user ? str_replace(' ', '_', $record->user->name) : 'User';
                            $newFileName = sprintf('%s_%s_%s.pdf', $userName, $record->bulan, $record->tahun);

                            return response()->download($fileAbsPath, $newFileName);
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('File tidak ditemukan')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn($record) => !empty($record->nama_file)),
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
                    BulkAction::make('download_merged_pdf')
                        ->label('Download PDF Gabungan')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $merger = new Merger;
                            $mergedCount = 0;

                            foreach ($records as $record) {
                                if ($record->nama_file && Storage::disk('public')->exists($record->nama_file)) {
                                    $fileAbsPath = Storage::disk('public')->path($record->nama_file);
                                    $merger->addFile($fileAbsPath);
                                    $mergedCount++;
                                }
                            }

                            if ($mergedCount === 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Tidak ada file PDF yang bisa digabungkan.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $createdPdf = $merger->merge();
                            $mergedFileName = 'ckp_kipapp_merged_' . now()->format('Y_m_d_His') . '.pdf';
                            $mergedPath = storage_path('app/public/' . $mergedFileName);

                            file_put_contents($mergedPath, $createdPdf);

                            return response()->download($mergedPath)->deleteFileAfterSend(true);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
