<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'sipetra_id',
        'user_id',
        'penilai_id',
        'nip',
        'nip_baru',
        'nip_lama',
        'sobat_id',
        'no_hp',
        'jabatan',
        'unit_kerja',
        'kd_satker',
        'jenis_kelamin',
        'pangkat',
        'golongan',
        'period',
        'contract_start',
        'contract_end',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end' => 'date',
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
