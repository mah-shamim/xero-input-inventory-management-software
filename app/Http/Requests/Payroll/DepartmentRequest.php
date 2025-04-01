<?php

namespace App\Http\Requests\Payroll;

use App\Models\Payroll\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', Rule::unique('departments')
                    ->where('company_id', compid())
                    ->ignore($this->route('department'), 'id'),
            ],
        ];
    }

    public function save(): \Illuminate\Http\JsonResponse
    {
        Department::create($this->all());

        return response()->json([
            'type' => 'success',
            'message' => 'Department has been created successfully',
        ]);
    }

    public function update()
    {
        $department = Department::where('id', $this->id)->where('company_id', compid())->first();
        $department->update(['name' => $this->name]);

        return response()->json([
            'type' => 'success',
            'message' => 'Department has been updated successfully',
        ]);
    }
}
