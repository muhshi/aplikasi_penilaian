<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class NilaiPegawaiTemplateExport
 * 
 * Template CSV/Excel untuk import nilai pegawai.
 * Sudah terisi NIP dan Nama pegawai, ketua tim hanya perlu mengisi nilai.
 * 
 * Kolom yang tersedia:
 * - nip: NIP pegawai (sudah terisi, jangan diubah)
 * - nama: Nama pegawai (referensi saja, tidak digunakan saat import)
 * - bulan: Bulan penilaian (isi angka 1-12 atau nama bulan)
 * - tahun: Tahun penilaian (isi angka, contoh: 2026)
 * - kualitas: Nilai kualitas (0-100)
 * - kuantitas: Nilai kuantitas (0-100)
 * - perilaku: Nilai perilaku (0-100)
 */
class NilaiPegawaiTemplateExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'nip',
            'nama',
            'bulan',
            'tahun',
            'kualitas',
            'kuantitas',
            'perilaku',
        ];
    }

    public function collection()
    {
        // Pre-fill NIP dan Nama dari semua pegawai
        return Pegawai::with('user')
            ->orderBy('nip')
            ->get()
            ->map(function ($pegawai) {
                return [
                    // Tambahkan tanda kutip satu (') agar Excel membaca NIP sebagai teks, bukan angka scientific (1.97E+17)
                    'nip' => "'" . $pegawai->nip,
                    'nama' => $pegawai->user->name ?? '-',
                    'bulan' => '',
                    'tahun' => '',
                    'kualitas' => '',
                    'kuantitas' => '',
                    'perilaku' => '',
                ];
            });
    }
}
