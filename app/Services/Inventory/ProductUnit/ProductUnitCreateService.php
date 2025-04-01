<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/27/2018
 * Time: 4:54 PM
 */

namespace App\Services\Inventory\ProductUnit;
use App\Models\Inventory\Product;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductUnitCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $product = Product::whereId($request->input('product_id'))->whereCompanyId($request->input('company_id'))->first();
        $unitIdList = explode(",", $request->input('unitidjoin'));
        $unitList = [];
        $parentId = 0;
        for ($i = 0; $i < sizeof($unitIdList); $i++) {
            if ($i == 0) {
                $unitList[$unitIdList[$i]] = ['parent_id' => $i];
            } else {
                $unitList[$unitIdList[$i]] = ['parent_id' => $parentId];
            }
            $parentId = $unitIdList[$i];
        }

        $product->units()->sync($unitList);
        $array['products'] = Product::productList()->get();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Product Unit Mapping has been created successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}