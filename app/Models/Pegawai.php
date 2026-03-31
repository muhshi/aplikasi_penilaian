<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'penilai_id', // ID Ketua Tim as assessor
        'nip',
        'nip_lama',
        'no_hp',
        'jabatan',
        'pangkat',
        'golongan',
    ];

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nilaiKipapps()
    {
        return $this->hasMany(NilaiKipapp::class, 'nip_lama', 'nip_lama');
    }
}
