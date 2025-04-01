<?php

namespace App\Models\Inventory\Views;

use App\Models\Inventory\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseViewTotalDue extends Model
{
    protected $table = 'view_purchases_total_due';

    public function purchases()
    {
        return $this->belongsTo(Purchase::class,'id');
    }
}
