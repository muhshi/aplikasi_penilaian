<?php

namespace App\Filament\Widgets;

use App\Models\NilaiPegawai;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

/**
 * Menampilkan ringkasan distribusi nilai pegawai bulan ini dalam bentuk Chart Pie.
 * Membantu pimpinan melihat sebaran performa secara visual.
 */
class NilaiDistributionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Distribusi Predikat Kinerja';

    protected static ?int $sort = 3;

    public function getExtraAttributes(): array
    {
        return [
            'style' => 'height: 400px !important; min-height: 400px !important; overflow: hidden;',
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $bulan = $this->filters['bulan'] ?? null;
        $tahun = $this->filters['tahun'] ?? null;

        // Fallback jika filter kosong
        if (!$bulan || !$tahun) {
            $latestRecord = NilaiPegawai::orderByDesc('tahun')->orderByDesc('bulan')->first();
            $bulan = $bulan ?? $latestRecord?->bulan ?? Carbon::now()->month;
            $tahun = $tahun ?? $latestRecord?->tahun ?? Carbon::now()->year;
        }

        $bulan = (int) $bulan;
        $tahun = (int) $tahun;

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
        $this->heading = "Distribusi Predikat Kinerja ($namaBulan)";

        // Ambil data nilai pegawai hanya pada bulan & tahun terpilih
        $data = NilaiPegawai::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        $counts = [
            'Sangat Baik' => 0,
            'Baik' => 0,
            'Cukup' => 0,
            'Kurang' => 0,
        ];

        // Kategorikan setiap nilai pegawai ke kelompok predikatnya
        foreach ($data as $record) {
            $avg = (float) $record->nilai_akhir;
            if ($avg >= 90)
                $counts['Sangat Baik']++;
            elseif ($avg >= 75)
                $counts['Baik']++;
            elseif ($avg >= 60)
                $counts['Cukup']++;
            else
                $counts['Kurang']++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pegawai',
                    'data' => array_values($counts),
                    'backgroundColor' => [
                        '#0D9488', // Sangat Baik (Teal-Green)
                        '#2563EB', // Baik (Ocean Blue)
                        '#D97706', // Cukup (Amber-Gold)
                        '#DC2626', // Kurang (Coral-Red)
                    ],
                    'borderRadius' => 2,
                ],
            ],
            'labels' => array_keys($counts),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false, // Bar chart labels are on X axis
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

}
