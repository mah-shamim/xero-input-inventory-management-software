<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalequotationRequest extends FormRequest
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
            'customer_id' => 'required',
            'quotation_date' => 'required',
            'quotation_no' => [
                'required',
                Rule::unique('salequotations')
                    ->where('company_id', compid())
                    ->ignore($this->route('sale_quotation'), 'id'),
            ],
            'items.*.unit' => 'required',
            'items.*.quantity' => 'required',
            'items.*.product_id' => 'required',
            'items.*.price' => 'required',
            'items.*.unit_price' => 'required',
            'items.*.warehouse' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'items.*.unit.required' => 'required',
            'items.*.price.required' => 'required',
            'items.*.quantity.required' => 'required',
            'items.*.product_id.required' => 'required',
            'items.*.unit_price.required' => 'required',
            'items.*.warehouse.required' => 'required',
        ];
    }
}
