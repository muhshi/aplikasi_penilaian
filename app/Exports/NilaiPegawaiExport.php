<?php

namespace App\Exports;

use App\Models\NilaiPegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NilaiPegawaiExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return NilaiPegawai::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Bulan',
            'Tahun',
            'Kualitas',
            'Kuantitas',
            'Perilaku',
            'Nilai Akhir',
        ];
    }

    /**
     * @var NilaiPegawai $nilaiPegawai
     */
    public function map($nilaiPegawai): array
    {
        $namaBulan = match ((int) $nilaiPegawai->bulan) {
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
            default => $nilaiPegawai->bulan,
        };

        return [
            $nilaiPegawai->user?->name,
            $namaBulan,
            $nilaiPegawai->tahun,
            $nilaiPegawai->kualitas,
            $nilaiPegawai->kuantitas,
            $nilaiPegawai->perilaku,
            $nilaiPegawai->nilai_akhir,
        ];
    }
}
