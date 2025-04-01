<?php

namespace App\Traits;

use App\Models\Bank\Transaction;
use App\Models\Inventory\Returns;
use Illuminate\Http\Request;

trait ReturnControllerTraits
{
    public function transactionUpdate(Request $request, $returns, $returns_total): void
    {
        Transaction::where('id', $returns->transaction_id)
            ->where('company_id', $request->input('company_id'))
            ->update(['amount' => $returns_total]);
    }

    /**
     * @return mixed
     */
    public function getReturnSum(Request $request, string $model)
    {
        return Returns::where('company_id', $request->input('company_id'))
            ->where('returnable_id', $request->input('returnable_id'))
            ->where('returnable_type', $model)
            ->sum('amount');
    }

    /**
     * @return mixed
     */
    public function findReturnObject(Request $request, string $model)
    {
        $returns = Returns::where('id', $request->input('id'))
            ->where('company_id', $request->input('company_id'))
            ->where('returnable_id', $request->input('returnable_id'))
            ->where('returnable_type', $model)
            ->first();

        return $returns;
    }
}
