<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SyncUsersFromSipetra extends Command
{
    protected $signature = 'sync:users {--full : Abaikan timestamp, sync semua data}';
    protected $description = 'Sinkronisasi master data pengguna (pegawai & mitra) dari Sipetra';

    public function handle(): int
    {
        $baseUrl = config('services.sipetra.base_url');
        $token   = config('services.sipetra.api_token');

        if (!$baseUrl || !$token) {
            $this->error('Konfigurasi SIPETRA_BASE_URL atau SIPETRA_API_TOKEN belum diatur.');
            return self::FAILURE;
        }

        $lastSync = $this->option('full') ? null : Cache::get('sipetra_last_synced_at');
        $this->info($lastSync ? "Incremental sync sejak: {$lastSync}" : 'Full sync...');

        $page = 1;
        $created = $updated = 0;
        $rolePegawai = Role::where('name', 'pegawai')->first();
        $roleMitra = Role::where('name', 'mitra')->first();

        do {
            $this->line("Mengambil halaman {$page}...");
            $response = Http::withToken($token)
                ->acceptJson()
                ->get("{$baseUrl}/api/master/users", array_filter([
                    'page'          => $page,
                    'per_page'      => 100,
                    'updated_after' => $lastSync,
                ]));

            if ($response->failed()) {
                $this->error("Gagal: HTTP {$response->status()}");
                return self::FAILURE;
            }

            $payload  = $response->json();
            $lastPage = $payload['meta']['last_page'] ?? 1;
            $syncedAt = $payload['synced_at'] ?? now()->toIso8601String();

            foreach ($payload['data'] as $data) {
                // 1. Update/Create User
                $user = User::updateOrCreate(
                    ['sipetra_id' => $data['sipetra_id']],
                    [
                        'name'          => $data['name'],
                        'email'         => $data['email'] ?? "no-email-{$data['sipetra_id']}@bps.go.id",
                        'avatar_url'    => $data['avatar_url'],
                        'identity_type' => $data['identity_type'],
                        'is_active'     => $data['is_active'],
                        'nip'           => $data['nip'],
                        'jabatan'       => $data['jabatan'],
                        'password'      => $this->option('full') ? Hash::make('password123') : null, // Hanya set pass jika baru/full
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $user->update(['password' => Hash::make('password123')]);
                    
                    // Assign Role
                    if ($data['identity_type'] === 'pegawai' && $rolePegawai) {
                        $user->assignRole($rolePegawai);
                    } elseif ($data['identity_type'] === 'mitra' && $roleMitra) {
                        $user->assignRole($roleMitra);
                    }
                }

                // 2. Update/Create Pegawai
                $pegawai = Pegawai::updateOrCreate(
                    ['sipetra_id' => $data['sipetra_id']],
                    [
                        'user_id'        => $user->id,
                        'nip'            => $data['nip'],
                        'nip_baru'       => $data['nip_baru'],
                        'nip_lama'       => $data['nip'], // Asumsi nip lama = nip di sipetra jika tidak ada khusus
                        'sobat_id'       => $data['sobat_id'],
                        'jabatan'        => $data['jabatan'],
                        'unit_kerja'     => $data['unit_kerja'],
                        'kd_satker'      => $data['kd_satker'],
                        'jenis_kelamin'  => $data['gender'],
                        'golongan'       => $data['golongan'],
                        'period'         => $data['period'],
                        'contract_start' => $data['contract_start'],
                        'contract_end'   => $data['contract_end'],
                    ]
                );

                $pegawai->wasRecentlyCreated ? $created++ : $updated++;
            }

            $page++;
        } while ($page <= $lastPage);

        Cache::put('sipetra_last_synced_at', $syncedAt, now()->addDays(30));
        $this->info("✅ Selesai. Dibuat: {$created}, Diupdate: {$updated}.");

        return self::SUCCESS;
    }
}
