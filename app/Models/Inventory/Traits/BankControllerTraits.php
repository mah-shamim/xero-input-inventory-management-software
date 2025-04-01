<?php

namespace App\Models\Inventory\Traits;


use App\Models\Bank\Bank;
use App\Models\Bank\Transaction;

trait BankControllerTraits
{
    public function total()
    {
        $total_debit = Transaction::where('company_id', request()->input('company_id'))->where('type', 'debit')->sum('amount');
        $total_credit = Transaction::where('company_id', request()->input('company_id'))->where('type', 'credit')->sum('amount');

        return response()->json([
            'total_debit' => $total_debit,
            'total_credit' => $total_credit,
            'ending_balance' => $total_debit - $total_credit,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function list()
    {
        if (request()->get('with_running_balance')) {
            $bank = $this->listOfBankWithRunningBalance();
        } else {
            $bank = Bank::where('company_id', request()->input('company_id'))->select('id', 'name')->get();
        }

        return response()->json($bank)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
