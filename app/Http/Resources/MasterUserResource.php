<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Transformasi data User ke format standar Master Data API.
 * 
 * Diadaptasi dari Sipetra untuk konsistensi antar ekosistem aplikasi BPS.
 */
class MasterUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pegawai = $this->pegawai;

        return [
            // Identifier
            'id'             => (string) $this->id,
            'sipetra_id'     => $this->sipetra_id,

            // Data dasar
            'name'           => $this->name,
            'email'          => $this->email,
            'avatar_url'     => $this->avatar_url ? Storage::disk('public')->url($this->avatar_url) : null,

            // Data kepegawaian (dari relasi pegawai)
            'nip'            => $pegawai?->nip,
            'nip_lama'       => $pegawai?->nip_lama,
            'jabatan'        => $pegawai?->jabatan,
            'pangkat'        => $pegawai?->pangkat,
            'golongan'       => $pegawai?->golongan,
            'no_hp'          => $pegawai?->no_hp,

            // Status
            'is_active'      => (bool) ($this->is_active ?? true),

            // Timestamps
            'updated_at'     => $this->updated_at?->toIso8601String(),
            
            // Metadata khusus penilaian (jika ada)
            'rekap_nilai'    => $this->whenLoaded('nilaiPegawais'),
        ];
    }
}
