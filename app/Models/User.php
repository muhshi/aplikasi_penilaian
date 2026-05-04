<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, HasPanelShield, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'is_active',
        'identity_type',
        'password',
        'sipetra_id',
        'nip',
        'jabatan',
        'must_change_password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    public function ckpKipapps()
    {
        return $this->hasMany(CkpKipapp::class);
    }

    public function nilaiPegawais()
    {
        return $this->hasMany(NilaiPegawai::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}