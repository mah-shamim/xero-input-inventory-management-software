<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/12/2017
 * Time: 1:20 PM
 */

namespace App\Services\Inventory\Product;


use App\Models\Inventory\Brand;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $brand = Brand::whereId($request->input('brand_id'))
            ->whereCompanyId($request->input('company_id'))
            ->first();

        $product = $brand->products()->create($request->except(['categories', 'brand_id', 'weight']));

        //categories are array of ids
        $product->categories()->attach($request->input('categories'));

        $product->units()->sync([
            $request->input('base_unit_id') => [
                'weight' => $request->input('weight'),
            ],
        ]);

        $array['product'] = $product;

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Product has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
