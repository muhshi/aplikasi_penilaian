<?php

namespace App\Filament\Widgets;

use App\Models\Pegawai;
use App\Models\NilaiPegawai;
use App\Models\CkpKipapp;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected static ?int $sort = 1;

    /**
     * Mapping angka bulan ke nama bulan (sesuai format yang disimpan di tabel ckp_kipapp).
     */
    protected const BULAN_MAP = [
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

    protected function getStats(): array
    {
        $bulan = $this->filters['bulan'] ?? null;
        $tahun = $this->filters['tahun'] ?? null;

        if (!$bulan || !$tahun) {
            $latestData = NilaiPegawai::orderByDesc('tahun')->orderByDesc('bulan')->first();
            $bulan = $bulan ?? $latestData?->bulan ?? Carbon::now()->month;
            $tahun = $tahun ?? $latestData?->tahun ?? Carbon::now()->year;
        }

        $bulan = (int) $bulan;
        $tahun = (int) $tahun;
        $namaBulan = self::BULAN_MAP[$bulan] ?? Carbon::create()->month($bulan)->translatedFormat('F');

        $totalPegawai = Pegawai::count();

        // Hitung rata-rata nilai (cap di 100 maksimal)
        $avgNilai = NilaiPegawai::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->avg('nilai_akhir') ?? 0;
        $avgNilai = min((float) $avgNilai, 100);

        // Hitung kelengkapan CKP — gunakan nama bulan string karena tabel ckp_kipapp
        // menyimpan bulan sebagai string (e.g. 'Januari', 'Februari', etc.)
        $totalCkp = CkpKipapp::where('bulan', $namaBulan)
            ->where('tahun', $tahun)
            ->distinct('user_id')
            ->count('user_id');

        return [
            Stat::make('Total Pegawai', $totalPegawai)
                ->description('Total SDM terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make("Rata-rata Nilai ($namaBulan)", number_format($avgNilai, 2))
                ->description('Performa kantor periode ini')
                ->descriptionIcon($avgNilai >= 75 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($avgNilai >= 75 ? 'success' : 'warning'),

            Stat::make("Kelengkapan CKP ($namaBulan)", "$totalCkp / $totalPegawai")
                ->description('Dokumen terkumpul periode ini')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('primary'),
        ];
    }
}
