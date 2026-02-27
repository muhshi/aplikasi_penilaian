<?php

namespace App\Imports;

use App\Models\NilaiKipapp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;

/**
 * Class NilaiKipappImport
 * 
 * Class untuk menghandle import data Nilai KIPAPP dari file Excel.
 * 
 * Format Excel yang diharapkan (header baris pertama):
 * | Nip lama | Nama | Bulan | Tahun | Jabatan | Rata-rata hasil kerja | Rata-rata perilaku | Nilai rata-rata | Predikat kinerja | Status | Nilai prestasi |
 * 
 * Catatan:
 * - Kolom "Bulan" berisi nama bulan (Januari, Februari, ..., Desember)
 *   akan otomatis dikonversi ke angka (1-12)
 * - Kolom "Tahun" berisi angka tahun (contoh: 2025)
 */
class NilaiKipappImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithChunkReading
{
    /**
     * Mapping nama bulan Indonesia ke angka
     */
    private array $bulanMapping = [
        'januari' => 1,
        'februari' => 2,
        'maret' => 3,
        'april' => 4,
        'mei' => 5,
        'juni' => 6,
        'juli' => 7,
        'agustus' => 8,
        'september' => 9,
        'oktober' => 10,
        'november' => 11,
        'desember' => 12,
    ];

    /**
     * Konversi nilai bulan dari Excel ke angka 1-12
     * 
     * Mendukung:
     * - Nama bulan Indonesia: "Januari", "FEBRUARI", "maret", dll
     * - Angka langsung: 1, 2, ..., 12
     * 
     * @param mixed $bulanValue Nilai bulan dari Excel
     * @return int|null Angka bulan (1-12) atau null jika tidak valid
     */
    private function parseBulan($bulanValue): ?int
    {
        if (empty($bulanValue)) {
            return null;
        }

        // Jika sudah berupa angka 1-12, langsung return
        if (is_numeric($bulanValue)) {
            $num = (int) $bulanValue;
            return ($num >= 1 && $num <= 12) ? $num : null;
        }

        // Konversi nama bulan ke lowercase lalu cari di mapping
        $bulanLower = strtolower(trim($bulanValue));
        return $this->bulanMapping[$bulanLower] ?? null;
    }

    /**
     * Proses setiap baris data dari Excel
     * 
     * @param array $row Data dari satu baris Excel
     * @return NilaiKipapp|null Return model jika berhasil, null jika skip
     */
    public function model(array $row)
    {
        // Ambil data wajib dari row
        $nipLama = $row['nip_lama'] ?? null;
        $bulan = $this->parseBulan($row['bulan'] ?? null);
        $tahun = isset($row['tahun']) ? (int) $row['tahun'] : null;

        // Skip baris jika data wajib tidak lengkap
        if (!$nipLama || !$bulan || !$tahun) {
            return null;
        }

        // Cek apakah data sudah ada (berdasarkan nip_lama + bulan + tahun)
        $nilai = NilaiKipapp::where('nip_lama', $nipLama)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        // Siapkan data yang akan disimpan/diupdate
        $data = [
            'nip_lama' => $nipLama,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'rata_rata_hasil_kerja' => $row['rata_rata_hasil_kerja'] ?? $row['rata-rata_hasil_kerja'] ?? 0,
            'rata_rata_perilaku' => $row['rata_rata_perilaku'] ?? $row['rata-rata_perilaku'] ?? 0,
            'nilai_rata_rata' => $row['nilai_rata_rata'] ?? $row['nilai_rata-rata'] ?? 0,
            'predikat_kinerja' => $row['predikat_kinerja'] ?? '',
            'nilai_prestasi' => $row['nilai_prestasi'] ?? 0,
        ];

        // Update jika data sudah ada, insert jika belum
        if ($nilai) {
            $nilai->update($data);
            return $nilai;
        }

        return new NilaiKipapp($data);
    }

    /**
     * Jumlah baris per batch (chunk)
     */
    public function chunkSize(): int
    {
        return 200;
    }
}
