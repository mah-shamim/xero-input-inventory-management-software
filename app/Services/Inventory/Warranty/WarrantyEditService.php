<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/11/2019
 * Time: 5:40 PM
 */

namespace App\Services\Inventory\Warranty;


use App\Models\Inventory\Warranty;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarrantyEditService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $warranty = Warranty::with('product','customer')->whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$warranty) {
            $result['message'] = "Selected warranty does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['warranty'] = $warranty;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
//        $array['products'] = Product::productList()->get();
//        $array['customers'] = Customer::customerList()->get();
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
