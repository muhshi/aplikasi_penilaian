<?php

namespace App\Filament\Resources\NilaiPegawais\Pages;

use App\Filament\Resources\NilaiPegawais\NilaiPegawaiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNilaiPegawai extends CreateRecord
{
    protected static string $resource = NilaiPegawaiResource::class;

    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    protected function afterCreate(): void
    {
        // Simpan periode (bulan & tahun) ke session agar tidak perlu input ulang
        // saat menggunakan fitur "Create & Create Another"
        $data = $this->form->getRawState();

        session(['last_nilai_pegawai_bulan' => $data['bulan'] ?? null]);
        session(['last_nilai_pegawai_tahun' => $data['tahun'] ?? null]);
    }
}
