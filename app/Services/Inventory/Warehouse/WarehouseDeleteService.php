<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/3/2018
 * Time: 10:58 AM
 */

namespace App\Services\Inventory\Warehouse;


use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarehouseDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $warehouse = Warehouse::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        $count = $warehouse->products()->count();
        if ($count > 0) {
            $result['message'] = $count . "Product exist with this Warehouse. Please delete corresponding product first";
            return $result;
        }
        $countExpense = $warehouse->expense()->count();
        if ($countExpense > 0) {
            $result['message'] = $countExpense . "Expense exist with the Warehouse. Please delete corresponding payroll first";
            return $result;
        }
        $result['warehouse'] = $warehouse;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $warehouse = $array['warehouse'];
        $warehouse->delete();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Warehouse deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
