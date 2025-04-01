<?php

namespace App\Http\Requests\Inventory;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email'=>'email',
            'phone' => ['required', new PhoneNumber()]
        ];

    }
}
