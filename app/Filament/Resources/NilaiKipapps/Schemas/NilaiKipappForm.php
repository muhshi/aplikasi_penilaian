<?php

namespace App\Filament\Resources\NilaiKipapps\Schemas;

use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Class NilaiKipappForm
 * 
 * Form schema untuk input data Nilai KIPAPP.
 * 
 * Field yang tersedia:
 * - nip_lama: NIP lama pegawai (identifier)
 * - bulan: Bulan penilaian (1-12)
 * - tahun: Tahun penilaian
 * - rata_rata_hasil_kerja: Rata-rata nilai hasil kerja
 * - rata_rata_perilaku: Rata-rata nilai perilaku
 * - nilai_rata_rata: Rata-rata keseluruhan
 * - predikat_kinerja: Predikat kinerja (misal: Baik, Sangat Baik, dsb.)
 * - nilai_prestasi: Nilai prestasi akhir
 * - nama_file: Upload dokumen PDF untuk verifikasi/arsip
 */
class NilaiKipappForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Identitas Pegawai')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('nip_lama')
                            ->label('NIP Lama')
                            ->required(),
                    ])->collapsible(),

                \Filament\Schemas\Components\Section::make('Periode Penilaian')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                Select::make('tahun')
                                    ->label('Tahun')
                                    ->options(\App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray())
                                    ->default(function () {
                                        $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
                                        return $active ? $active->tahun : date('Y');
                                    })
                                    ->searchable()
                                    ->required(),
                                Select::make('bulan')
                                    ->label('Bulan')
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
                                    ])
                                    ->required(),
                            ]),
                    ])->collapsible(),

                \Filament\Schemas\Components\Section::make('Komponen Penilaian')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('rata_rata_hasil_kerja')
                                    ->label('Rata-rata Hasil Kerja')
                                    ->numeric(),
                                TextInput::make('rata_rata_perilaku')
                                    ->label('Rata-rata Perilaku')
                                    ->numeric(),
                            ]),
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                TextInput::make('nilai_rata_rata')
                                    ->label('Nilai Rata-rata')
                                    ->numeric(),
                                TextInput::make('predikat_kinerja')
                                    ->label('Predikat Kinerja'),
                                TextInput::make('nilai_prestasi')
                                    ->label('Nilai Prestasi')
                                    ->numeric(),
                            ]),
                    ])->collapsible(),

                \Filament\Schemas\Components\Section::make('Dokumen Pendukung')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        AdvancedFileUpload::make('nama_file')
                            ->label('Dokumen Nilai KIPAPP (PDF)')
                            ->disk('public')
                            ->directory('nilai-kipapp-documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->pdfPreviewHeight(500)
                            ->pdfDisplayPage(1)
                            ->pdfToolbar(true)
                            ->pdfZoomLevel(100),
                    ])->collapsible(),
            ]);
    }
}
