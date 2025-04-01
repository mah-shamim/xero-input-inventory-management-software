<?php

namespace App\Http\Controllers\Bank;

use App\Bank\Loan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LoanController extends Controller
{
    public function index()
    {
        return view('temp.bank.loans.index', ['banks' => auth()->user()->banks()->with('loans')->get()]);
    }

    public function create()
    {

        return view('temp.bank.loans.create', ['banks' => auth()->user()->banks()->get(), 'transaction' => null]);
    }

    public function store(Request $request)
    {
        //        return $request->all();
        $loan = new Loan($request->except('bank'));
        $loan->user()->associate(auth()->user());
        $loan->bank()->associate($request->input('bank'));
        $loan->save();

        return back();
    }

    public function show(Loan $loan)
    {
        //
    }

    public function edit(Loan $loan)
    {
        //
    }

    public function update(Request $request, Loan $loan)
    {
        //
    }

    public function destroy(Loan $loan)
    {
        //
    }
}
