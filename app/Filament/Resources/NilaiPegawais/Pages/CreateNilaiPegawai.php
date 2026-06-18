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

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $userIds = $data['user_ids'] ?? [];
        $nilaiData = $data['nilai'] ?? [];
        $penilaiId = $data['penilai_id'] ?? auth()->id();
        $bulan = $data['bulan'] ?? null;
        $tahun = $data['tahun'] ?? null;

        $firstRecord = null;

        if (empty($userIds)) {
            // Jika untuk alasan tertentu user_ids kosong (fallback)
            return static::getModel()::create($data);
        }

        foreach ($userIds as $userId) {
            if (isset($nilaiData[$userId])) {
                $recordData = [
                    'user_id' => $userId,
                    'penilai_id' => $penilaiId,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'kualitas' => $nilaiData[$userId]['kualitas'] ?? 0,
                    'kuantitas' => $nilaiData[$userId]['kuantitas'] ?? 0,
                    'perilaku' => $nilaiData[$userId]['perilaku'] ?? 0,
                    'nilai_akhir' => $nilaiData[$userId]['nilai_akhir'] ?? 0,
                    'predikat' => $nilaiData[$userId]['predikat'] ?? 'Kurang',
                ];

                $record = static::getModel()::create($recordData);
                if (!$firstRecord) {
                    $firstRecord = $record;
                }
            }
        }

        // Return record pertama (atau instance kosong) agar Filament tidak crash 
        // ketika selesai mengeksekusi creation hooks.
        return $firstRecord ?? new (static::getModel())();
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
