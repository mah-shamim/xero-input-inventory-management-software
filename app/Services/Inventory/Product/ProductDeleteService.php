<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/1/2018
 * Time: 12:33 PM
 */

namespace App\Services\Inventory\Product;


use App\Models\Inventory\Product;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $product = Product::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        $count = $product->purchases()->count();
        if ($count > 0) {
            $result['message'] = $count . " Purchase(s) exist with this Product. Please delete corresponding Purchase first";
            return $result;
        }
        $result['product'] = $product;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $product = $array['product'];
        $product->delete();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Product deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}