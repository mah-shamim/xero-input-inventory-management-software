<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Validation\Rule;
use App\Rules\inventory\LocationRules;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required',
            'bank_id' => (! $this->route('purchase') && $this->input('paid') > 0 && $this->input('payment_type') == 2) || (! $this->route('purchase') && $this->input('paid') > 0 && $this->input('payment_type')) == 3 ? 'required' : [],
            'supplier_id' => 'required',
            'shipping_cost'=> 'numeric|min:0,max:100000000.9999',
            'overall_discount'=>'numeric|min:0,max:100000000.9999',
            'items.*.unit' => 'required',
            'items.*.price' => 'required',
            'purchase_date' => 'required',
            'payment_status' => 'required',
            'items.*.quantity' => 'required',
            'items.*.warehouse' => 'required',
//            'items.*.location' => ['required', new LocationRules], //don't remove this line
            'items.*.unit_price' => 'required',
            'items.*.product_id' => 'required',
            'bill_no' => [
                'required',
                Rule::unique('purchases')
                    ->where('supplier_id', $this->input('supplier_id'))
                    ->ignore($this->route('purchase')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.unit.required' => 'The Unit field is required.',
            'items.*.product_id.required' => 'The Product field is required.',
            'items.*.quantity.required' => 'The Quantity field is required.',
            'items.*.warehouse.required' => 'The Warehouse field is required.',
            'items.*.location.required' => 'The Location field is required.',
            'items.*.unit_price.required' => 'The Unit Price field is required.',
        ];
    }
}
