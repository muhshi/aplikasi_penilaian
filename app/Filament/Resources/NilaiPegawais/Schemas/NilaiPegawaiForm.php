<?php

namespace App\Filament\Resources\NilaiPegawais\Schemas;

use App\Models\NilaiPegawai;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class NilaiPegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // Container Utama: Compact & Professional (max-w-4xl)
                Group::make()
                    ->columnSpan('full')
                    ->schema([
                        // Baris 1: Tahun & Bulan (Grid 2 Kolom)
                        Grid::make(2)
                            ->schema([
                                Select::make('tahun')
                                    ->label('Tahun')
                                    ->options(function () {
                                        return \App\Models\PeriodeTahun::pluck('tahun', 'tahun')->toArray();
                                    })
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
                                    ->label('Bulan')
                                    ->options([
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
                                    ])
                                    ->required()
                                    ->default(fn() => session('last_nilai_pegawai_bulan'))
                                    ->live()
                                    ->extraInputAttributes(['class' => '!rounded-lg !bg-gray-50 !border-gray-200 !shadow-sm !text-center !p-2.5 focus:!ring-1 focus:!ring-primary-500']),
                            ])
                            ->extraAttributes(['class' => 'gap-4 mb-4']),

                        // Baris 2: Nama Pegawai 
                        Select::make('user_id')
                            ->label('Nama Pegawai')
                            ->options(function (Get $get) {
                                $bulan = $get('bulan');
                                $tahun = $get('tahun');
                                return \App\Models\User::whereDoesntHave('nilaiPegawais', function ($q) use ($bulan, $tahun) {
                                    if ($bulan && $tahun) {
                                        $q->where('bulan', $bulan)->where('tahun', $tahun);
                                    } else {
                                        $q->whereRaw('1 = 0');
                                    }
                                })->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->extraInputAttributes(['class' => '!rounded-lg !bg-gray-50 !border-gray-200 !shadow-sm !text-base !p-3 focus:!ring-1 focus:!ring-primary-500']),

                        // Baris 3: Komponen Nilai Grid 3 Kolom
                        Grid::make(3)
                            ->schema([
                                TextInput::make('kualitas')
                                    ->label('Kualitas')
                                    ->integer()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->regex('/^(0|[1-9][0-9]?|100)$/')
                                    ->validationMessages([
                                        'integer' => 'Harus berupa angka bulat.',
                                        'min' => 'Minimal 0.',
                                        'max' => 'Maksimal 100.',
                                        'regex' => 'Format tidak valid (0-100, tanpa awalan nol).',
                                    ])
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get))
                                    ->extraInputAttributes([
                                        'class' => '!rounded-md !bg-white !border !border-gray-200 !shadow-sm !text-center !p-2 focus:!ring-1 focus:!ring-primary-500',
                                        'oninput' => "if(this.value.length > 1 && this.value[0] === '0') this.value = this.value.replace(/^0+/, ''); if(this.value > 100) this.value = 100;",
                                    ]),

                                TextInput::make('kuantitas')
                                    ->label('Kuantitas')
                                    ->integer()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->regex('/^(0|[1-9][0-9]?|100)$/')
                                    ->validationMessages([
                                        'integer' => 'Harus berupa angka bulat.',
                                        'min' => 'Minimal 0.',
                                        'max' => 'Maksimal 100.',
                                        'regex' => 'Format tidak valid (0-100, tanpa awalan nol).',
                                    ])
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get))
                                    ->extraInputAttributes([
                                        'class' => '!rounded-md !bg-white !border !border-gray-200 !shadow-sm !text-center !p-2 focus:!ring-1 focus:!ring-primary-500',
                                        'oninput' => "if(this.value.length > 1 && this.value[0] === '0') this.value = this.value.replace(/^0+/, ''); if(this.value > 100) this.value = 100;",
                                    ]),

                                TextInput::make('perilaku')
                                    ->label('Perilaku')
                                    ->integer()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->regex('/^(0|[1-9][0-9]?|100)$/')
                                    ->validationMessages([
                                        'integer' => 'Harus berupa angka bulat.',
                                        'min' => 'Minimal 0.',
                                        'max' => 'Maksimal 100.',
                                        'regex' => 'Format tidak valid (0-100, tanpa awalan nol).',
                                    ])
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get))
                                    ->extraInputAttributes([
                                        'class' => '!rounded-md !bg-white !border !border-gray-200 !shadow-sm !text-center !p-2 focus:!ring-1 focus:!ring-primary-500',
                                        'oninput' => "if(this.value.length > 1 && this.value[0] === '0') this.value = this.value.replace(/^0+/, ''); if(this.value > 100) this.value = 100;",
                                    ]),
                            ])
                            ->extraAttributes(['class' => 'gap-4 mt-4 mb-4']),

                        // Baris 4: Nilai Akhir 
                        TextInput::make('nilai_akhir')
                            ->label('Nilai Akhir')
                            ->numeric()
                            ->readOnly()
                            ->extraInputAttributes(['class' => '!rounded-lg !bg-blue-50 !border-blue-100 !text-blue-700 !font-bold !text-center !text-lg !p-2.5']),

                        TextInput::make('predikat')
                            ->hidden()

                    ])
                    ->extraAttributes(['class' => 'w-full py-8 px-6 bg-white rounded-xl shadow-sm border border-gray-200']),
            ]);
    }

    // --- LOGIKA HITUNG ---
    public static function calculateResult(Set $set, Get $get): void
    {
        $k1 = $get('kualitas');
        $k2 = $get('kuantitas');
        $p = $get('perilaku');

        // Validasi internal sebelum menghitung (mencegah manipulasi input)
        // Jika tidak valid, set nilai_akhir ke 0 atau tampilkan pesan
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

        // Tentukan predikat
        $predikat = match (true) {
            $avg >= 90 => 'Sangat Baik',
            $avg >= 75 => 'Baik',
            $avg >= 60 => 'Cukup',
            default => 'Kurang',
        };
        $set('predikat', $predikat);
    }
}
