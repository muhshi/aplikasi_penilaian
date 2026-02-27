<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class PegawaiTemplateExport
 * 
 * Class untuk generate template Excel kosong untuk import data Pegawai.
 * File yang di-download hanya berisi header (nama kolom) tanpa data.
 * 
 * Cara penggunaan:
 * 1. User download template ini
 * 2. User isi data pegawai sesuai kolom yang tersedia
 * 3. User upload kembali file yang sudah diisi untuk di-import
 * 
 * Kolom yang tersedia:
 * - Nip: NIP baru pegawai (wajib)
 * - Nip Lama: NIP lama pegawai (opsional)
 * - Nama: Nama lengkap pegawai (wajib)
 * - Jabatan: Jabatan pegawai (opsional)
 * - Pangkat: Pangkat pegawai (opsional)
 * - Golongan: Golongan kepegawaian (opsional)
 * - No Hp: Nomor HP pegawai (opsional)
 * - Email: Email pegawai untuk login (wajib, harus format email valid)
 */
class PegawaiTemplateExport implements WithHeadings
{
    /**
     * Definisi header kolom untuk template Excel
     * 
     * @return array Nama-nama kolom yang akan muncul di baris pertama Excel
     */
    public function headings(): array
    {
        return [
            'Nama',       // Kolom A
            'Email',      // Kolom B
            'Nip',        // Kolom C
            'Nip Lama',   // Kolom D
            'No Hp',      // Kolom E
            'Jabatan',    // Kolom F
            'Pangkat',    // Kolom G
            'Golongan',   // Kolom H
        ];
    }
}
