<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UnitConversionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_unit_id' => 'required|integer',
            'from_unit_val' => 'required',
            'to_unit_id' => 'required|integer',
            'to_unit_val' => 'required',
        ];
    }
}
