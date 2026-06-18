<?php

namespace App\Filament\Resources\NilaiPegawais\Schemas;

use App\Models\NilaiPegawai;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class NilaiPegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('penilai_id')
                    ->default(fn() => auth()->id()),

                Section::make('Data Penilaian')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('tahun')
                                    ->label('Tahun')
                                    ->options(\App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray())
                                    ->default(function () {
                                        $session = session('last_nilai_pegawai_tahun');
                                        if ($session)
                                            return $session;

                                        $active = \App\Models\PeriodeTahun::where('is_active', true)->first();
                                        return $active ? $active->tahun : date('Y');
                                    })
                                    ->required()
                                    ->live()
                                    ->searchable()
                                    ->extraInputAttributes(['class' => '!rounded-lg !bg-gray-50 !border-gray-200 !shadow-sm !text-center !p-2.5 focus:!ring-1 focus:!ring-primary-500']),

                                Select::make('bulan')
                                    ->label('Periode / Bulan')
                                    ->options(function (Get $get) {
                                        $tahun = $get('tahun');
                                        if (!$tahun)
                                            return [];

                                        $pengaturan = \App\Models\PeriodeTahun::where('tahun', $tahun)->first();
                                        $periodeAktif = $pengaturan ? $pengaturan->periode_aktif : [];

                                        if (!is_array($periodeAktif))
                                            $periodeAktif = [];

                                        $options = [];
                                        foreach ($periodeAktif as $periode) {
                                            $options[$periode] = $periode;
                                        }

                                        return $options;
                                    })
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set) => $set('user_id', null)),

                                Select::make('penilai_id')
                                    ->label('Nama Penilai')
                                    ->options(
                                        \App\Models\User::role(['super_admin', 'ketua_tim'])
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->default(fn() => auth()->id())
                                    ->searchable()
                                    ->required(),
                            ]),

                        Select::make('user_id')
                            ->label('Nama Pegawai (Yang Dinilai)')
                            ->options(function (Get $get) {
                                $penilaiId = $get('penilai_id');
                                $bulan = $get('bulan');
                                $tahun = $get('tahun');

                                if (!$penilaiId) {
                                    return [];
                                }

                                $query = \App\Models\User::role('pegawai')
                                    ->where('id', '!=', $penilaiId)
                                    ->whereDoesntHave('nilaiPegawai', function ($q) use ($penilaiId, $bulan, $tahun) {
                                        if ($bulan && $tahun) {
                                            $q->where('bulan', $bulan)
                                                ->where('tahun', $tahun)
                                                ->where('penilai_id', $penilaiId);
                                        } else {
                                            $q->whereRaw('1 = 0');
                                        }
                                    });

                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                    ])->collapsible(),

                Section::make('Komponen Nilai')
                    ->icon('heroicon-o-chart-pie')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('kualitas')
                                    ->label('Kualitas')
                                    ->integer()
                                    ->required()
                                    ->rule('min:0')
                                    ->rule('max:100')
                                    ->regex('/^(0|[1-9][0-9]?|100)$/')
                                    ->validationMessages([
                                        'integer' => 'Harus berupa angka bulat.',
                                        'min' => 'Minimal 0.',
                                        'max' => 'Maksimal 100.',
                                        'regex' => 'Format tidak valid (0-100).',
                                    ])
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get)),

                                TextInput::make('kuantitas')
                                    ->label('Kuantitas')
                                    ->integer()
                                    ->required()
                                    ->rule('min:0')
                                    ->rule('max:100')
                                    ->regex('/^(0|[1-9][0-9]?|100)$/')
                                    ->validationMessages([
                                        'integer' => 'Harus berupa angka bulat.',
                                        'min' => 'Minimal 0.',
                                        'max' => 'Maksimal 100.',
                                        'regex' => 'Format tidak valid (0-100).',
                                    ])
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get)),

                                TextInput::make('perilaku')
                                    ->label('Perilaku')
                                    ->integer()
                                    ->required()
                                    ->rule('min:0')
                                    ->rule('max:100')
                                    ->regex('/^(0|[1-9][0-9]?|100)$/')
                                    ->validationMessages([
                                        'integer' => 'Harus berupa angka bulat.',
                                        'min' => 'Minimal 0.',
                                        'max' => 'Maksimal 100.',
                                        'regex' => 'Format tidak valid (0-100).',
                                    ])
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get)),
                            ]),
                    ])->collapsible(),

                Section::make('Hasil Akhir')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nilai_akhir')
                                    ->label('Nilai Akhir')
                                    ->numeric()
                                    ->readOnly()
                                    ->extraInputAttributes(['class' => 'font-bold text-lg']),

                                TextInput::make('predikat')
                                    ->label('Predikat')
                                    ->readOnly()
                                    ->extraInputAttributes(['class' => 'font-bold text-lg']),
                            ])
                    ])->collapsible(),
            ]);
    }

    // --- LOGIKA HITUNG ---
    public static function calculateResult(Set $set, Get $get): void
    {
        $k1 = $get('kualitas');
        $k2 = $get('kuantitas');
        $p = $get('perilaku');

        if (
            !preg_match('/^(0|[1-9][0-9]?|100)$/', (string) $k1) ||
            !preg_match('/^(0|[1-9][0-9]?|100)$/', (string) $k2) ||
            !preg_match('/^(0|[1-9][0-9]?|100)$/', (string) $p)
        ) {
            $set('nilai_akhir', 0);
            $set('predikat', 'Input Tidak Valid');
            return;
        }

        $v1 = (float) $k1;
        $v2 = (float) $k2;
        $v3 = (float) $p;

        $avg = ($v1 + $v2 + $v3) / 3;
        $set('nilai_akhir', number_format($avg, 2));

        $predikat = match (true) {
            $avg >= 90 => 'Sangat Baik',
            $avg >= 75 => 'Baik',
            $avg >= 60 => 'Cukup',
            default => 'Kurang',
        };
        $set('predikat', $predikat);
    }
}
