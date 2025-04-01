<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/1/2018
 * Time: 10:57 AM
 */

namespace App\Services\Inventory\Supplier;


use App\Models\Inventory\Supplier;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SupplierDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $supplier = Supplier::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        $count = $supplier->purchases()->count();
        if ($count > 0) {
            $result['message'] = $count . " Purchases exist with this supplier. Please delete corresponding Purchase first";
            return $result;
        }
        $result['supplier'] = $supplier;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $supplier = $array['supplier'];
        $supplier->delete();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Supplier has been deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}