<?php
/**
 * This file is a part of MicroDreamIT
 * (c) Shahanur Sharif <shahanur.sharif@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * or visit https://microdreamit.com
 * Created by Shahanur Sharif.
 * User: sharif
 * Date: 4/24/2018
 * Time: 6:03 PM
 */

namespace App\Services\Inventory\Returns\Sale;


use App\Models\Inventory\Product;
use App\Models\Inventory\Returns;
use App\Models\Inventory\Sale;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SaleReturnCreateService implements ActionIntf
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
        $requestArr = $request->input('return');
        for ($i = 0; $i < sizeof($requestArr); $i++) {
            $requestObj = $requestArr[$i];
            $requestObj['company_id'] = $request->input('company_id');
            $return = Returns::create($requestObj);

            $product = Product::findOrFail($return->product_id);
            $result = $this->convertQuantity($return, $product);
            if ($result['message'] != '') {
                return ['type' => 'error', 'message' => $result['message']];
            }
            $this->updateQuantity($product, $result['quantity'], $return->warehouse_id);
        }
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Sale return added successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function convertQuantity(Returns $return, Product $product)
    {
        $fromUnitId = $return->unit_id;
        $toUnitId = $product->base_unit_id;
        $quantity = $return->quantity;
        if ($fromUnitId == $toUnitId) {
            return ['quantity' => $quantity, 'message' => ''];
        }
        $conversion = UnitConversion::where('from_unit_id', $fromUnitId)->where('to_unit_id', $toUnitId)->first();
        if ($conversion == null) {
            return ['message' => "Unit mapping doest exits"];
        }
        $quantity = $quantity * $conversion->conversion_factor;
        return ['quantity' => $quantity, 'message' => ''];
    }

    private function updateQuantity(Product $product, $quantity, $warehouseId)
    {
        $oldQuantity = $product->getQuantity($product, $warehouseId);
        $product->warehouses()->syncWithoutDetaching([$warehouseId => ['quantity' => $oldQuantity + $quantity, 'unit_id' => $product->base_unit_id]]);
    }
}