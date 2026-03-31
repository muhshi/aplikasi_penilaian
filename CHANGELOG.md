# Changelog

All notable changes to this project will be documented in this file.

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
