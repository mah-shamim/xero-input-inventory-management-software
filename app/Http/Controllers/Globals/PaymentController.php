<?php

namespace App\Http\Controllers\Globals;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;

class PaymentController extends Controller
{
    public function getPaymentType($paymentable_type)
    {
        if($paymentable_type==='purchases'){
            return Purchase::classPath();
        }
        if($paymentable_type==='sales'){
            return Sale::classPath();
        }
    }
    public function paymentable_id_type($paymentable_id, $paymentable_type)
    {
        $payments = Payment::where('paymentable_id', $paymentable_id)
            ->where('paymentable_type', $this->getPaymentType($paymentable_type))
            ->get();
        return response()->json([
            'payments'=>$payments
        ]);
    }
}
