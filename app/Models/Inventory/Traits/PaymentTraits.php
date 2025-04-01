<?php

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Payment;

trait PaymentTraits
{
    public function delete_payments_transactions(string $paymentable_type, $model): void
    {
        $payments = Payment::with('transaction')
            ->wherePaymentableType($paymentable_type)
            ->wherePaymentableId($model)->get();

        foreach ($payments as $item) {
            $item->transaction()->delete();
            $item->forceDelete();
        }
    }
}
