<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/18/2018
 * Time: 12:41 PM
 */

namespace App\Services\Inventory\ProductDamage;


use App\Models\Inventory\ProductDamage;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductDamageDeleteService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $productDamage = ProductDamage::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (!$productDamage) {
            $result['message'] = "Selected Damaged Product does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['productDamage'] = $productDamage;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $productDamage = $array['productDamage'];
        $productDamage->delete();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Product Damage has been deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}