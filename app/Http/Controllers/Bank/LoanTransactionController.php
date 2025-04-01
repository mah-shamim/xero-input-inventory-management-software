<?php

namespace App\Http\Controllers\Bank;

use App\Bank\LoanTransaction;
use App\Bank\Loanwithdraw;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanTransactionController extends Controller
{
    public function index($id)
    {
        return view('temp.bank.loantransaction.index', [
            'loanid' => auth()->user()->loanwithdraws()->whereId($id)->with('loanTransactions')->first(),
        ]);
    }

    public function create($id)
    {
        return view('temp.bank.loantransaction.create', [
            'withdrawid' => auth()->user()->loanwithdraws()->whereId($id)->with(['loan' => function ($q) {
                $q->with('bank');
            }])->first()]);
    }

    public function store(Request $request, Loanwithdraw $id)
    {
        //        return $request->all();
        $loanid = $id->first();
        $value = (float) $loanid->withdraw_paid ? $loanid->withdraw_paid : 0;
        $value2 = (float) $request->amount;

        $loanTransaction = new LoanTransaction($request->all());
        $loanTransaction->user()->associate(auth()->user());
        $loanTransaction->loanwithdraw()->associate($loanid);
        $loanTransaction->save();
        $loanid->update(['withdraw_paid' => $value + $value2]);

        return back();
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
