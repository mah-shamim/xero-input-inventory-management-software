<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/19/2019
 * Time: 3:29 PM
 */

namespace App\Services\Payroll\Employee;


use App\Models\Payroll\Employee;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeCreateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {

        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }


    public function execute($array, Request $request)
    {
        $request->merge([
            'birth' => $request->input('birth')?
                Carbon::parse($request->input('birth'))->format('Y-m-d H:i:s'):null,
            'join_date' => $request->input('join_date')?
                Carbon::parse($request->input('join_date'))->format('Y-m-d H:i:s'):null,
            ]
        );
        Employee::create($request->all());
        return $array;

    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Employee has been created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}