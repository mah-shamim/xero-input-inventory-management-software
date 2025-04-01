<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */


    public function rules(): array
    {
        return [
            'name' => 'required',
            'brand_id' => 'required',
            'price' => 'required',
            'categories' => 'required',
            'base_unit_id' => 'required',
            'buying_price' => 'required',
            'code' => [
                'required',
                Rule::unique('products')
                    ->where('company_id', compid())
                    ->ignore($this->input('id'), 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'brand_id.required'=>'The brand name field is required.',
            'base_unit_id.required'=>'The unit field is required.'
        ];
    }
}
