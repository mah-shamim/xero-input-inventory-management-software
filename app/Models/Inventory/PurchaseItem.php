<?php

namespace App\Models\Inventory;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table='purchase_items';

    protected $guarded = ['id', 'product_id', 'purchase_id', 'warehouse_id'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
