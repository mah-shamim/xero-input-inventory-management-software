<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/10/2018
 * Time: 4:19 PM
 */

namespace App\Services\Inventory\Sales;

use App\Models\Inventory\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\Sale;
use App\Models\Inventory\Salestable;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SalesEditService implements ActionIntf
{
    public function executePreCondition(Request $request, $params): array
    {
        $result = ['type' => 'success', 'message' => ''];
        $id = $params['id'];
        $sales = Sale::with(
            'products.warehouses',
            'partnumbers',
            'products.units',
            'products.partnumbers',
            'payments')
            ->whereId($id)->first();
        if (!$sales) {
            $result['message'] = 'Selected Sale does not exists anymore. Please refresh and try again';

            return $result;
        }
        $result['sales'] = $sales;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['warehouses'] = Warehouse::warehouseList()->get();
        $array['products'] = Product::with('warehouses', 'units')->has('warehouses')->has('units')->productList()->get();
        $array['customers'] = Customer::customerList()->get();
        $array['units'] = Unit::unitList()->get();
        $array['tables'] = Salestable::get();

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
