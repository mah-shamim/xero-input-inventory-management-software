<?php

namespace App\Http\Requests\Inventory;

use App\Models\Bank\Transaction;
use App\Models\Expense\Expense;
use App\Models\Income\Income;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;
use Illuminate\Foundation\Http\FormRequest;

class PaymentCrudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required',
            'paid' => 'required|gt:0',
            'model_object' => 'required',
            'payment_type' => 'required',
            'bank_id' => 'required',
            'cheque_number' => ($this->input('payment_type') === 3) ? 'required' : '',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'paid.gt' => 'amount must be greater than 0',
            'bank_id.required' => 'bank is required',
            'model_object.required' => $this->getModelObjectMessage(),
        ];
    }

    /**
     * @return string
     */
    private function getModelObjectMessage()
    {
        if ($this->input('model') === 'sale') {
            return 'ref is required';
        }
        if ($this->input('model') === 'purchase') {
            return 'bill no is required';
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        if ($this->input('multiple_ids')) {
            if ($this->input('model') === 'purchase') {
                $this->saveMultiplePurchaseAccount();

                return response()->json([
                    'type' => 'success',
                    'message' => 'Transaction has been created successfully',
                ]);
            }
        }
        if ($this->input('model') === 'expense') {
            $transaction = Transaction::create(array_merge(
                $this->all(),
                [
                    'type' => 'credit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'note' => $this->input('note'),
                    'ref_no' => $this->input('model_object')['ref'],
                    'account_id' => $this->input('model_object')['account_id'],
                    'userable_id' => $this->input('model_object')['userable_id'],
                    'payment_method' => payment_method($this->input('payment_type')),
                    'userable_type' => $this->input('model_object')['userable_type'],
                ])
            );
            $this->merge(['transaction_id' => $transaction->id]);
            $expense = Expense::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $expense->payments()->create($this->all());

            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been created successfully',
            ]);
        }

        if ($this->input('model') === 'income') {
            $transaction = Transaction::create(array_merge(
                $this->all(),
                [
                    'type' => 'debit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'note' => $this->input('note'),
                    'ref_no' => $this->input('model_object')['ref'],
                    'account_id' => $this->input('model_object')['account_id'],
                    'userable_id' => $this->input('model_object')['userable_id'],
                    'payment_method' => payment_method($this->input('payment_type')),
                    'userable_type' => $this->input('model_object')['userable_model'],
                ])
            );
            $this->merge(['transaction_id' => $transaction->id]);
            $income = Income::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $income->payments()->create($this->all());

            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been created successfully',
            ]);
        }

        if ($this->input('model') === 'sale') {
            $transaction = Transaction::create(array_merge(
                $this->all(),
                [
                    'type' => 'debit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'payment_method' => payment_method($this->input('payment_type')),
                ])
            );
            $this->merge(['transaction_id' => $transaction->id]);
            $sale = Sale::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $sale->payments()->create($this->all());

            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been created successfully',
            ]);
        }


