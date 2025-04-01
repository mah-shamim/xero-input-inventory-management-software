<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/21/2019
 * Time: 12:11 PM
 */

namespace App\Services\Payroll\Salary;

use App\Models\Payroll\Employee;
use App\Models\Payroll\Salary;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SalaryEditService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];

        $salary = Salary::with('employee')->whereId($id)->first();
        if (! $salary) {
            $result['message'] = 'Selected Salary does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['salary'] = $salary;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['employees'] = Employee::employeeList()->get();

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
