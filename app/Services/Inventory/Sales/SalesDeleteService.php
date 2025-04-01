<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/3/2018
 * Time: 11:40 AM
 */

namespace App\Services\Inventory\Sales;

use App\Models\Inventory\Product;
use App\Models\Inventory\Sale;
use App\Models\Inventory\Salequotation;
use App\Models\Inventory\Traits\PaymentTraits;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesDeleteService implements ActionIntf
{
    use PaymentTraits;

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $sales = Sale::whereId($params['id'])->with('products');
        if ($sales == null) {
            $result['message'] = 'Selected Sales does not exists anymore';

            return $result;
        }
        $result['sales'] = $sales;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $sale = $array['sales'];

        foreach ($sale->first()->products as $product) {

            $productWarehouse = DB::table('product_warehouse')
                ->whereProductId($product->pivot->product_id)
                ->whereWarehouseId($product->pivot->warehouse_id)->first();

//            dd($productWarehouse);

            $productData = Product::with('warehouses')
                ->where('id', $product->pivot->product_id)
                ->first();
            $quantity = $product->pivot->quantity;
            $fromUnitId = $product->pivot->unit_id;
            $toUnitId = $productData->base_unit_id;
//            $location_value = $product->pivot['location_value'];
            $previous_warehouse_quantity = $productWarehouse->quantity;
            $previous_warehouse_weight = $productWarehouse->weight;
//            $productWarehouse_location = $productWarehouse->location;
//            $productWarehouse_location_json = json_decode($productWarehouse_location, true);
//            if($productWarehouse_location_json && count($productWarehouse_location_json)){
//                $product_warehouse_location_quantity = $productWarehouse_location_json[$location_value]['quantity'];
//                $product_warehouse_location_weight = $productWarehouse_location_json[$location_value]['weight'];
//            }else{
//                $product_warehouse_location_quantity=0;
//                $product_warehouse_location_weight=0;
//            }
            if ($fromUnitId == $toUnitId) {
                DB::table('product_warehouse')
                    ->whereProductId($product->pivot->product_id)
                    ->whereWarehouseId($product->pivot->warehouse_id)->update([
                        'quantity' => $previous_warehouse_quantity + $product->pivot->quantity,
                        'weight' => $previous_warehouse_weight + $product->pivot->weight,
//                    'location->'.$location_value.'->quantity' =>$product_warehouse_location_quantity + $product->pivot->quantity,
//                    'location->'.$location_value.'->weight' =>$product_warehouse_location_weight + $product->pivot->weight
                    ]);
            } else {
                $conversion = UnitConversion::where('from_unit_id', $fromUnitId)->where('to_unit_id', $toUnitId)->first();
                $quantity = $quantity * $conversion->conversion_factor;
                DB::table('product_warehouse')
                    ->whereProductId($product->pivot->product_id)
                    ->whereWarehouseId($product->pivot->warehouse_id)
                    ->update([
                        'quantity' => $previous_warehouse_quantity + $quantity,
                        'weight' => $previous_warehouse_weight + $product->pivot->weight,
//                    'location->'.$location_value.'->quantity' =>$product_warehouse_location_quantity + $quantity,
//                    'location->'.$location_value.'->weight' =>$product_warehouse_location_weight + $product->pivot->weight
                    ]);
            }
        }

        $this->delete_payments_transactions('App\Models\Inventory\Sale', $sale->first()->id);

        $this->deleteQuotation($sale->first()->id);

        $sale->delete();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Sale has been deleted successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function deleteQuotation($id)
    {
        $saleQuotation = Salequotation::where('sale_id', $id)->first();
        if ($saleQuotation) {
            $saleQuotation->update([
                'sale_id' => null,
            ]);
        }
    }
}
