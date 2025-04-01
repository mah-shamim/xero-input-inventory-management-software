<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
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
            'income_date' => 'required',
            'ref' => 'required',
            'warehouse_id' => 'required',
            'category_id' => 'required',
            'amount' => 'required',
            'payment_method'=>'required'
        ];
    }
}
