<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/19/2019
 * Time: 4:04 PM
 */

namespace App\Services\Payroll\Employee;


use App\Models\Payroll\Department;
use App\Models\Payroll\Employee;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class EmployeeEditService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];

        $employee = Employee::find($id);
        if (!$employee) {
            $result['message'] = "Selected Employee does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['departments'] = Department::where('company_id', compid())->get();
        $result['employee'] = $employee;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

}