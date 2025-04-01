<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/24/2017
 * Time: 11:21 AM
 */

namespace App\Services\Inventory\Supplier;


use App\Models\Inventory\Supplier;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SupplierEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $supplier = Supplier::whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$supplier) {
            $result['message'] = "Selected Supplier does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['suppliers'] = $supplier;
        $result['type'] = $supplier;
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