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
- Kelola akun pengguna
- Sistem role & permission (admin, pegawai)
- Fitur paksa ganti password saat login pertama

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

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan internal BPS Kabupaten Demak.
