<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/9/2018
 * Time: 11:25 AM
 */

namespace App\Services\Inventory\ProductDamage;


use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ShowProductDamageService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = $request->input('company_id');
        $array['warehouses'] = Warehouse::whereCompanyId($companyId)->get();
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