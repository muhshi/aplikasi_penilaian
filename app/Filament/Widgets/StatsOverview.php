<?php

namespace App\Filament\Widgets;

use App\Models\Pegawai;
use App\Models\NilaiPegawai;
use App\Models\CkpKipapp;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

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
        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        $totalPegawai = Pegawai::count();
        $avgNilai = NilaiPegawai::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->avg('nilai_akhir') ?? 0;

        $totalCkp = CkpKipapp::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->distinct('user_id')
            ->count();

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
