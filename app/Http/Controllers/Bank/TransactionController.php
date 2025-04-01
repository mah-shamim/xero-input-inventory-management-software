<?php

namespace App\Http\Controllers\Bank;

use App\Models\Account;
use App\Models\Bank\Bank;
use App\Models\Bank\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bank\TransactionFromRequest;
use App\Traits\TransactionTraits;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use TransactionTraits;

    public function index()
    {
        $transactions = Transaction::with('bank', 'payment', 'transfer', 'returns.returnable', 'userable')
            ->leftjoin('buildevents as be', 'be.transaction_id', '=', 'transactions.id')
            ->leftJoin('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->leftJoin('salaries', 'salaries.transaction_id', '=', 'transactions.id')
            ->where('transactions.company_id', request()->input('company_id'))
            ->select(
                'transactions.*',
                'be.id as buildevent_id',
                'accounts.name as account_name',
                'salaries.id as salary_id',
                DB::raw("(
                CASE 
                        WHEN transactions.type = 'credit' THEN transactions.amount 
                        ELSE 0 
                        END) AS withdraw
                        "
                ),
                DB::raw("(
                CASE 
                        WHEN transactions.type = 'debit' THEN transactions.amount 
                        ELSE 0 
                        END) AS deposit
                        "
                )
            )
            ->groupBy('transactions.id');

        $this->indexQuery($transactions);

        $transactions = $transactions->paginate(itemPerPage());

        return response()->json([
            'banks' => Bank::select('id', 'name')->where('company_id', request()->input('company_id'))->get(),
            'transactions' => $transactions,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function create()
    {
        $accounts = Account::accountByGroupText()
            ->select('accounts.*', 'a.id as parent_id', 'a.name as group')
            ->groupBy('accounts.id')
            ->orderBy('accounts.parent_id')
            ->get();

        return response()->json($accounts)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function store(TransactionFromRequest $request)
    {
        return $request->save();
    }

    public function edit($id)
    {
        return view('temp.bank.transaction.edit', ['transaction' => auth()->user()->transactions()->where('id', $id)->with('bank')->first()]);
    }

    public function update(TransactionFromRequest $request, Transaction $transaction)
    {
        return $request->update($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('company_id', request()->input('company_id'))->where('id', $id)->first();
        Transaction::where('company_id', request()->input('company_id'))->where('refer_id', $transaction->id)->delete();
        if ($transaction->delete()) {
            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been deleted successfully',
            ]);
        }
    }
}
