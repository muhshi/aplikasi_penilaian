<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeTahun extends Model
{
    protected $fillable = ['tahun', 'is_active', 'periode_aktif'];

    protected $casts = [
        'is_active' => 'boolean',
        'periode_aktif' => 'array',
    ];
}
