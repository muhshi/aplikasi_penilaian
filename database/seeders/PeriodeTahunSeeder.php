<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodeTahunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periodeBulanan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Tahunan Penetapan', 'Tahunan Penilaian', 'Tahunan Dokumen Evaluasi'
        ];

        $periodeTriwulan = [
            'Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV',
            'Tahunan Penilaian', 'Tahunan Penetapan', 'Tahunan Evaluasi'
        ];

        \App\Models\PeriodeTahun::updateOrCreate(
            ['tahun' => 2024],
            [
                'periode_aktif' => $periodeBulanan,
                'is_active' => false,
            ]
        );

        \App\Models\PeriodeTahun::updateOrCreate(
            ['tahun' => 2025],
            [
                'periode_aktif' => $periodeBulanan,
                'is_active' => false,
            ]
        );

        \App\Models\PeriodeTahun::updateOrCreate(
            ['tahun' => 2026],
            [
                'periode_aktif' => $periodeTriwulan,
                'is_active' => true, // Default to 2026 as active
            ]
        );
    }
}
