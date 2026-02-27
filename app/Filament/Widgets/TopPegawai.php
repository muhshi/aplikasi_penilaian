<?php

namespace App\Filament\Widgets;

use App\Models\NilaiPegawai;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class TopPegawai extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    public function getExtraAttributes(): array
    {
        return [
            'style' => 'height: 400px !important; min-height: 400px !important; overflow: hidden;',
        ];
    }


    public function table(Table $table): Table
    {
        $bulan = $this->filters['bulan'] ?? null;
        $tahun = $this->filters['tahun'] ?? null;

        // Fallback jika filter kosong
        if (!$bulan || !$tahun) {
            $latestData = NilaiPegawai::orderByDesc('tahun')->orderByDesc('bulan')->first();
            $bulan = $bulan ?? $latestData?->bulan ?? Carbon::now()->month;
            $tahun = $tahun ?? $latestData?->tahun ?? Carbon::now()->year;
        }

        $bulan = (int) $bulan;
        $tahun = (int) $tahun;

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        return $table
            ->query(
                NilaiPegawai::query()
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->orderByDesc('nilai_akhir')
                    ->limit(5)
            )
            ->heading("🏆 Pegawai Terbaik ($namaBulan)")
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->weight('bold'),
                TextColumn::make('nilai_akhir')
                    ->label('Skor')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->alignCenter(),
                TextColumn::make('predikat')
                    ->label('Predikat')
                    ->state(fn($record) => match (true) {
                        $record->nilai_akhir >= 90 => 'Sangat Baik',
                        $record->nilai_akhir >= 75 => 'Baik',
                        $record->nilai_akhir >= 60 => 'Cukup',
                        default => 'Kurang',
                    })
                    ->badge(),
            ])
            ->paginated(false);
    }
}
