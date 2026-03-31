<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Ambil data nama dan email dari array 'user' yang dikirim oleh form
        $userData = $data['user'] ?? [];
        
        // 2. Buat objek User baru secara otomatis
        $newUser = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            // Berikan password default (misal: password123)
            'password' => Hash::make('password123'), 
        ]);

        // 3. (Opsional) Langsung beri role 'pegawai' pada user tersebut jika ada
        $role = Role::where('name', 'pegawai')->first();
        if ($role) {
            $newUser->syncRoles([$role->name]);
        }

        // 4. Masukkan user_id yang baru dibuat ke data Pegawai
        $data['user_id'] = $newUser->id;
        
        // 5. Buang array 'user' dari $data supaya tidak error DB "column user not found"
        unset($data['user']);

        // 6. Buat record Pegawai menggunakan data yang sudah rapi
        return static::getModel()::create($data);
    }
}
