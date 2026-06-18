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
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('user_id', null);
                                        $set('user_ids', []);
                                    }),

                                Select::make('penilai_id')
                                    ->label('Nama Penilai')
                                    ->options(
                                        User::role(['super_admin', 'ketua_tim'])
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->default(fn() => auth()->id())
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ]),

                        // Untuk mode EDIT (single)
                        Select::make('user_id')
                            ->label('Nama Pegawai (Yang Dinilai)')
                            ->options(function (Get $get) {
                                return self::getAvailableUsers($get);
                            })
                            ->searchable()
                            ->preload()
                            ->required(fn(string $operation) => $operation === 'edit')
                            ->hidden(fn(string $operation) => $operation === 'create')
                            ->live(),

                        // Untuk mode CREATE (multiple)
                        Select::make('user_ids')
                            ->label('Nama Pegawai (Yang Dinilai)')
                            ->multiple()
                            ->options(function (Get $get) {
                                return self::getAvailableUsers($get);
                            })
                            ->searchable()
                            ->preload()
                            ->required(fn(string $operation) => $operation === 'create')
                            ->hidden(fn(string $operation) => $operation === 'edit')
                            ->live(),
                    ])->collapsible(),

                // Komponen Nilai Dinamis
                Group::make()
                    ->schema(function (Get $get, string $operation) {
                        if ($operation === 'edit') {
                            return [
                                self::getKomponenNilaiSection('Komponen Nilai', null)
                            ];
                        }

                        $userIds = $get('user_ids') ?? [];
                        $sections = [];

                        foreach ($userIds as $userId) {
                            $user = User::find($userId);
                            if ($user) {
                                $sections[] = self::getKomponenNilaiSection('Komponen Nilai - ' . $user->name, $userId)
                                    ->statePath("nilai.{$userId}");
                            }
                        }

                        return $sections;
                    }),
            ]);
    }

    private static function getAvailableUsers(Get $get): array
    {
        $penilaiId = $get('penilai_id');
        $bulan = $get('bulan');
        $tahun = $get('tahun');

        if (!$penilaiId) {
            return [];
        }

        $query = User::role('pegawai')
            ->where('id', '!=', $penilaiId)
            ->where('is_active', true)
            ->whereDoesntHave('nilaiPegawais', function ($q) use ($penilaiId, $bulan, $tahun) {
                if ($bulan && $tahun) {
                    $q->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->where('penilai_id', $penilaiId);
                } else {
                    $q->whereRaw('1 = 0');
                }
            });

        return $query->pluck('name', 'id')->toArray();
    }

    private static function getKomponenNilaiSection(string $title, ?string $userId): Section
    {
        return Section::make($title)
            ->icon('heroicon-o-chart-pie')
            ->schema([
                Grid::make(5)
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
                            ->live(debounce: 500)
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
                            ->live(debounce: 500)
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
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::calculateResult($set, $get)),

                        TextInput::make('nilai_akhir')
                            ->label('Nilai Akhir')
                            ->numeric()
                            ->readOnly()
                            ->extraInputAttributes(['class' => 'font-bold text-lg bg-gray-50']),

                        TextInput::make('predikat')
                            ->label('Predikat')
                            ->readOnly()
                            ->extraInputAttributes(['class' => 'font-bold text-lg bg-gray-50']),
                    ])
            ])->collapsible();
    }

    // --- LOGIKA HITUNG ---
    public static function calculateResult(Set $set, Get $get): void
    {
        $k1 = $get('kualitas');
        $k2 = $get('kuantitas');
        $p  = $get('perilaku');

        // Jika salah satu field masih kosong, jangan lakukan apa-apa dulu
        if ($k1 === null || $k1 === '' || $k2 === null || $k2 === '' || $p === null || $p === '') {
            return;
        }

        // Validasi: harus angka bulat 0–100
        $isValid = fn($v) => is_numeric($v) && (int)$v == $v && (int)$v >= 0 && (int)$v <= 100;

        if (!$isValid($k1) || !$isValid($k2) || !$isValid($p)) {
            $set('nilai_akhir', null);
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
            default    => 'Kurang',
        };
        $set('predikat', $predikat);
    }
}
