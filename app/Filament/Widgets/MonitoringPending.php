<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\NilaiPegawai;
use App\Models\CkpKipapp;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class MonitoringPending extends BaseWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

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

        // Mapping angka bulan ke nama bulan (sesuai format di tabel ckp_kipapp)
        $bulanMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $namaBulan = $bulanMap[$bulan] ?? (string) $bulan;

        return $table
            ->query(
                User::query()
                    ->whereHas('pegawai') // Hanya user yang merupakan pegawai
                    ->where(function ($query) use ($bulan, $tahun, $namaBulan) {
                        $query->whereDoesntHave('nilaiPegawais', function ($q) use ($bulan, $tahun) {
                            $q->where('bulan', $bulan)->where('tahun', $tahun);
                        })
                            ->orWhereDoesntHave('ckpKipapps', function ($q) use ($namaBulan, $tahun) {
                                $q->where('bulan', $namaBulan)->where('tahun', $tahun);
                            });
                    })
            )
            ->heading('⚠️ Monitoring Kelengkapan (Pending)')
            ->description('Pegawai yang belum melengkapi data/dokumen pada periode terpilih.')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pegawai')
                    ->searchable(),
                IconColumn::make('nilai_status')
                    ->label('Nilai Pegawai')
                    ->boolean()
                    ->state(fn($record) => $record->nilaiPegawais()->where('bulan', $bulan)->where('tahun', $tahun)->exists())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                IconColumn::make('ckp_status')
                    ->label('Dokumen CKP')
                    ->boolean()
                    ->state(fn($record) => $record->ckpKipapps()->where('bulan', $namaBulan)->where('tahun', $tahun)->exists())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                TextColumn::make('pegawai.jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Action::make('remind')
                    ->label('Ingatkan')
                    ->icon('heroicon-m-megaphone')
                    ->color('warning')
                    ->url(function ($record) use ($bulan, $tahun) {
                        $namaBulan = match ($bulan) {
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
                            default => $bulan,
                        };

                        $message = "Halo {$record->name}, mengingatkan untuk melengkapi data dan dokumen CKP periode {$namaBulan} {$tahun}. Terima kasih.";

                        return "https://wa.me/" . ($record->pegawai->no_hp ?? '') . "?text=" . urlencode($message);
                    }, true)
                    ->hidden(fn($record) => empty($record->pegawai->no_hp)),
            ]);
    }
}
