<?php

namespace App\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WarehousePivot extends Pivot
{
    protected $casts = [
        'location' => 'json',
    ];
}
