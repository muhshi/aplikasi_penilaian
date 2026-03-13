<?php

namespace App\Imports;

use App\Models\NilaiPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;

/**
 * Class NilaiPegawaiImport
 * 
 * Import data Nilai Pegawai dari file CSV/Excel.
 * Lookup pegawai berdasarkan NIP (lebih akurat daripada nama).
 * penilai_id otomatis diisi dari user yang login (ketua tim).
 * 
 * Format CSV yang diharapkan (header baris pertama):
 * | nip | bulan | tahun | kualitas | kuantitas | perilaku |
 * 
 * Catatan:
 * - Kolom "nip" harus sesuai dengan NIP di tabel pegawai
 * - Kolom "bulan" bisa berupa nama bulan (Januari, dst) atau angka (1-12)
 * - "nilai_akhir" otomatis dihitung dari rata-rata kualitas, kuantitas, perilaku
 * - Jika data sudah ada (pegawai + bulan + tahun + penilai), akan di-update
 */
class NilaiPegawaiImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithChunkReading
{
    private int $penilaiId;

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

    public function __construct()
    {
        $this->penilaiId = auth()->id();
    }

    private function parseBulan($bulanValue): ?int
    {
        if (empty($bulanValue)) {
            return null;
        }

        if (is_numeric($bulanValue)) {
            $num = (int) $bulanValue;
            return ($num >= 1 && $num <= 12) ? $num : null;
        }

        $bulanLower = strtolower(trim($bulanValue));
        
        // Cek awal kata (agar "januar", "jan", "januari" tetap masuk ke Januari)
        if (str_starts_with($bulanLower, 'jan')) return 1;
        if (str_starts_with($bulanLower, 'fe')) return 2;
        if (str_starts_with($bulanLower, 'mar')) return 3;
        if (str_starts_with($bulanLower, 'ap')) return 4;
        if (str_starts_with($bulanLower, 'mei')) return 5;
        if (str_starts_with($bulanLower, 'jun')) return 6;
        if (str_starts_with($bulanLower, 'jul')) return 7;
        if (str_starts_with($bulanLower, 'agu')) return 8;
        if (str_starts_with($bulanLower, 'sep')) return 9;
        if (str_starts_with($bulanLower, 'okt')) return 10;
        if (str_starts_with($bulanLower, 'nov')) return 11;
        if (str_starts_with($bulanLower, 'des')) return 12;

        return null;
    }

    public function collection(Collection $rows)
    {
        \Illuminate\Support\Facades\Log::info('--- Memulai Import Nilai Pegawai ---');
        \Illuminate\Support\Facades\Log::info('Total baris dibaca: ' . $rows->count());

        foreach ($rows as $index => $row) {
            \Illuminate\Support\Facades\Log::info("Baris ke-{$index}:", $row->toArray());

            // Hilangkan spasi dan tanda kutip satu (') di awal NIP jika ada (karena template menggunakan ' agar tidak jadi scientific)
            $nip = ltrim(trim((string)($row['nip'] ?? '')), "'");
            
            $bulan = $this->parseBulan($row['bulan'] ?? null);
            $tahun = isset($row['tahun']) ? (int) $row['tahun'] : null;

            if (!$nip || !$bulan || !$tahun) {
                \Illuminate\Support\Facades\Log::warning("Baris {$index} di-skip. Alasan: Data tidak lengkap. NIP: '{$nip}', Bulan: '{$bulan}', Tahun: '{$tahun}'");
                continue;
            }

            // Cari pegawai berdasarkan NIP, lalu ambil user_id-nya
            $pegawai = Pegawai::where('nip', $nip)->first();
            if (!$pegawai) {
                \Illuminate\Support\Facades\Log::warning("Baris {$index} di-skip. Alasan: Pegawai dengan NIP '{$nip}' tidak ditemukan di database.");
                continue;
            }

            $userId = $pegawai->user_id;

            $kualitas = (float) ($row['kualitas'] ?? 0);
            $kuantitas = (float) ($row['kuantitas'] ?? 0);
            $perilaku = (float) ($row['perilaku'] ?? 0);
            $nilaiAkhir = round(($kualitas + $kuantitas + $perilaku) / 3, 2);

            \Illuminate\Support\Facades\Log::info("Menyimpan/Update Penilaian: UserID: {$userId}, PenilaiID: {$this->penilaiId}, Bulan: {$bulan}, Tahun: {$tahun}, N Akhir: {$nilaiAkhir}");

            // updateOrCreate: update jika sudah ada, buat baru jika belum
            NilaiPegawai::updateOrCreate(
                [
                    'user_id' => $userId,
                    'penilai_id' => $this->penilaiId,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'kualitas' => $kualitas,
                    'kuantitas' => $kuantitas,
                    'perilaku' => $perilaku,
                    'nilai_akhir' => $nilaiAkhir,
                ]
            );
        }
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
