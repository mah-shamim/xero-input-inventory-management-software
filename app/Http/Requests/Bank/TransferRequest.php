<?php

namespace App\Http\Requests\Bank;

use App\Bank\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required',
            'bank_to' => 'required',
            'bank_from' => 'required',
            'amount' => 'required|numeric|min:0|not_in:0',
        ];
    }

    public function store()
    {
        $transaction = Transaction::create([
            'type' => 'credit',
            'payment_method' => 'transfer',
            'note' => $this->input('note'),
            'date' => $this->input('date'),
            'amount' => $this->input('amount'),
            'transfer_id' => $this->input('bank_to'),
            'bank_id' => $this->input('bank_from'),
            'company_id' => $this->input('company_id'),
        ]);
        $transaction2 = Transaction::create([
            'type' => 'debit',
            'payment_method' => 'transfer',
            'date' => $this->input('date'),
            'note' => $this->input('note'),
            'amount' => $this->input('amount'),
            'bank_id' => $this->input('bank_to'),
            'transfer_id' => $this->input('bank_from'),
            'company_id' => $this->input('company_id'),
        ]);

        $transaction->update(['refer_id' => $transaction2->id]);
        $transaction2->update(['refer_id' => $transaction->id]);

        return response()->json([
            'type' => 'success',
            'message' => 'Transfer has been successfully completed',
        ]);
    }

    public function update()
    {
        $transaction = Transaction::where('id', $this->input('id'))
            ->where('company_id', $this->input('company_id'))
            ->first();

        $transaction2 = Transaction::where('refer_id', $this->input('id'))
            ->where('company_id', $this->input('company_id'))
            ->first();

        $transaction->update([
            'note' => $this->input('note'),
            'date' => $this->input('date'),
            'amount' => $this->input('amount'),
            'transfer_id' => $this->input('bank_to'),
            'bank_id' => $this->input('bank_from'),
            'company_id' => $this->input('company_id'),
        ]);
        $transaction2->update([
            'note' => $this->input('note'),
            'date' => $this->input('date'),
            'amount' => $this->input('amount'),
            'bank_id' => $this->input('bank_to'),
            'transfer_id' => $this->input('bank_from'),
            'company_id' => $this->input('company_id'),
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Transfer has been successfully updated',
        ]);
    }
}
