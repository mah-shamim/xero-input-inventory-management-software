<?php

namespace App\Http\Requests\Inventory;

use App\Enums\PaymentTypeEnum;
use App\Rules\inventory\LocationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SalesRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

//    if pay>0
//      bank_id<-required

//if pay>0 and payment_type==2 || payment_type==3
//    bank_id<-required
    public function rules(): array
    {
//        dd($this->payment_type==3, $this->payment_type==2, $this->payment_type);
        return [
            'status' => 'required',
            'bank_id' => [
                Rule::requiredIf($this->payment_type==3 || $this->payment_type==2)
            ],
            'payment_type'=>['required', new Enum(PaymentTypeEnum::class)],
            'sales_date' => 'required',
            'customer_id' => 'required',
            'items.*.unit' => 'required',
            'payment_status' => 'required',
            'items.*.quantity' => 'required',
            'items.*.warehouse' => 'required',
//            'items.*.location' => ['required', new LocationRules($this->input('items.*.product_id'))],
            'items.*.unit_price' => 'required',
            'items.*.product_id' => 'required',
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
