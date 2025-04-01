<?php

namespace App\Http\Requests\Inventory;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
            'name' => 'required',
            'code' => 'required',
            'phone' => ['required', new PhoneNumber()],
            'email' => 'required|email',
            'address' => 'max:255'
        ];
    }
}
