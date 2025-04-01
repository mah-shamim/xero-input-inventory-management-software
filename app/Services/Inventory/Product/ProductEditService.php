<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/18/2017
 * Time: 4:37 PM
 */

namespace App\Services\Inventory\Product;


use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $product = Product::with('brands:id,name')
            ->with('categories')->whereId($id)
            ->whereCompanyId($request->input('company_id'))
            ->first();
        if (!$product) {
            $result['message'] = "Selected Product does not exists anymore. Please refresh and try again";
            return $result;
        }
        $result['products'] = $product;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $categories = Category::categoryWithLabelAndValue(Category::getTypeProduct());
        $array['categories'] = $categories;
        $array['units'] = Unit::whereIsPrimary(true)
            ->whereCompanyId($request->input('company_id'))
            ->get();
        $array['brands'] = Brand::select('id', 'name')
            ->whereCompanyId($request->input('company_id'))
            ->get();
        $array['warehouses'] = Warehouse::warehouseList()->get();
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