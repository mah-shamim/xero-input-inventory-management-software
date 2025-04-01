<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\Bank\Bank;
use App\Models\Bank\Transaction;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;
use Carbon\Carbon;

trait Ledger
{
    /**
     * @return mixed
     */
    public function ledgerOfDynamicAccount($account_id)
    {
        $transactions = Transaction::select('transactions.*')
            ->with('userable', 'bank', 'account')
            ->selectRaw(" sum(coalesce(case when type='debit' then amount end,0)) as debit")
            ->selectRaw(" sum(coalesce(case when type='credit' then amount end,0)) as credit")
            ->selectRaw('
            (select 
            sum(coalesce(case when type=\'debit\' then amount end,0)) 
            from transactions t2 where t2.id<=transactions.id and t2.account_id = '.$account_id.' and t2.company_id = '.request()->input('company_id').') as total_debits
            ')
            ->selectRaw('
            (select 
            sum(coalesce(case when type=\'credit\' then amount end,0)) 
            from transactions t2 where t2.id<=transactions.id and t2.account_id = '.$account_id.' and t2.company_id = '.request()->input('company_id').') as total_credits
            ')
            ->selectRaw('
            (select 
            sum(coalesce(case when type=\'debit\' then amount end,0)) 
            - sum(coalesce(case when type=\'credit\' then amount end,0)) 
            from transactions t2 where t2.id<=transactions.id and t2.account_id = '.$account_id.' and t2.company_id = '.request()->input('company_id').') as balance
            ')
            ->where('transactions.company_id', request()->input('company_id'))
            ->where('transactions.account_id', $account_id)
            ->groupBy('transactions.id')
            ->orderBy('date', 'desc');

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

        return $transactions->paginate(itemPerPage());
    }

    /**
     * @return mixed
     */
    public function listOfBankWithRunningBalance()
    {
        $banks = Bank::where('company_id', request()->input('company_id'))->select('id', 'name')->get();
        if ($banks) {
            foreach ($banks as $bank) {
                $bank->running_balance = $this->bankTransactionTotal_debit_credit($bank->id) ? $this->bankTransactionTotal_debit_credit($bank->id)->running_balance : 0;
            }
        }

        return $banks;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function bankTransactionTotal_debit_credit($bank_id)
    {
        return Transaction::with('bank')->where('bank_id', $bank_id)
            ->select('transactions.*')
            ->selectRaw('
            sum(coalesce(case when type=\'debit\' then amount end, 0)) as total_debits,
            sum(coalesce(case when type=\'credit\' then amount end, 0)) as total_credits,
            SUM(COALESCE(CASE WHEN type = \'debit\' THEN amount END,0)) - SUM(COALESCE(CASE WHEN type = \'credit\' THEN amount END,0)) running_balance 
            ')
            ->where('company_id', request()->input('company_id'))
            ->groupBy('transactions.bank_id')
            ->havingRaw('running_balance<>0')
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function account_total_debit_total_credit($account_id)
    {
        $transaction = Transaction::with('bank', 'account')
            ->where('account_id', $account_id)
            ->select('transactions.*')
            ->selectRaw('
            sum(coalesce(case when type=\'debit\' then amount end, 0)) as total_debits,
            sum(coalesce(case when type=\'credit\' then amount end, 0)) as total_credits,
            SUM(COALESCE(CASE WHEN type = \'debit\' THEN amount END,0)) - SUM(COALESCE(CASE WHEN type = \'credit\' THEN amount END,0)) running_balance 
            ')
            ->where('company_id', request()->input('company_id'))
            ->groupBy('transactions.account_id')
            ->havingRaw('running_balance<>0');

        if ((request()->get('date') && count(request()->get('date')) > 1) && request()->get('with_total') == 1) {
            $transaction->whereBetween('transactions.date', request()->get('date'));
        }

        return $transaction->first();
    }

    /**
     * @return mixed
     */
    public function bankTransaction_ledger($bank_id)
    {
        return Transaction::select('transactions.*')
            ->selectRaw(" sum(coalesce(case when type='debit' then amount end,0)) as debit")
            ->selectRaw(" sum(coalesce(case when type='credit' then amount end,0)) as credit")
            ->selectRaw('
            (select 
            sum(coalesce(case when type=\'debit\' then amount end,0)) 
            from transactions t2 where t2.id<=transactions.id and t2.bank_id = '.$bank_id.' and t2.company_id = '.request()->input('company_id').') as total_debits
            ')
            ->selectRaw('
            (select 
            sum(coalesce(case when type=\'credit\' then amount end,0)) 
            from transactions t2 where t2.id<=transactions.id and t2.bank_id = '.$bank_id.' and t2.company_id = '.request()->input('company_id').') as total_credits
            ')
            ->selectRaw('
            (select 
            sum(coalesce(case when type=\'debit\' then amount end,0)) 
            - sum(coalesce(case when type=\'credit\' then amount end,0)) 
            from transactions t2 where t2.id<=transactions.id and t2.bank_id = '.$bank_id.' and t2.company_id = '.request()->input('company_id').') as running_balance
            ')
            ->where('company_id', request()->input('company_id'))
            ->where('bank_id', $bank_id)
            ->groupBy('transactions.id');
    }

    /**
     * @return mixed
     * return object
     */
    public function getParent($account_id): string
    {
        $parent_id = Account::where('id', $account_id)->where('company_id', compid())->first()->parent_id;
        $parent_obj = Account::where('id', $parent_id)->where('company_id', compid())->first();

        return $parent_obj;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerLedger()
    {
        $customer_id = request()->input('user');
        $dates = dateLowToHighWithTime(request()->input('date'));

        $up_to_balance_date = $dates[0];
        $all_transactions = $this->transaction_query_date_between(
            $customer_id,
            $dates[0],
            $dates[1],
            Sale::class,
            "App\Models\Inventory\Sale",
            "App\Inventory\Customer",
            'customer_id'
        )
            ->take(itemPerPage())
            ->get()
            ->collect();

        $first_transaction = $this->transaction_query(
            $customer_id,
            "App\Models\Inventory\Sale",
            Sale::class,
            'customer_id',
            "App\Inventory\Customer"
        )
            ->orderBy('date', 'asc')
            ->first();

        $all_sales = $this->sale_query($customer_id)
            ->whereBetween('sales_date', [$dates[0], $dates[1]])
            ->orderBy('sales_date', 'asc')->take(itemPerPage())
            ->get()
            ->collect();

        $transaction_sum_received = $this->getSum('debit', $customer_id, $first_transaction->date, $up_to_balance_date, 'sale');

        $transaction_sum_paid = $this->getSum('credit', $customer_id, $first_transaction->date, $up_to_balance_date, 'sale');

        $transaction_sum = $transaction_sum_received - $transaction_sum_paid;

        $first_sale = $this->sale_query($customer_id)
            ->first();

        $sale_sum = Sale::fromCompany()
            ->whereCustomer_id($customer_id)
            ->whereBetween('sales_date', [$first_sale->date, $up_to_balance_date])
            ->sum('total');

        $first_balance = $sale_sum + $transaction_sum;

        $merge = $all_transactions->merge($all_sales);

        $merge = collect($merge)->sortBy('date')->take(itemPerPage())->values();

        foreach ($merge as $index => $item) {
            $this->customer_balance_calculation($merge, $index, $first_balance);
            if (isset($item->sale_total)) {
                $item['ledger_type'] = 'debit';
                $item['value'] = $item->sale_total;
            }
            if (isset($item->type) and $item->type === 'debit') {
                $item['ledger_type'] = 'credit';
                $item['value'] = $item->amount;
            }
            if (isset($item->type) and $item->type === 'credit') {
                $item['ledger_type'] = 'debit';
                $item['value'] = $item->amount;
            }
        }

        return response()->json(['data' => $merge])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function supplierLedger()
    {
        $supplier_id = request()->input('user');
        $dates = dateLowToHigh(request()->input('date'));
        $up_to_balance_date = $dates[0];
        $all_transactions = $this->transaction_query_date_between(
            $supplier_id,
            Carbon::parse($dates[0])->startOfDay(),
            Carbon::parse($dates[1])->endOfDay()
        )
            ->take(itemPerPage())
            ->get()
            ->collect();

        $first_transaction = $this->transaction_query(
            $supplier_id,
            Purchase::classPath(),
            Purchase::class)
            ->orderBy('date', 'asc')
            ->first();

        $all_purchases = $this->purchase_query($supplier_id)
            ->whereBetween('purchase_date', [$dates[0], $dates[1]])
            ->orderBy('date', 'asc')->take(itemPerPage())
            ->get()
            ->collect();

        $transaction_sum_received = $this->getSum('debit', $supplier_id, $first_transaction->date, $up_to_balance_date);

        $transaction_sum_paid = $this->getSum('credit', $supplier_id, $first_transaction->date, $up_to_balance_date);

        $transaction_sum = $transaction_sum_paid - $transaction_sum_received;

        $first_purchase = $this->purchase_query($supplier_id)
            ->first();

        $purchase_sum = Purchase::fromCompany()
            ->whereSupplier_id($supplier_id)
            ->whereBetween('purchase_date', [$first_purchase->date, $up_to_balance_date])
            ->sum('total');

        $first_balance = $purchase_sum + $transaction_sum;

        //need to find first balance

        $merge = $all_transactions->merge($all_purchases);

        $merge = collect($merge)->sortBy('date')->take(itemPerPage())->values();

        foreach ($merge as $index => $item) {
            $this->supplier_balance_calculation($merge, $index, $first_balance);
            if (isset($item->purchase_total)) {
                $item['ledger_type'] = 'credit';
                $item['value'] = $item->purchase_total;
            }
            if (isset($item->type) and $item->type === 'debit') {
                $item['ledger_type'] = 'credit';
                $item['value'] = $item->amount;
            }
            if (isset($item->type) and $item->type === 'credit') {
                $item['ledger_type'] = 'debit';
                $item['value'] = $item->amount;
            }
        }

        return response()->json(['data' => $merge])->setEncodingOptions(JSON_NUMERIC_CHECK);

    }

    private function customer_balance_calculation($items, $index, $first_balance)
    {
        if ($index === 0) {
            if (isset($items[$index]->sale_total)) {
                $items[$index]['balance'] = round($items[$index]->sale_total + $first_balance, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'debit') {
                $items[$index]['balance'] = round($items[$index]->amount + $first_balance, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'credit') {
                $items[$index]['balance'] = round($items[$index]->amount - $first_balance, 2);
            }
        } else {
            $previousIndex = $index - 1;

            if ($items[$index]->type === 'credit' and $items[$index]->amount === 15) {
                dd($items[$index]);
            }

            if (isset($items[$index]->sale_total)) {
                $items[$index]['balance'] = round($items[$previousIndex]->balance + $items[$index]->sale_total, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'debit') {
                $items[$index]['balance'] = round($items[$previousIndex]->balance - $items[$index]->amount, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'credit') {
                $items[$index]['balance'] = round($items[$previousIndex]->balance - $items[$index]->amount, 2);
            }
        }
    }

    /**
     * @return void
     */
    private function supplier_balance_calculation($items, $index, $first_balance)
    {
        if ($index === 0) {
            if (isset($items[$index]->purchase_total)) {
                $items[$index]['balance'] = round($items[$index]->purchase_total + $first_balance, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'debit') {
                $items[$index]['balance'] = round($items[$index]->amount + $first_balance, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'credit') {
                $items[$index]['balance'] = round($items[$index]->amount - $first_balance, 2);
            }
        } else {
            $previousIndex = $index - 1;
            if (isset($items[$index]->purchase_total)) {
                $items[$index]['balance'] = round($items[$previousIndex]->balance + $items[$index]->purchase_total, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'debit') {
                $items[$index]['balance'] = round($items[$previousIndex]->balance + $items[$index]->amount, 2);
            }
            if (isset($items[$index]->type) and $items[$index]->type === 'credit') {
                $items[$index]['balance'] = round($items[$previousIndex]->balance - $items[$index]->amount, 2);
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseLedger()
    {
        $dates = dateLowToHigh(request()->input('date'));

        $first_purchase = Purchase::first();
        $first_date = $first_purchase->purchase_date;
        $up_to_balance_date = $dates[0];

        $purchase_query = Purchase::with(['activities:id,subject_id,causer_id,causer_type', 'activities.causer:id,name'])
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.company_id', compid())
            ->select(
                'purchases.id',
                'purchases.bill_no',
                'purchases.purchase_date',
                'purchases.purchase_date as date',
                'purchases.total as debit',
                'suppliers.name as supplier_name',
            )
            ->whereBetween('purchases.purchase_date', [$dates[0], $dates[1]]);

        $purchase_sum = Purchase::fromCompany()->whereBetween('purchase_date', [$first_date, $up_to_balance_date])->sum('total');

        //        dd([$first_date, $up_to_balance_date], $purchase_sum);

        $purchase = $purchase_query->get()->collect();

        $payments = Payment::with(['activities:id,subject_id,causer_id,causer_type', 'activities.causer:id,name'])
            ->join('purchases', 'payments.paymentable_id', '=', 'purchases.id')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->select(
                'payments.date',
                'payments.id',
                'payments.paid as credit',
                'payments.payment_type', //use condition if 1 = cash, 2 = credit card, 3 = bank
                'payments.balance',
                'payments.running_balance',
                'purchases.bill_no',
                'purchases.id as purchase_id',
                'suppliers.name as supplier_name'
            )
            ->whereHasMorph('paymentable', [Purchase::class], function ($q) {
                $q->where('company_id', compid());
            })
            ->where('paymentable_type', "App\Models\Inventory\Purchase")
            ->whereBetween('payments.date', [$dates[0], $dates[1]])
            ->get()->collect();

        $payment_sum = Payment::where('paymentable_type', "App\Models\Inventory\Purchase")
            ->whereHasMorph('paymentable', [Purchase::class], function ($q) {
                $q->where('company_id', compid());
            })
            ->whereBetween('date', [$first_date, $up_to_balance_date])->sum('paid');

        $first_balance = $purchase_sum - $payment_sum;

        $merge = $payments->merge($purchase);
        $merge = collect($merge)->sortBy('date')->values();

        foreach ($merge as $index => $item) {
            if ($index === 0) {
                $item->balance = isset($item->purchase_date)
                    ? $first_balance + $item['debit']
                    : $first_balance - $item['credit'];
            } else {
                $nextIndex = $index - 1;
                if (isset($item->purchase_date)) {
                    //                    var_dump(
                    //                        'index='.$index,
                    //                        'debit='.$item['debit'],
                    //                        'balance='.$merge[$nextIndex]['balance'],
                    //                        $item['debit']+$merge[$nextIndex]['balance']
                    //                    );
                    $item->balance = $item['debit'] + $merge[$nextIndex]['balance'];
                } else {
                    //                    var_dump(
                    //                        'index='.$index,
                    //                        'credit='.$item['credit'],
                    //                        'balance='.$merge[$nextIndex]['balance'],
                    //                        $item['credit']-$merge[$nextIndex]['balance']
                    //                    );
                    $item->balance = $merge[$nextIndex]['balance'] - $item['credit'];
                }
            }
        }

        return response()->json(['data' => $merge])->setEncodingOptions(JSON_NUMERIC_CHECK);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function saleLedger()
    {
        $dates = dateLowToHigh(request()->input('date'));

        $first_sale = Sale::first();
        $first_date = $first_sale->sales_date;
        $up_to_balance_date = $dates[0];

        $sale_query = Sale::with(['activities:id,subject_id,causer_id,causer_type', 'activities.causer:id,name'])
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('sales.company_id', compid())
            ->select(
                'sales.id',
                'sales.ref',
                'sales.sales_date',
                'sales.sales_date as date',
                'sales.total as debit',
                'customers.name as customer_name',
            )
            ->whereBetween('sales.sales_date', [$dates[0], $dates[1]]);

        $sale_sum = Sale::whereBetween('sales_date', [$first_date, $up_to_balance_date])->sum('total');

        //        dd([$first_date, $up_to_balance_date], $sale_sum);

        $sale = $sale_query->get()->collect();

        $payments = Payment::with(['activities:id,subject_id,causer_id,causer_type', 'activities.causer:id,name'])
            ->join('sales', 'payments.paymentable_id', '=', 'sales.id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'payments.date',
                'payments.id',
                'payments.paid as credit',
                'payments.payment_type', //use condition if 1 = cash, 2 = credit card, 3 = bank
                'sales.ref',
                'sales.id as sale_id',
                'customers.name as customer_name'
            )
            ->whereHasMorph('paymentable', [Sale::class], function ($q) {
                $q->where('company_id', compid());
            })
            ->where('paymentable_type', "App\Models\Inventory\Sale")
            ->whereBetween('payments.date', [$dates[0], $dates[1]])
            ->get()->collect();

        $payment_sum = Payment::where('paymentable_type', "App\Models\Inventory\Sale")
            ->whereHasMorph('paymentable', [Sale::class], function ($q) {
                $q->where('company_id', compid());
            })
            ->whereBetween('date', [$first_date, $up_to_balance_date])->sum('paid');

        $first_balance = $sale_sum - $payment_sum;
        $merge = $sale->merge($payments);
        $merge = collect($merge)->sortBy('date')->values();

        foreach ($merge as $index => $item) {
            if ($index === 0) {
                $item->balance = isset($item->sales_date)
                    ? round($first_balance + $item['debit'], 2)
                    : round($first_balance - $item['credit'], 2);
            } else {
                $nextIndex = $index - 1;
                if (isset($item->sales_date)) {
                    //                    var_dump(
                    //                        'index='.$index,
                    //                        'debit='.$item['debit'],
                    //                        'balance='.$merge[$nextIndex]['balance'],
                    //                        $item['debit']+$merge[$nextIndex]['balance']
                    //                    );
                    $item->balance = round($item['debit'] + $merge[$nextIndex]['balance'], 2);
                } else {
                    //                    var_dump(
                    //                        'index='.$index,
                    //                        'credit='.$item['credit'],
                    //                        'balance='.$merge[$nextIndex]['balance'],
                    //                        $item['credit']-$merge[$nextIndex]['balance']
                    //                    );
                    $item->balance = round($merge[$nextIndex]['balance'] - $item['credit'], 2);
                }
            }
        }

        return response()->json(['data' => $merge])->setEncodingOptions(JSON_NUMERIC_CHECK);

    }

    /**
     * @param  string  $user_column
     * @param  string  $transaction_userable_model
     */
    protected function transaction_query(
        $user_id,
        $paymentable_type,
        $class,
        string $user_column = 'supplier_id',
        string $transaction_userable_model = 'App\Models\Inventory\Supplier')
    {
        return Transaction::fromCompany()->whereHas('payment', function ($payments) use ($user_id, $paymentable_type, $class, $user_column) {
            $payments->where('paymentable_type', $paymentable_type)
                ->whereHasMorph('paymentable', [$class], function ($paymentable) use ($user_id, $user_column) {
                    $paymentable->where($user_column, $user_id);
                });
        })
            ->orWhere('transactions.userable_model', $transaction_userable_model);

    }

    /**
     * @param  string  $class
     * @param  string  $paymentable_type
     * @param  string  $transaction_userable_model
     * @param  string  $user_column
     */
    protected function transaction_query_date_between($user_id,
        $first_date,
        $last_date,
        $class = Purchase::class,
        string $paymentable_type = "App\Models\Inventory\Purchase",
        string $transaction_userable_model = "App\Inventory\Supplier",
        string $user_column = 'supplier_id')
    {
        //        dd($user_id, $first_date, $last_date, $class, $paymentable_type, [ 'user model' => $transaction_userable_model ], [ 'column' => $user_column ]);
        return Transaction::where('transactions.company_id', compid())
            ->whereBetween('transactions.date', [$first_date, $last_date])
            ->whereHas('payment', function ($payments) use ($user_id, $paymentable_type, $class, $user_column) {
                $payments->where('paymentable_type', $paymentable_type)
                    ->whereHasMorph('paymentable', [$class], function ($paymentable) use ($user_id, $user_column) {
                        $paymentable->where($user_column, $user_id);
                    });
            })
            ->orWhere(function ($query) use ($first_date, $last_date, $transaction_userable_model) {
                $query->where('transactions.userable_model', $transaction_userable_model)
                    ->whereBetween('transactions.date', [$first_date, $last_date]);
            });
    }

    /**
     * @param  string  $transaction_type
     */
    protected function getSum($type, $user_id, $first_date, $last_date, string $transaction_type = 'purchase')
    {
        $call_method = $transaction_type === 'purchase'
            ? 'transactionHasPurchaseForASupplier'
            : 'transactionHasSaleForACustomer';

        return Transaction::fromCompany()
            ->whereType($type)
            ->whereBetween('date', [$first_date, $last_date])
            ->{$call_method}($user_id)
            ->sum('amount');
    }

    /**
     * @return mixed
     */
    protected function sale_query($customer_id)
    {
        return Sale::fromCompany()
            ->where('customer_id', $customer_id)
            ->select(
                'id as sale_id',
                'ref',
                'customer_id',
                'total as sale_total',
                'sales_date',
                'sales_date as date'
            );
    }

    protected function purchase_query($supplier_id)
    {
        return Purchase::where('company_id', compid())
            ->where('supplier_id', $supplier_id)
            ->select(
                'id as purchase_id',
                'bill_no as purchase_bill_no',
                'supplier_id',
                'total as purchase_total',
                'purchase_date',
                'purchase_date as date'
            );
    }
}