//        array:14 [ // app/Http/Requests/Inventory/PaymentCrudRequest.php:158
//        "date" => "2024-01-18"
//  "bank_id" => 1
//  "model" => "purchase"
//  "id" => null
//  "model_object" => array:19 [
//        "id" => 23
//    "supplier_id" => 4
//    "company_id" => 3
//    "shipping_cost" => 0
//    "overall_discount" => 0
//    "total" => 37.45
//    "total_weight" => 0
//    "sum_fields" => []
//    "bill_no" => "cbncvsd"
//    "ref" => "2024-16-1-091655-1-2"
//    "status" => 1
//    "payment_status" => 1
//    "note" => null
//    "purchase_date" => "2024-01-16 00:00:00"
//    "deleted_at" => null
//    "created_at" => "2024-01-16 09:16:55"
//    "updated_at" => "2024-01-16 09:16:55"
//    "paid_total" => 0
//    "purchase_date_formatted" => "2024-01-16"
//  ]
//  "paid" => 37.45
//  "payment_type" => 1
//  "errors" => array:6 [
//        "model_object" => null
//    "id" => null
//    "date" => null
//    "payment_type" => null
//    "paid" => null
//    "model_id" => null
//  ]
//  "company_id" => 3
//  "user_date_format" => "YYYY-MM-DD"
//  "user_date_format_php" => "Y-m-d"
//  "type" => "credit"
//  "amount" => 37.45
//  "payment_method" => "cash"
//]

        if ($this->input('model') === 'purchase') {
            $transaction = Transaction::create(array_merge(
                $this->all(),
                [
                    'type' => 'credit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'payment_method' => payment_method($this->input('payment_type')),
                ])
            );
            $this->merge(['transaction_id' => $transaction->id]);
            $purchase = Purchase::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $purchase->payments()->create($this->all());

            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been created successfully',
            ]);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        if ($this->input('model') === 'income') {
            $purchase = Income::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $payment = $purchase->payments()->with('transaction')->where('id', $this->route('crud'))->first();
            $payment->update($this->all());
            $payment->transaction->update(array_merge(
                $this->all(),
                [
                    'type' => 'debit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'payment_method' => payment_method($this->input('payment_type')),
                ])
            );
        }
        if ($this->input('model') === 'sale') {
            //            dd('hello');
            $sale = Sale::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $payment = $sale->payments()->with('transaction')->where('id', $this->route('crud'))->first();
            $payment->update($this->all());
            $payment->transaction->update(array_merge(
                $this->all(),
                [
                    'type' => 'debit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'payment_method' => payment_method($this->input('payment_type')),
                ])
            );
        }
        if ($this->input('model') === 'purchase') {
            $purchase = Purchase::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $payment = $purchase->payments()->with('transaction')->where('id', $this->route('crud'))->first();
            $payment->update($this->all());
            $payment->transaction->update(array_merge(
                $this->all(),
                [
                    'type' => 'credit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'payment_method' => payment_method($this->input('payment_type')),
                ])
            );
        }
        if ($this->input('model') === 'expense') {
            $purchase = Expense::where('id', $this->input('model_object')['id'])
                ->where('company_id', compid())->first();
            $payment = $purchase->payments()->with('transaction')->where('id', $this->route('crud'))->first();
            $payment->update($this->all());
            $payment->transaction->update(array_merge(
                $this->all(),
                [
                    'type' => 'credit',
                    'amount' => $this->input('paid'),
                    'date' => $this->input('date'),
                    'payment_method' => payment_method($this->input('payment_type')),
                ])
            );
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Transaction has been updated successfully',
        ]);
    }

    private function saveMultiplePurchaseAccount()
    {
        $input_amount = $this->input('paid');
        foreach ($this->input('multiple_ids') as $id) {
            $purchase = Purchase::where('purchases.company_id', compid())
                ->leftJoin('payments as p', function ($payment) {
                    $payment->on('p.paymentable_id', '=', 'purchases.id')
                        ->where('p.paymentable_type', '=', Purchase::classPath());
                })
                ->where('purchases.id', $id)
                ->select('purchases.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->selectRaw('coalesce(purchases.total - coalesce(sum(p.paid),0)) as due')
                ->groupBy('purchases.id')
                ->first();

            if ($input_amount > 0 && $purchase->due > 0) {
                if ($input_amount > $purchase->due) {
                    $transaction = Transaction::create(array_merge(
                        $this->all(),
                        [
                            'type' => 'credit',
                            'amount' => $purchase->due,
                            'date' => $this->input('date'),
                            'payment_method' => payment_method($this->input('payment_type')),
                        ])
                    );
                    $this->merge(['transaction_id' => $transaction->id, 'paid' => $purchase->due]);
                    $purchase->payments()->create($this->all());
                    $input_amount = $input_amount - $purchase->due;
                } else {
                    $transaction = Transaction::create(array_merge(
                        $this->all(),
                        [
                            'type' => 'credit',
                            'amount' => $input_amount,
                            'date' => $this->input('date'),
                            'payment_method' => payment_method($this->input('payment_type')),
                        ])
                    );
                    $this->merge(['transaction_id' => $transaction->id, 'paid' => $input_amount]);
                    $purchase->payments()->create($this->all());
                    $input_amount = 0;
                }
            }
        }
    }
}
