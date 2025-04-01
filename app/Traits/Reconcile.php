<?php

namespace App\Traits;

use App\Models\Bank\Transaction;

trait Reconcile
{
    public function reconcile()
    {
        $transactions = Transaction::where('payment_method', 'cheque')
            ->leftjoin('banks', 'bank_id', '=', 'banks.id')
            ->orWhere('payment_method', 'credit card')
            ->fromCompany()
            ->select('*', 'banks.name as bank_name', 'banks.id as bank_id')
            ->get();

        return response()->json(
            [
                'transactions' => $transactions,
                'ending_balance' => 0,
                'beginning_balance' => 0,
            ]
        )->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
