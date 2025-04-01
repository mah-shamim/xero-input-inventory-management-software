<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/18/2019
 * Time: 11:21 AM
 */

namespace App\Services\Income;


use App\Models\Income\Income;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class IncomeListService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $income = Income::with('warehouse:id,name', 'category:id,name')->whereCompanyId($companyId);
        $query = request()->query();
        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'ref' || $key == 'amount') {
                    $income->where($key, 'like', '%' . $query[$key] . '%');
                }

                if ($key == 'warehouse') {
                    $income->whereHas('warehouse', function ($w) use ($key, $query) {
                        $w->where('name', 'like', '%' . $query[$key] . '%');

                    });
                }
                if ($key == 'category') {
                    $income->where('category_id', 'like', '%' . $query[$key] . '%');
                }


            }
        }
        $array['income'] = $income->paginate($request->get('itemsPerPage') ? $request->get('itemsPerPage') : getResultPerPage());
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