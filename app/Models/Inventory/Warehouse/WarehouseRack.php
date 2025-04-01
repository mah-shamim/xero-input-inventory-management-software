<?php

namespace App\Models\Inventory\Warehouse;

use App\Enums\WarehouseStorageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseRack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    protected $casts = [
        'type' => WarehouseStorageEnum::class,
    ];

    public function bins()
    {
        return $this->hasMany(WarehouseBin::class);
    }

    public function isle()
    {
        return $this->belongsTo(WarehouseIsle::class);
    }
}
