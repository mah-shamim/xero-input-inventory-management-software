<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_type',
        'transaction_number',
        'paid',
    ];

    public function paymentable()
    {
        return $this->morphTo();
    }
}
