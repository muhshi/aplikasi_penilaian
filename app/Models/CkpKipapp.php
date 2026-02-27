<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CkpKipapp extends Model
{
    protected $table = 'ckp_kipapp';

    protected $fillable = [
        'user_id',
        'nama_file',
        'bulan',
        'tahun',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
