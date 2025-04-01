<?php

namespace App\Http\Requests\Bank;

use App\Models\Bank\Transaction;
use App\Models\Inventory\Payment;
use Illuminate\Foundation\Http\FormRequest;

class TransactionFromRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required',
            'type' => 'required',
            'ref_no' => 'required',
            'payment_method' => 'required',
            'bank_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'account' => 'required',
            'userable_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'userable_id.required' => $this->input('type') === 'debit' ? 'receive from is required' : 'pay to is require',
            'bank_id.required' => 'bank is required',
        ];
    }

    public function save()
    {
        $this->mergeData();
        try {
            $transaction = new Transaction($this->all());
            $transaction->bank()->associate($this->input('bank_id'));
            $transaction->save();

            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been created successfully',
            ]);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function update(Transaction $transaction)
    {
        //        dd($this->all());
        try {
            $this->mergeData();
            $transaction->fill($this->all());
            $transaction->bank()->associate($this->input('bank_id'));
            $payment = Payment::where('transaction_id', $this->input('id'))->first();
            if ($payment) {
                $payment->update([
                    'paid' => $this->input('amount'),
                    'payment_type' => payment_method_id($this->input('payment_method')),
                ]);
            }
            $transaction->save();

            return response()->json([
                'type' => 'success',
                'message' => 'Transaction has been updated successfully',
            ]);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }

    public function mergeData(): void
    {
        $this->merge(['account_id' => $this->input('account')['id']]);
        $this->merge(['userable_id' => $this->input('userable_id')['id']]);
    }
}
