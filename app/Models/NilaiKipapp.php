<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiKipapp extends Model
{
    protected $table = 'nilai_kipapp';

    protected $fillable = [
        'nip_lama',
        'bulan',
        'tahun',
        'rata_rata_hasil_kerja',
        'rata_rata_perilaku',
        'nilai_rata_rata',
        'predikat_kinerja',
        'nilai_prestasi',
        'nama_file', // Path file PDF dokumen Nilai KIPAPP
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'rata_rata_hasil_kerja' => 'decimal:2',
        'rata_rata_perilaku' => 'decimal:2',
        'nilai_rata_rata' => 'decimal:2',
        'nilai_prestasi' => 'decimal:2',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip_lama', 'nip_lama');
    }
}
