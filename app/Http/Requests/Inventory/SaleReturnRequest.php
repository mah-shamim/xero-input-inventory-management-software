<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class SaleReturnRequest extends FormRequest
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
            'return.*.warehouse_id' => 'required',
            'return.*.amount' => 'required',
        ];
    }
}
//
//'data.*.name' => 'required|string',
//   'data.*.' => 'required|string'
