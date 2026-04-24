# Aplikasi Penilaian CKP KIPAPP — BPS Kabupaten Demak

Sistem informasi berbasis web untuk mengelola proses **pengiriman**, **monitoring**, dan **penilaian** Capaian Kinerja Pegawai (CKP) berbasis KIPAPP di lingkungan BPS Kabupaten Demak.

## 🚀 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Framework** | Laravel 12 |
| **Admin Panel** | Filament 5 |
| **Database** | MySQL / SQLite |
| **Auth & Roles** | Filament Shield (Spatie Permission) |
| **Export** | Maatwebsite Excel |
| **PDF Viewer** | Filament PDF Viewer |

## 📋 Fitur Utama

### 1. Dashboard
- Filter berdasarkan **Tahun** dan **Bulan**
- Tahun default mengikuti pengaturan Periode Tahun Aktif

### 2. Monitoring CKP KIPAPP
- Matriks monitoring status pengiriman CKP per pegawai
- 12 periode bulanan + 3 periode tahunan (Penetapan, Penilaian, Dokumen Evaluasi)
- Filter tahun secara dinamis

### 3. Kirim CKP KIPAPP
- Upload dokumen CKP dalam format PDF
- Preview dokumen langsung di dalam aplikasi
- CRUD data CKP per pegawai per periode

### 4. Nilai KIPAPP
- Input dan pengelolaan nilai KIPAPP per pegawai
- Filter berdasarkan **Bulan** dan **Tahun** untuk kemudahan manajemen data

### 5. Nilai Pegawai
- Rekapitulasi nilai pegawai secara keseluruhan

### 9. Bulk Import Lapkin (Google Drive)
- Impor dokumen CKP/KIPAPP massal dari link Google Drive via Excel.
- **Fuzzy Name Matching**: Otomatis mendeteksi pegawai meskipun penulisan nama di Excel berbeda dengan database (mendukung singkatan, nama panggilan, dan pembersihan gelar).
- **Background Processing**: Dokumentasi diunduh menggunakan antrian (Queue) untuk efisiensi server.

### 6. Manajemen Pegawai
- Kelola data pegawai (NIP, nama, jabatan)
- Relasi dengan akun user

### 7. Manajemen User
- **Otomatisasi User & Pegawai** — Akun user otomatis dibuat/diperbarui saat data pegawai ditambah/diubah.
- **Sistem Role & Permission** — Manajemen role (admin, ketua tim, pegawai) berbasis Spatie.
- **Pemetaan Penilai** — Fitur untuk menentukan hubungan atasan-bawahan (Ketua Tim -> Pegawai).
- **Fitur Paksa Ganti Password** — Mewajibkan ganti password saat login pertama kali.

### 8. Pengaturan Sistem
- Manajemen Periode Tahun
- Satu tahun aktif sebagai default di seluruh aplikasi
- Dropdown tahun pada Dashboard, CKP, dan Nilai otomatis mengambil dari tabel ini

## ⚙️ Instalasi

### Prasyarat
- PHP ≥ 8.2
- Composer
- Node.js & NPM
- MySQL / SQLite

### Langkah-langkah

```bash
# 1. Clone repository
git clone https://github.com/muhshi/aplikasi_penilaian.git
cd aplikasi_penilaian

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di file .env
# Sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Jalankan migrasi & seed
php artisan migrate --seed

# 6. Setup storage link
php artisan storage:link

# 7. Build assets
npm run build

# 8. Jalankan server
php artisan serve
```

 Atau gunakan shortcut:
```bash
composer setup   # Install, migrate, build sekaligus
composer dev     # Jalankan server + queue + pail + vite secara bersamaan
```

### 🕒 Menjalankan Antrian (Background Job)
Fitur Bulk Import memerlukan Worker untuk mendownload file di latar belakang. Jalankan perintah ini di terminal:
```bash
php artisan queue:work
```
*(Gunakan **Supervisor** jika dideploy ke server produksi untuk memastikan worker selalu aktif).*

## 📁 Struktur Utama

```
app/
├── Filament/
│   ├── Pages/
│   │   ├── Dashboard.php              # Halaman utama dengan filter tahun & bulan
│   │   └── MonitoringCkpKipapp.php    # Monitoring matriks CKP
│   └── Resources/
│       ├── CkpKipapps/                # CRUD CKP KIPAPP
│       ├── NilaiKipapps/              # CRUD Nilai KIPAPP
│       ├── NilaiPegawais/             # Rekap Nilai Pegawai
│       ├── Pegawais/                  # CRUD Data Pegawai
│       ├── PeriodeTahuns/             # Pengaturan Periode Tahun
│       └── Users/                     # Manajemen User
├── Exports/                           # Export Excel
└── Models/
    ├── CkpKipapp.php
    ├── NilaiKipapp.php
    ├── NilaiPegawai.php
    ├── Pegawai.php
    ├── PeriodeTahun.php
    └── User.php
```

## 🔐 Role & Permission

Aplikasi ini menggunakan **Filament Shield** untuk manajemen role dan permission:
- **Super Admin** — Akses penuh ke seluruh fitur
- **Pegawai** — Akses terbatas untuk mengirim CKP dan melihat nilai

## 📝 Changelog

- **[2026-03-31]** `chore(gitignore)`: Menambahkan `/public/build`, `/public/css/filament`, dan `/public/js/filament` ke `.gitignore` untuk mencegah pelacakan aset statis hasil kompilasi.
- **[2026-03-31]** `fix(pegawai)`: Memperbaiki error `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'user.email' in 'where clause'` saat melakukan input data Pegawai dengan menyesuaikan *rule validator unique* agar secara eksplisit diarahkan ke tabel `users`.
- **[2026-03-31]** `docs(rules)`: Menambahkan *Git Workflow* rule pada `.cursorrules` agar setiap adanya perubahan di repositori wajib memperbarui log `README.md` dan melakukan `push`.

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan internal BPS Kabupaten Demak.

---

# Changelog

Semua perubahan penting dalam proyek ini akan didokumentasikan di bagian ini.

## [Unreleased] - 2026-04-24

### Added
- **Integrasi SIPETRA SSO**: Implementasi Single Sign-On (SSO) menggunakan OAuth2 dari SIPETRA.
- **Custom Socialite Provider**: Pembuatan provider khusus untuk menangani otentikasi SIPETRA.
- **SSO Login Button**: Penambahan tombol "Masuk dengan SIPETRA SSO" pada halaman login Filament.
- **Auto User Provisioning**: Otomatisasi pendaftaran user baru atau pembaruan data user (NIP, Jabatan, Token) saat login melalui SSO.

### Fixed
- **Development Environment Compatibility**: Memperbaiki perintah `composer dev` agar dapat berjalan di Windows dengan menghapus `laravel/pail` (yang memerlukan ekstensi `pcntl` khusus Unix) dari skrip `concurrently` di `composer.json`.
- **Missing Dependencies**: Memastikan seluruh dependensi frontend terpasang agar perintah `npm run dev` (Vite) dapat berjalan dengan semestinya.


## [Unreleased] - 2026-04-09


### Added
- **Otorisasi Import Massal**: Implementasi pembatasan fitur impor massal dokumen CKP/KIPAPP via Excel dan Google Drive yang kini hanya dapat diakses oleh role `super_admin`.
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
- **PDF Viewer Support**: Migrasi `joaopaulolndev/filament-pdf-viewer` ke `^3.0` untuk Filament v5.
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
