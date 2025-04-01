<?php

namespace App\Http\Requests\Bank;

use App\Bank\Bank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankFormRequest extends FormRequest
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
            'branch' => 'required',
            'address' => 'required',
            'type' => Bank::where('company_id', $this->input('company_id'))->where('type', 'cr')->first() ? 'in:,cr' : 'in:cr',
            'account_no' => [
                'required',
                Rule::unique('banks')
                    ->where('company_id', $this->input('company_id'))
                    ->ignore($this->input('id'), 'id'),
            ],
        ];
    }

    public function save()
    {
        try {
            $this->toggleNullify_dbcl_type();
            Bank::create($this->all());

            return response()->json([
                'type' => 'success',
                'message' => 'Bank has been created successfully',
            ]);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function update(Bank $bank)
    {
        try {
            $this->toggleNullify_dbcl_type();
            $bank->update($this->all());

            return response()->json([
                'type' => 'success',
                'message' => 'Bank has been updated successfully',
            ]);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function toggleNullify_dbcl_type(): void
    {
        if ($this->input('type') === 'cr') {
            $banks = Bank::where('company_id', $this->input('company_id'))
                ->where('type', 'cr')
                ->first();
            if ($banks) {
                foreach ($banks as $bank) {
                    $bank->update(['type' => null]);
                }
            }
        }
    }
}
