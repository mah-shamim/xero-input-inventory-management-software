<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/18/2019
 * Time: 11:27 AM
 */

namespace App\Services\Income;


use App\Models\Income\Income;
use App\Models\Inventory\Category;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class IncomeEditService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];

        $expense = Income::with('warehouse')->whereId($id)->first();
        if (!$expense) {
            $result['message'] = "Selected Income does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['incomes'] = $expense;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['warehouses'] = Warehouse::warehouseList()->get();
        $array['categories'] = Category::categoryList()->whereType("INCOME")->get();
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