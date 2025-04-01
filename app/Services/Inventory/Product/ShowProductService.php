<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/13/2017
 * Time: 12:16 PM
 */

namespace App\Services\Inventory\Product;


use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ShowProductService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = $request->input('company_id');
        $categories = Category::categoryWithLabelAndValue(Category::getTypeProduct());
        $array['categories'] = $categories;
        $array['units'] = Unit::whereIsPrimary(true)->whereCompanyId($companyId)->get();
        $array['brands'] = Brand::select('id', 'name')->whereCompanyId($companyId)->get();
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