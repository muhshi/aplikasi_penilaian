<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

/**
 * Class PegawaiImport
 * 
 * Class untuk menghandle import data Pegawai dari file Excel.
 * 
 * Fitur:
 * - Membaca file Excel dengan header row (baris pertama sebagai nama kolom)
 * - Validasi data sebelum import
 * - Skip baris kosong otomatis
 * - Otomatis membuat User baru jika email belum terdaftar
 * - Update data Pegawai jika user_id sudah ada
 * - Insert data Pegawai baru jika belum ada
 * - Password default menggunakan NIP
 * 
 * Format Excel yang diharapkan:
 * | nip | nip_lama | nama | jabatan | pangkat | golongan | no_hp | email |
 */
class PegawaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * Proses setiap baris data dari Excel
     * 
     * Alur proses:
     * 1. Cek apakah User dengan email tersebut sudah ada
     * 2. Jika belum ada, buat User baru dengan password = NIP
     * 3. Cek apakah Pegawai dengan user_id tersebut sudah ada
     * 4. Jika sudah ada, update data Pegawai
     * 5. Jika belum ada, insert data Pegawai baru
     * 
     * @param array $row Data dari satu baris Excel (key = nama kolom, value = nilai cell)
     * @return Pegawai|null Return model jika berhasil insert, null jika update atau skip
     */
    public function model(array $row)
    {
        // Ambil data wajib dari row
        $email = $row['email'];
        $nip = (string) $row['nip'];
        $nama = $row['nama'];

        // Skip baris jika data wajib tidak lengkap
        if (!$email || !$nip || !$nama) {
            return null;
        }

        // ========================================
        // STEP 1: Cari atau Buat User
        // ========================================
        $user = User::where('email', $email)->first();

        // Jika user belum ada, buat user baru
        if (!$user) {
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make($nip), // Password default = NIP
                'must_change_password' => true, // Flag agar user harus ganti password saat login pertama
            ]);
        }

        // ========================================
        // STEP 2: Update atau Buat Pegawai
        // ========================================
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        // Siapkan data pegawai yang akan disimpan/diupdate
        $pegawaiData = [
            'nip' => $nip,
            'nip_lama' => (string) ($row['nip_lama'] ?? ''),
            'no_hp' => $row['no_hp'] ?? '',
            'jabatan' => $row['jabatan'] ?? '',
            'pangkat' => $row['pangkat'] ?? '',
            'golongan' => $row['golongan'] ?? '',
        ];

        // Update jika pegawai sudah ada
        if ($pegawai) {
            $pegawai->update($pegawaiData);
            return null; // Return null karena tidak ada model baru yang dibuat
        } else {
            // Insert pegawai baru
            return new Pegawai(array_merge(['user_id' => $user->id], $pegawaiData));
        }
    }

    /**
     * Aturan validasi untuk setiap baris data
     * 
     * @return array Aturan validasi Laravel
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'nip' => 'required',
            'nip_lama' => 'required',
            'nama' => 'required',
        ];
    }
}
