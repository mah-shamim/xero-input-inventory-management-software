<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/20/2017
 * Time: 12:37 PM
 */

namespace App\Services\Inventory\Warehouse;


use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarehouseEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $warehouse = Warehouse::whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$warehouse) {
            $result['message'] = "Selected Warehouse does not exists anymore.Please refres and try again";
            return $result;
        }
        $result['warehouses'] = $warehouse;
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