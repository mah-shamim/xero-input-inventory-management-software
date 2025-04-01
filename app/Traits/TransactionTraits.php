<?php

namespace App\Traits;

use App\Models\Bank\Bank;
use App\Models\Bank\Transaction;
use App\Http\Requests\Bank\TransferRequest;
use App\Models\Inventory\Customer;
use App\Models\Inventory\Otheruser;
use App\Models\Inventory\Supplier;
use App\Models\Payroll\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait TransactionTraits
{
    public function transfer(TransferRequest $transferRequest)
    {
        if ($transferRequest->input('id')) {
            return $transferRequest->update();
        }

        return $transferRequest->store();
    }

    public function create_bank_transaction(Request $request, $type): void
    {
        //        dd($request->input('userable_id')['model']);
        if (! $request->input('date')) {
            $request->merge(['date' => Carbon::now()]);
        }

        $request->merge([
            'type' => $type,
            'amount' => $request->input('paid'),
            'payment_method' => payment_method($request->input('payment_type')),
        ]);
        if ($request->input('payment_type') == 2 || $request->input('payment_type') == 3) {
            $this->storeTransaction($request);
        }
        if ($request->input('payment_type') == 1) {
            $bank = Bank::where('company_id', $request->input('company_id'))->where('type', 'cr')->first();
            if ($bank) {
                $request->merge(['bank_id' => $bank->id]);
                $this->storeTransaction($request);
            }
        }
    }

    public function update_bank_transaction(Request $request, $model, $type): void
    {
        if ($request->input('payment_type') == 2 || $request->input('payment_type') == 3) {
            $model->payments->first()->transaction()->update([
                'amount' => $request->input('paid'),
            ]);
        }

        if ($request->input('payment_type') == 1) {
            $transaction = $model->payments->first()->transaction()->first();
            if ($transaction) {
                $model->payments->first()->transaction()->update([
                    'amount' => $request->input('paid'),
                ]);
            }
        }
        //        else {
        //            $model->payments->first()->transaction()->delete();
        //            $model->payments->first()->update([ 'transaction_id' => null ]);
        //        }
    }

    public function update_bank_without_pivot_table(Request $request, $model, $type)
    {
        $model->transaction()->update([
            'amount' => $request->input('paid'),
        ]);
    }

    /**
     * @return mixed
     */
    public function storeTransaction(Request $request)
    {
        $transaction = Transaction::create($request->all());
        $request->merge(['transaction_id' => $transaction->id]);
    }

    public function indexQuery(\Illuminate\Database\Eloquent\Builder $transactions): void
    {
        if (request()->get('bank')) {
            $transactions->where('transactions.bank_id', request()->get('bank'));
        }

        if (request()->get('payment_method')) {
            $transactions->where('transactions.payment_method', request()->get('payment_method'));
        }

        if (request()->get('transaction_number')) {
            $transactions->where('transactions.transaction_number', 'like', request()->get('transaction_number').'%');
        }

        if (request()->get('name_id')) {
            $transactions->whereHasMorph(
                'userable',
                [Customer::class, Supplier::class, Otheruser::class],
                function ($query) {
                    $query->where('name', 'like', '%'.request()->get('name_id').'%')
                        ->orWhere('code', request()->get('name_id'));
                })->orWhereHasMorph(
                    'userable',
                    Employee::class,
                    function ($query) {
                        $query->where('name', 'like', '%'.request()->get('name_id').'%')
                            ->orWhere('employee_id', request()->get('name_id'));
                    }
                );
        }

        if (request()->get('id')) {
            $transactions->where('transactions.id', request()->get('id'));
        }
        if (request()->get('date')) {
            if (count(request()->input('date')) === 1) {
                $transactions->whereDate('transactions.date', request()->input('date')[0]);
            }
            if (count(request()->input('date')) == 2) {

                if (Carbon::parse(request()->input('date')[0]) < Carbon::parse(request()->input('date')[1])) {
                    $transactions->whereBetween('transactions.date', request()->get('date'));
                } else {
                    $transactions->whereBetween('transactions.date', array_reverse(request()->get('date')));
                }

            }
        }
        if (request()->get('account_id')) {
            $transactions->where('account_id', request()->get('account_id'));
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $transactions->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }
    }
}
