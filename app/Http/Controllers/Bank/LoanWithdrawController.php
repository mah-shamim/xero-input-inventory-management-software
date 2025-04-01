<?php

namespace App\Http\Controllers\Bank;

use App\Bank\Loan;
use App\Bank\Loanwithdraw;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        return view('temp.bank.loanwithdraw.index',
            ['sanction' => auth()->user()->loans()->whereId($id)->with('loanwithdraws')->first()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {

        return view('temp.bank.loanwithdraw.create',
            ['sanction' => auth()->user()->loans()->whereId($id)->with('bank')->first(), 'transaction' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request, Loan $id)
    {
        $this->sanctionUpdate($request, $id);
        $this->loanWithdraw($request, $id);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loanWithdraw(Request $request, Loan $id)
    {
        $loanWithdraw = new Loanwithdraw($request->all());
        $loanWithdraw->user()->associate(auth()->user());
        $loanWithdraw->loan()->associate($id->first());
        $loanWithdraw->save();
    }

    private function sanctionUpdate(Request $request, Loan $id)
    {
        $value = $id->first()->sanction_paid;
        $value = $value == null ? $request->input('withdraw') : $request->input('withdraw') + $value;
        $id->first()->update([
            'sanction_paid' => $value,
        ]);
    }
}
