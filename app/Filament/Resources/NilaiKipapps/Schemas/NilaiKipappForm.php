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
                // Identitas pegawai
                TextInput::make('nip_lama')
                    ->label('NIP Lama')
                    ->required(),

                // Periode penilaian
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
                Select::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        return \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray();
                    })
                    ->default(function () {
                        $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
                        return $active ? $active->tahun : date('Y');
                    })
                    ->searchable()
                    ->required(),

                // Komponen nilai
                TextInput::make('rata_rata_hasil_kerja')
                    ->label('Rata-rata Hasil Kerja')
                    ->numeric(),
                TextInput::make('rata_rata_perilaku')
                    ->label('Rata-rata Perilaku')
                    ->numeric(),
                TextInput::make('nilai_rata_rata')
                    ->label('Nilai Rata-rata')
                    ->numeric(),
                TextInput::make('predikat_kinerja')
                    ->label('Predikat Kinerja'),
                TextInput::make('nilai_prestasi')
                    ->label('Nilai Prestasi')
                    ->numeric(),

                // --- BAGIAN UPLOAD PDF (AdvancedFileUpload) ---
                // Upload dokumen PDF dengan preview langsung,
                // agar user bisa memverifikasi file sebelum submit
                AdvancedFileUpload::make('nama_file')
                    ->label('Dokumen Nilai KIPAPP (PDF)')
                    ->disk('public')                           // Simpan di disk 'public' agar bisa diakses
                    ->directory('nilai-kipapp-documents')       // Folder penyimpanan di storage/app/public/
                    ->acceptedFileTypes(['application/pdf'])    // Hanya terima file PDF
                    ->pdfPreviewHeight(500)                     // Tinggi preview PDF (px)
                    ->pdfDisplayPage(1)                         // Tampilkan halaman pertama saat preview
                    ->pdfToolbar(true)                          // Tampilkan toolbar PDF (zoom, download, dll)
                    ->pdfZoomLevel(100),                        // Level zoom default 100%
                // ----------------------------------------------
            ]);
    }
}
