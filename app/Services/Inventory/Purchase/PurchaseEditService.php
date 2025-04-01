<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/7/2018
 * Time: 3:15 PM
 */

namespace App\Services\Inventory\Purchase;


use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class PurchaseEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        $id = $params['id'];
        $purchase = Purchase::with('products.warehouses', 'products.units', 'products.purchases', 'payments')->whereId($id)->first();
        if (!$purchase) {
            $result['message'] = "Selected Purchase does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['purchases'] = $purchase;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['warehouses'] = Warehouse::warehouseList()->get();
        $array['products'] = Product::with('warehouses', 'units')->has('units')->productList()->get();
        $array['suppliers'] = Supplier::supplierList()->get();
        $array['units'] = Unit::unitList()->get();
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