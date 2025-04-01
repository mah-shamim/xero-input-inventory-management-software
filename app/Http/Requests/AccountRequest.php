<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'parent_id' => 'required',
            'name' => 'required',
        ];
    }

    public function save()
    {
        $this->merge(['type' => 'ledger']);
        Account::create($this->all());

        return response()->json([
            'type' => 'success',
            'message' => 'Account has been created successfully',
        ]);
    }

    public function update($data)
    {
        $this->merge(['type' => 'ledger']);
        $data->update($this->all());

        return response()->json([
            'type' => 'success',
            'message' => 'account has been updated successfully',
        ]);
    }
}
