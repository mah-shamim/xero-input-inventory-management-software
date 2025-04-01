<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/30/2018
 * Time: 5:22 PM
 */

namespace App\Services\Inventory\Returns\Sale;


use App\Models\Inventory\Sale;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ShowSaleReturnService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $companyId = $request->input('company_id');
        $sale = Sale::with(['products.units', 'payments'])->whereId($id)->whereCompanyId($companyId)->first();
        if ($sale == null) {
            $result['message'] = "This sales information does not exist anymore. Please return back and try again";
            return $result;
        }
        $result['sale'] = $sale;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $sale = $array['sale'];
        quantityStrConversion($sale);
        $array['sale'] = $sale;
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