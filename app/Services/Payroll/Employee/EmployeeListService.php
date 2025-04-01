<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/19/2019
 * Time: 3:54 PM
 */

namespace App\Services\Payroll\Employee;


use App\Models\Payroll\Employee;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class EmployeeListService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $expense = Employee::whereCompanyId($companyId);
        $query = request()->query();
        $pages = array_key_exists('itemsPerPage', $query) && $query['itemsPerPage'] ? $query['itemsPerPage'] : getResultPerPage();

        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'employee_id' || $key == 'name' || $key == 'nid' || $key == 'mobile') {
                    if($query[$key]){
                        $expense->where($key, 'like', '%' . $query[$key] . '%');
                    }
                }
            }
        }
        $array['employee'] = $expense->paginate($pages);
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