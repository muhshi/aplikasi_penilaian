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

### 5. Nilai Pegawai
- Rekapitulasi nilai pegawai secara keseluruhan

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

All notable changes to this project will be documented in this section.

## [Unreleased] - 2026-03-31

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
