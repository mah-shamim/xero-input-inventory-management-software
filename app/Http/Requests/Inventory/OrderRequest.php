<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            'order_date' => 'required',
            'customer_id' => 'required',
            'expected_shipping_date' => 'required',
            'order_no' => [
                'required',
                Rule::unique('orders')
                    ->where('company_id', compid())
                    ->ignore($this->route('sale_order'), 'id'),
            ],
            'items.*.price' => 'required',
            'items.*.unit' => 'required',
            'items.*.quantity' => 'required',
            'items.*.warehouse' => 'required',
            'item.*.unit_price' => 'required',
            'items.*.product_id' => 'required',
            'items.*.unit_price' => 'required',
            'total' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'items.*.price.required' => 'price is required',
            'items.*.unit.required' => 'unit is required',
            'items.*.quantity.required' => 'quantity is required',
            'items.*.warehouse.required' => 'warehouse is required',
            'items.*.unit_price.required' => 'warehouse is required',
        ];
    }
}
