<?php

namespace App\Http\Requests\Inventory;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'name' => 'required|max:50',
            'email' => 'required|email',
            'phone' => ['required', new PhoneNumber],
            'company' => 'required|max:100',
            'code' => [
                'required',
                Rule::unique('suppliers')
                    ->where('company_id', $this->input('company_id'))
                    ->ignore($this->input('id'), 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The id field is required.',
        ];
    }
}
