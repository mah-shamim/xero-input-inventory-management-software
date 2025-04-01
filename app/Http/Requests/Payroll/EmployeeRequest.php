<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'nid' => [
                'required',
                Rule::unique('employees')
                    ->where('company_id', compid())
                    ->ignore($this->route('employee'), 'id'),
            ],
            'department_id' => [
                'required', Rule::exists('departments', 'id')
                    ->where('company_id', compid())
                    ->where('id', $this->department_id),
            ],
            'employee_id' => [
                'required', Rule::unique('employees')
                    ->where('company_id', compid())
                    ->ignore($this->route('employee'), 'id'),
            ],
            'name' => 'required',
            'mobile'=>'required',
            'emergency'=>'required',
            'designation'=>'required',
            'contract_type'=>'required',
            'salary'=>'required',
            'address'=>'required',
            'birth'=>'required',
            'join_date'=>'required',
            'avatar' => 'dimensions:max_width=10000,max_height=10000',
        ];
    }
}
