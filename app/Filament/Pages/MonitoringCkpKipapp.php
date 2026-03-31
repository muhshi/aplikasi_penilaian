<?php

namespace App\Filament\Pages;

use App\Models\CkpKipapp;
use App\Models\NilaiPegawai;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class MonitoringCkpKipapp extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Monitoring';

    protected static ?string $title = 'Monitoring CKP KIPAPP';

    protected static ?string $slug = 'monitoring-ckp-kipapp';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.monitoring-ckp-kipapp';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'ketua_tim']) ?? false;
    }

    public int $tahun;

    /**
     * Daftar periode bulanan dan tahunan.
     */
    public array $periods = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
        'Tahunan Penetapan',
        'Tahunan Penilaian',
        'Tahunan Dokumen Evaluasi',
    ];

    public array $bulanList = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public function mount(): void
    {
        $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
        $this->tahun = $active ? $active->tahun : (int) now()->year;
    }

    /**
     * Property untuk daftar tahun yang tersedia di dropdown.
     */
    public function getAvailableYearsProperty(): array
    {
        return \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray();
    }

    /**
     * Property untuk data monitoring matrix.
     */
    public function getMonitoringDataProperty(): Collection
    {
        // Ambil semua user yang punya pegawai (relasi)
        $users = User::whereHas('pegawai')
            ->with('pegawai')
            ->orderBy('name')
            ->get();

        // Ambil semua CKP records untuk tahun yang dipilih
        $ckpRecords = CkpKipapp::where('tahun', $this->tahun)
            ->get()
            ->groupBy('user_id');

        return $users->map(function ($user) use ($ckpRecords) {
            $userRecords = $ckpRecords->get($user->id, collect());

            // Buat array status per periode
            $status = [];
            foreach ($this->periods as $period) {
                $status[$period] = $userRecords->contains('bulan', $period);
            }

            return [
                'name' => $user->name,
                'status' => $status,
            ];
        });
    }

    /**
     * Property untuk monitoring nilai pegawai per ketua tim.
     * Menampilkan setiap ketua tim dan status apakah sudah menilai di setiap bulan.
     */
    public function getMonitoringNilaiDataProperty(): Collection
    {
        // Ambil semua user dengan role ketua_tim
        $ketuaTimUsers = User::role('ketua_tim')->orderBy('name')->get();

        // Ambil jumlah pegawai (yang punya relasi pegawai)
        $totalPegawai = User::whereHas('pegawai')->count();

        // Ambil semua nilai pegawai untuk tahun yang dipilih, grouped by penilai_id dan bulan
        $nilaiRecords = NilaiPegawai::where('tahun', $this->tahun)
            ->whereNotNull('penilai_id')
            ->get()
            ->groupBy('penilai_id');

        return $ketuaTimUsers->map(function ($ketuaTim) use ($nilaiRecords, $totalPegawai) {
            $ketuaRecords = $nilaiRecords->get($ketuaTim->id, collect());

            $status = [];
            foreach ($this->bulanList as $bulanNum => $bulanName) {
                // Berapa banyak pegawai yang sudah dinilai oleh ketua tim ini?
                $jumlahDinilai = $ketuaRecords->where('bulan', $bulanNum)->count();
                
                // Cek apakah ketua tim ini punya pemetaan bawahan khusus
                $assignedCount = \App\Models\Pegawai::where('penilai_id', $ketuaTim->id)->count();
                
                // Jika punya bawahan, targetnya adalah jumlah bawahan. Jika tidak (sistem terbuka), targetnya adalah total semua pegawai.
                $target = ($assignedCount > 0) ? $assignedCount : $totalPegawai;

                $status[$bulanName] = [
                    'sudah' => $jumlahDinilai,
                    'total' => $target,
                    'selesai' => ($target > 0) && ($jumlahDinilai >= $target),
                ];
            }

            return [
                'name' => $ketuaTim->name,
                'status' => $status,
            ];
        });
    }

    /**
     * Property untuk monitoring progres nilai per bulan.
     * Menampilkan status per pegawai: berapa penilai yang sudah menilai.
     */
    public function getMonitoringProgresDataProperty(): Collection
    {
        $totalPenilai = User::role('ketua_tim')->count();
        $pegawais = User::whereHas('pegawai')->orderBy('name')->get();

        // Ambil semua nilai untuk tahun ini
        $nilaiRecords = NilaiPegawai::where('tahun', $this->tahun)
            ->whereNotNull('penilai_id')
            ->get();

        return $pegawais->map(function ($pegawaiUser) use ($nilaiRecords, $totalPenilai) {
            $pegawaiModel = $pegawaiUser->pegawai;
            $assignedPenilaiId = $pegawaiModel?->penilai_id;
            
            $pegawaiNilai = $nilaiRecords->where('user_id', $pegawaiUser->id);

            $status = [];
            foreach ($this->bulanList as $bulanNum => $bulanName) {
                // Hitung total penilai yang sudah menilai pegawai ini
                $jumlahAktual = $pegawaiNilai->where('bulan', $bulanNum)->count();
                
                // Jika pegawai ini sudah ditugaskan ke penilai tertentu, targetnya 1.
                // Jika belum dipetakan (terbuka), targetnya adalah seluruh ketua tim.
                $target = $assignedPenilaiId ? 1 : $totalPenilai;

                $status[$bulanName] = [
                    'sudah' => $jumlahAktual,
                    'total' => $target,
                    'lengkap' => ($target > 0) && ($jumlahAktual >= $target),
                ];
            }

            return [
                'name' => $pegawaiUser->name,
                'status' => $status,
            ];
        });
    }

    /**
     * Ketika tahun diubah dari dropdown, update data.
     */
    public function updatedTahun(): void
    {
        // Livewire akan otomatis re-render
    }
}
