<?php

namespace App\Models\Inventory\Warehouse;

use App\Enums\WarehouseStorageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseIsle extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    protected $casts = [
        'type' => WarehouseStorageEnum::class,
    ];

    public function racks()
    {
        return $this->hasMany(WarehouseRack::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
