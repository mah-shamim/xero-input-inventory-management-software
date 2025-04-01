<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/21/2019
 * Time: 12:11 PM
 */

namespace App\Services\Payroll\Salary;

use App\Models\Payroll\Salary;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryUpdateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $request->input('id');
        $salary = Salary::find($id);
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
        $salary = $array['salary'];
        $request->merge([
            'salary_date' => Carbon::parse($request->input('salary_date'))->format('Y-m-d H:m:s'),
            'salary_month' => Carbon::parse($request->input('salary_month'))->format('Y-m-d'),
            'total' => ($request->input('amount') + $request->input('festival_bonus') +
                    $request->input('other_bonus')) - $request->input('deduction'),
        ]);
        $request->merge(['paid' => $request->input('total')]);
        $salary->transaction()->update([
            'amount' => $request->input('total'),
        ]);
        $salary->update($request->all());

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Salary has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
