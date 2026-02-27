<?php

namespace App\Filament\Resources\CkpKipapps\Schemas;

use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Class CkpKipappForm
 * 
 * Form schema untuk input data CKP KIPAPP.
 * 
 * Field yang tersedia:
 * - user_id: ID user yang login (otomatis terisi, hidden)
 * - bulan: Bulan periode CKP (dropdown, termasuk opsi Tahunan)
 * - tahun: Tahun periode CKP
 * - nama_file: Upload dokumen CKP berformat PDF (dengan preview)
 */
class CkpKipappForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // User ID otomatis terisi dari user yang sedang login
                Hidden::make('user_id')
                    ->default(fn() => auth()->id()),

                // Pilihan bulan/periode CKP
                Select::make('bulan')
                    ->label('Bulan')
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
                        // Opsi tahunan untuk dokumen yang bersifat tahunan
                        'Tahunan Penetapan' => 'Tahunan Penetapan',
                        'Tahunan Penilaian' => 'Tahunan Penilaian',
                        'Tahunan Dokumen Evaluasi' => 'Tahunan Dokumen Evaluasi',
                    ])
                    ->searchable()
                    ->required(),

                // Tahun periode CKP 
                Select::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        return \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray();
                    })
                    ->default(function () {
                        $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
                        return $active ? $active->tahun : date('Y');
                    })
                    ->required(),

                //upload dokumen
                AdvancedFileUpload::make('nama_file')
                    ->label('Dokumen CKP (PDF)')
                    ->disk('public')
                    ->directory('ckp-documents')
                    ->acceptedFileTypes(['application/pdf'])
                    ->pdfPreviewHeight(500)
                    ->pdfDisplayPage(1)
                    ->pdfToolbar(true)
                    ->pdfZoomLevel(100)
                    ->required(),
            ]);
    }
}
