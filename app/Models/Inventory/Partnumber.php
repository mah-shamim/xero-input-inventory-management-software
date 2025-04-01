<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Warehouse\Warehouse;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Partnumber extends Model
{
    protected $table = 'part_numbers';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'purchase_id',
        'sale_id',
        'part_number',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(request()->input('user_date_format_php').' H:i:s');
    }
}
