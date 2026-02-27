<?php

namespace App\Filament\Resources\Pegawais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PegawaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                /**
                 * Action untuk import data Pegawai dari file Excel
                 * 
                 * Fitur:
                 * - Upload file Excel (.xlsx, .xls)
                 * - Validasi format file
                 * - Import data menggunakan PegawaiImport class
                 * - Otomatis membuat User baru jika email belum terdaftar
                 * - Update data Pegawai jika sudah ada
                 * - Notifikasi sukses setelah import selesai
                 */
                Action::make('import')
                    ->label('Import Pegawai')
                    ->color('success')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->disk('local')
                            // Hanya terima file Excel (.xlsx, .xls)
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PegawaiImport, $path);

                            \Filament\Notifications\Notification::make()
                                ->title('Import Berhasil')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Import')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                /**
                 * Action untuk download template Excel kosong
                 * 
                 * Template berisi header kolom yang sesuai dengan format import.
                 * User dapat mengisi data pegawai di template ini, kemudian upload kembali.
                 */
                Action::make('download_template')
                    ->label('Download Template')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        // Generate dan download file Excel template
                        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PegawaiTemplateExport, 'template_pegawai.xlsx');
                    }),
            ])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('nip')
                    ->searchable(),
                TextColumn::make('nip_lama')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->searchable(),
                TextColumn::make('jabatan')
                    ->searchable(),
                TextColumn::make('pangkat')
                    ->searchable(),
                TextColumn::make('golongan')
                    ->searchable(),
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
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
