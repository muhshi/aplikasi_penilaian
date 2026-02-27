<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeTahun extends Model
{
    protected $fillable = ['tahun', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
