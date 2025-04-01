<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class WarrantyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warranty_date'=>'required',
            'product_id'=>'required',
            'customer_id'=>'required',
            'status'=>'required',
        ];
    }
}
