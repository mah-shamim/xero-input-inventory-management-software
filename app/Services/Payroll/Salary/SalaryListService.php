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

class SalaryListService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $salaries = Salary::select('salaries.*', 'employees.name as employee_name')
            ->leftJoin('employees', 'employees.id', '=', 'salaries.employee_id')
            ->selectRaw('coalesce(salaries.current_salary - salaries.amount, 0) as due')
            ->selectRaw('coalesce(salaries.current_salary - salaries.current_salary, 0) as negative_due')
            ->selectRaw('salaries.amount as salary_paid')
            ->where('salaries.company_id', compid());
        $query = request()->query();

        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'salary_date' && $query[$key]) {
                    $salaries->whereDate('salaries.salary_date', '=', Carbon::parse($query['salary_date']));
                }
                if ($key == 'salary_month' && $query[$key]) {
                    $salaries->whereMonth('salaries.salary_month', '=', Carbon::parse($query['salary_month'])->format('m'));
                    $salaries->whereYear('salaries.salary_month', '=', Carbon::parse($query['salary_month'])->format('Y'));
                }

                if ($key == 'employee') {
                    $salaries->where('employees.name', 'like', '%'.$query[$key].'%');
                }
                if ($key == 'id') {
                    $salaries->where('salaries.id', $query[$key]);
                }
                if ($key == 'department') {
                    $salaries->whereHas('employee', function ($employee) use ($query, $key) {
                        //                        dd($employee);
                        return $employee->whereHas('department', function ($department) use ($query, $key) {
                            return $department->where('id', $query[$key]);
                        });
                    });
                }
            }
        }
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $salaries->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }
        $array['salaries'] = $salaries->paginate(itemPerPage());

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
