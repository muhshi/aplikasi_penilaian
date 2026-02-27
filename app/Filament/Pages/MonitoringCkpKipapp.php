<?php

namespace App\Filament\Pages;

use App\Models\CkpKipapp;
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
     * Ketika tahun diubah dari dropdown, update data.
     */
    public function updatedTahun(): void
    {
        // Livewire akan otomatis re-render
    }
}
