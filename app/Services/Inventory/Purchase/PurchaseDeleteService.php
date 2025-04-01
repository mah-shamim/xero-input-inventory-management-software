<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/1/2018
 * Time: 1:00 PM
 */

namespace App\Services\Inventory\Purchase;


use App\Models\Inventory\Payment;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $purchase = Purchase::whereId($params['id'])->with('products');
        if ($purchase == null) {
            $result['message'] = "Selected Purchase does not exists anymore";
            return $result;
        }
        $result['Purchase'] = $purchase;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $purchase = $array['Purchase'];

        foreach ($purchase->first()->products as $product) {

            $productWarehouse = DB::table('product_warehouse')
                ->whereProductId($product->pivot->product_id)
                ->whereWarehouseId($product->pivot->warehouse_id);

            $productData=Product::with('warehouses')->find($product->pivot->product_id);
            $quantity = $product->pivot->quantity;
            $fromUnitId = $product->pivot->unit_id;
            $toUnitId = $productData->base_unit_id;
            if ($fromUnitId == $toUnitId) {
                $productWarehouse->update(['quantity'=>$productWarehouse->first()->quantity - $product->pivot->quantity]);
            }else{
                $conversion = UnitConversion::where('from_unit_id', $fromUnitId)->where('to_unit_id', $toUnitId)->first();
                $quantity = $quantity * $conversion->conversion_factor;
                $productWarehouse->update(['quantity'=>$productWarehouse->first()->quantity - $quantity]);
            }
        }
        $payment=Payment::wherePaymentableType(Purchase::classPath())->wherePaymentableId($purchase->first()->id);
        $payment->delete();
        $purchase->delete();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Purchase has been deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}