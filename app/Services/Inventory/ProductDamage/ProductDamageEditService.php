<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/11/2018
 * Time: 11:06 AM
 */

namespace App\Services\Inventory\ProductDamage;


use App\Models\Inventory\Product;
use App\Models\Inventory\ProductDamage;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductDamageEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $productDamage = ProductDamage::whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$productDamage) {
            $result['message'] = "Selected product damage record does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['productdamages'] = $productDamage;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = $request->input('company_id');
        $productDamages = $array['productdamages'];
        $array['warehouses'] = Warehouse::whereCompanyId($companyId)->get();
        $array['products'] = Product::with('warehouses', 'units')->has('warehouses')->has('units')->select('id', 'name', 'code', 'price', 'base_unit_id', 'buying_price')->leftJoin('product_warehouse AS pw', 'products.id', '=', 'pw.product_id')->where('pw.warehouse_id', $productDamages->warehouse_id)->whereCompanyId($companyId)->orderBy('name', 'ASC')->get();
        $array['units'] = Unit::leftJoin('product_unit AS pu', 'units.id', '=', 'pu.unit_id')->whereCompanyId($companyId)->where('pu.product_id', $productDamages->product_id)->orderBy('key', 'ASC')->get();
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
