# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased] - 2026-04-09

### Added
- **Otorisasi Import Massal**: Implementasi pembatasan fitur impor massal dokumen CKP/KIPAPP via Excel dan Google Drive yang kini hanya dapat diakses oleh role `super_admin`.
- **Anonimitas Penilai**: Menyembunyikan nama penilai (Ketua Tim) pada tabel Nilai Pegawai untuk pengguna dengan role `pegawai` guna menjaga objektivitas dan privasi proses penilaian.
- **Smart Name Matching (Fuzzy Matching)**: Peningkatan algoritma pencarian user yang mampu menangani perbedaan penulisan nama antara Excel dan database (misal: "Siswo Pranyoto" cocok dengan "Siswo") serta normalisasi singkatan umum (M., Muh. -> Muhamad).
- **Background Download Queue**: Implementasi sistem antrian (Laravel Queue) untuk mengunduh dokumen di latar belakang, mencegah timeout saat memproses ribuan data sekaligus.
- **Renovasi Landing Page Modern**: Pembaruan total halaman depan aplikasi dengan desain glassmorphism, Branding BPS yang profesional, skema warna Navy Blue (#0A2540) yang elegan, serta animasi scroll reveal.
- **Filter Tabel**: Penambahan filter *Tahun* dan *Bulan* pada tabel Nilai Kipapp untuk kemudahan navigasi data.
- **Aset Visual Baru**: Penambahan berbagai aset gambar premium untuk mendukung tampilan landing page yang lebih modern dan informatif.

### Fixed
- **Masalah Visibilitas Role**: Perbaikan masalah menu yang hilang dengan melakukan *Bulk Role Assignment* (menugaskan role `pegawai`) ke seluruh user yang belum memiliki peran.
- **Akurasi Statistik Dashboard**: Resolusi ketidakcocokan tipe data pada perbandingan bulan (numerik vs string) yang sebelumnya menyebabkan kelengkapan CKP terbaca 0.
- **Penyempurnaan Perhitungan Nilai**: Pembaruan logika format nilai pada matriks monitoring untuk memastikan akurasi data dan memastikan nilai tidak melebihi 100%.
- **Konsistensi UI**: Standardisasi CSS untuk judul dashboard dan penyelarasan logo guna mencegah penumpukan (overlapping) dan masalah visibilitas.

## [1.1.0] - 2026-03-31

### Added
- **Otomatisasi User via Pegawai**: Implementasi pembuatan akun `User` secara otomatis (password default: `password123`) saat data `Pegawai` baru dibuat, serta sinkronisasi nama/email saat data diperbarui.
- **Pemetaan Penilai (Assessor Mapping)**: Menambahkan kolom `penilai_id` pada tabel `pegawai` untuk menentukan hubungan atasan-bawahan secara resmi.
- **Filter Akses Ketua Tim**: Mengimplementasikan filter pada `NilaiPegawaiResource` sehingga Ketua Tim hanya melihat baris penilaian miliknya sendiri (Personal), sementara akses monitor CKP tetap bersifat Global.
- **Filter CKP KIPAPP**: Menambahkan filter berdasarkan *Bulan* dan *Tahun* pada tabel CKP KIPAPP.
- **Bulk Download ZIP**: Menambahkan aksi massal (Bulk Action) pada tabel CKP KIPAPP untuk mendownload beberapa file CKP sekaligus dalam format ZIP.
- **Bulk Assign Role User**: Menambahkan aksi massal pada tabel User untuk memberikan (assign) Role ke beberapa pengguna sekaligus.
- **Tampilan Peran User**: Menambahkan kolom pencarian dan tampilan label Peran/Role pada tabel User.
- **PDF Viewer Support**: Migrated `joaopaulolndev/filament-pdf-viewer` to `^3.0` untuk Filament v5.
- **CKP KIPAPP PDF Preview**: Integrasi PDF preview pada detail halaman dan form edit CKP.

### Fixed
- **Validasi NIP Baru**: Memperbaiki error notasi ilmiah pada NIP dengan mengganti validasi `->numeric()` menjadi `rules(['digits:18'])` agar mendukung 18 digit angka murni sebagai string.
- **Role Stacking Fix**: Mengganti `assignRole()` menjadi `syncRoles()` pada seluruh fitur manajemen user/pegawai untuk mencegah penumpukan role yang tidak diinginkan.
- **Monitoring Matrix Error**: Memperbaiki error `Undefined array key "Januari"` pada `MonitoringCkpKipapp` akibat kesalahan urutan argumen pada loop bulan.
- **Validasi Pegawai Form**: Perbaiki validasi `unique()` pada field `user.email` agar mengarah ke tabel `users`.
- **Validasi Password User**: Memperbaiki validasi pembaruan password agar tidak wajib diisi saat edit.
- **MySQL Group By Constraint**: Fixed `ONLY_FULL_GROUP_BY` error di widget rekapitulasi.
- **Table Actions Namespace**: Standarisasi penggunaan `Filament\Actions` untuk seluruh aksi tabel sesuai standar Filament v5.

### Rules Established
- **Filament V5 Action Namespace Rule**: Implemented a core rule to always use `Filament\Actions` for all actions (including table actions) instead of legacy `Filament\Tables\Actions` to prevent "Class Not Found" conflicts in Filament v5.
- **Secure File URL Binding Rule**: Established pattern for `filament-pdf-viewer`; always use a dedicated field name (like `nama_file_viewer`) bound to `->fileUrl()` closure to avoid filament state-binding conflicts when showing documents from secure or custom routes.
