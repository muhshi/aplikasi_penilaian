<?php

namespace App\Exports;

use App\Models\NilaiKipapp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NilaiKipappExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return NilaiKipapp::all();
    }

    public function headings(): array
    {
        return [
            'NIP Lama',
            'Bulan',
            'Tahun',
            'Rata-rata Hasil Kerja',
            'Rata-rata Perilaku',
            'Nilai Rata-rata',
            'Predikat Kinerja',
            'Nilasi Prestasi',
        ];
    }

    /**
     * @var NilaiKipapp $nilaiKipapp
     */
    public function map($nilaiKipapp): array
    {
        $namaBulan = match ((int) $nilaiKipapp->bulan) {
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
            default => $nilaiKipapp->bulan,
        };

        return [
            $nilaiKipapp->nip_lama,
            $namaBulan,
            $nilaiKipapp->tahun,
            $nilaiKipapp->rata_rata_hasil_kerja,
            $nilaiKipapp->rata_rata_perilaku,
            $nilaiKipapp->nilai_rata_rata,
            $nilaiKipapp->predikat_kinerja,
            $nilaiKipapp->nilai_prestasi,
        ];
    }
}
