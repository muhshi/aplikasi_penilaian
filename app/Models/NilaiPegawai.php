<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiPegawai extends Model
{
    protected $table = 'nilai_pegawai';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'kualitas',
        'kuantitas',
        'perilaku',
        'nilai_akhir',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'kualitas' => 'decimal:2',
        'kuantitas' => 'decimal:2',
        'perilaku' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
