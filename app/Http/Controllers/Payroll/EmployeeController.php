<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\EmployeeRequest;
use App\Models\Payroll\Department;
use App\Models\Payroll\Employee;
use App\Models\Payroll\Salary;
use App\Services\Payroll\Employee\EmployeeCreateService;
use App\Services\Payroll\Employee\EmployeeEditService;
use App\Services\Payroll\Employee\EmployeeListService;
use App\Services\Payroll\Employee\EmployeeUpdateService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

    public function index(EmployeeListService $employeeListService, Request $request)
    {
        $result = $this->renderArrayOutput($employeeListService, $request, null);
        return $result['employee'];
    }


    public function create(): \Illuminate\Http\JsonResponse
    {

        return response()->json(
            ['departments'=>Department::where('company_id', compid())->get()]
        );
    }


    public function store(EmployeeCreateService $employeeCreateService, EmployeeRequest $request)
    {
        return $this->renderJsonOutput($employeeCreateService, $request, null);
    }


    public function show($id)
    {

    }

    public function edit($id, EmployeeEditService $employeeEditService, Request $request)
    {
        return $this->renderJsonOutput($employeeEditService, $request, ['id' => $id]);
    }


    public function update(EmployeeUpdateService $employeeUpdateService, EmployeeRequest $request, $id)
    {
        return $this->renderJsonOutput($employeeUpdateService, $request, ['id'=>$id]);
    }


    public function destroy($id)
    {
        $salaryCount=Salary::whereEmployeeId($id)->count();

        if($salaryCount>0){
            return response()->json([
                'type' => 'error', 'message' => 'Salary Exist within this employee.'
            ]);
        }else{
            $employee=Employee::find($id);
            $employee->delete();
            return response()->json([
                'type' => 'success', 'message' => 'Employee has been deleted successfully'
            ]);
        }

    }
}
