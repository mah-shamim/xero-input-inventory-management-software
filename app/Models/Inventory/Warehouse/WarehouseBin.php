<?php

namespace App\Models\Inventory\Warehouse;

use App\Enums\WarehouseStorageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseBin extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'is_default'];

    protected $casts = [
        'type' => WarehouseStorageEnum::class,
        'is_default' => 'boolean',
    ];

    public function rack()
    {
        $this->belongsTo(WarehouseRack::class);
    }
}
