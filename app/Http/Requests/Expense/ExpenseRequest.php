<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'ref' => [
                'required',
                Rule::unique('expenses')
                    ->where('company_id', compid())
                    ->ignore($this->route('expense'), 'id'),
            ],
            'amount' => 'required',
            'account_id' => 'required',
            'userable_id' => 'required',
            'expense_date' => 'required',
            'warehouse_id' => 'required|numeric|min:0|not_in:0',
        ];
    }

    public function messages()
    {
        return [
            'ref.required' => 'The bill no is required',
            'account_id.required' => 'The account is required',
            'userable_id.required' => 'The user is required',
            'warehouse_id.required' => 'The warehouse is required',
            'expense_date.required' => 'The bill date is required',
        ];
    }
}
