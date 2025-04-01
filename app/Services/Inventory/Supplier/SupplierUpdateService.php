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

class SupplierUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $supplier = Supplier::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (!$supplier) {
            $result['message'] = "Selected Supplier does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['supplier'] = $supplier;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {

        $supplier = $array['supplier'];
        $supplier->update($request->all());
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Supplier updated successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}