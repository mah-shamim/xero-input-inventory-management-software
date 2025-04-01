<?php

namespace App\Models\Inventory\Warehouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePicking extends Model
{
    use HasFactory;

    public $fillable = [
        'type',
        'location',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
