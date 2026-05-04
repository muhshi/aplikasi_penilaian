<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PegawaiObserver
{
    /**
     * Handle the Pegawai "creating" event.
     */
    public function creating(Pegawai $pegawai): void
    {
        // Jika user_id belum ada, kita bisa coba cari berdasarkan email atau buat baru
        // Namun biasanya saat sync, kita sudah punya data email.
        // Untuk saat ini, kita biarkan logic di Command atau UI yang menangani link user_id
        // jika memang dibutuhkan logic yang sangat spesifik.
    }

    /**
     * Handle the Pegawai "saved" event (created or updated).
     */
    public function saved(Pegawai $pegawai): void
    {
        if ($pegawai->user_id) {
            $user = User::find($pegawai->user_id);
            if ($user) {
                $user->update([
                    'name' => $pegawai->nama ?? $user->name,
                    'nip' => $pegawai->nip ?? $user->nip,
                    'jabatan' => $pegawai->jabatan ?? $user->jabatan,
                    'is_active' => $pegawai->is_active ?? $user->is_active,
                    'identity_type' => $pegawai->identity_type ?? $user->identity_type,
                ]);
            }
        }
    }
}
