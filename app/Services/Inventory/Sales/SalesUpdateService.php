<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/10/2018
 * Time: 1:58 PM
 */

namespace App\Services\Inventory\Sales;

use App\Http\Controllers\Bank\TransactionController;
use App\Http\Controllers\Inventory\SalesController;
use App\Models\Inventory\Partnumber;
use App\Models\Inventory\Product;
use App\Models\Inventory\Sale;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesUpdateService extends SaleCrudMethods implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];

        $sales = Sale::with([
            'products', 'payments' => function ($p) {
                $p->first();
            },
        ])->find($id);
        if (! $sales) {
            $result['message'] = 'Selected Sale does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['sales'] = $sales;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['type'] = 'error';
        $sales = $array['sales'];
        $request->merge(['total_weight' => $request->input('total_weight') + $request->input('extra_weight')]);
        $array = $this->getSalesData($request, $sales, $array);
        if ($array['message'] != '') {
            return $array;
        }
        //        $salesData = $array['salesData'];
        $sales = $sales->fill(
            array_merge($request->except('sales_date'), [
                'sales_date' => Carbon::parse($request->input('sales_date'))->format('Y-m-d H:m:s'),
            ])
        );
        $sales->customer()->associate($request->input('customer_id'));
        $first_payment = $sales->payments->first();
        if ($first_payment) {
            $first_payment->update($request->all());
            (new TransactionController())->update_bank_transaction($request, $sales, 'debit');
        }

        $sales->save();

        if (request()->input('selectedButton') == 'print') {
            $array['sale'] = (new SalesController())->show($sales->id);
        }

        $array['type'] = 'success';

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Sale has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function getSalesData(Request $request, $sales, $array)
    {
        $salesData = [];
        $sales->products()->detach();
        $old_products = $sales->products;
        foreach ($request->input('items') as $item) {
            $product = Product::with('warehouses')->find($item['product_id']);
            $location_detail = (new Product())->getGlobalLocationStringToEloquent($item['location']);
            $item['class_location'] = $location_detail ? $location_detail['class_location'] : 'fixed';
            $item['locatable'] = $location_detail ? $location_detail['locatable']['id'] : null;
            $salesData[$product->id] = $this->getArrForSale($item, $location_detail);
            $array = $this->convertQuantity($product, $item, $array);
            if ($array['message'] != '') {
                return $array;
            }
            $old_product_for_warehouse = $old_products->first(function ($p) use ($item) {
                return $p->pivot->warehouse_id === $item['warehouse'];
            });
            $product_exist = $this->checkProductExist($item['product_id'], $old_products);
            $item['ps_id'] = array_key_exists('ps_id', $item) ? (int) $item['ps_id'] : null;
            $warehouse_id = $old_product_for_warehouse ? $old_product_for_warehouse->pivot->warehouse_id : null;
            //            dd($product_exist, $warehouse_id, $item['warehouse']);
            if ($product_exist && ($warehouse_id === (int) $item['warehouse'])) {
                $product->editUpdateWeight($product, $sales, $item['warehouse'], $item['weight_total'], 'subtract', $item['ps_id']);
            } else {
                $product->updateWeight('subtract', $product, $item['warehouse'], $item['weight_total']);
            }

            $product->updateQuantity($product, $array['quantity'], $item['warehouse'], $sales, null, $item['ps_id']);
            $product->update();
            $sales->products()->attach($salesData);
//            $product->updateLocationEdit($product, $item, $sales);

            if ($product->manufacture_part_number) {
                Partnumber::where('sale_id', $sales->id)->update(['sale_id' => null]);

                foreach ($item['selected_part_number'] as $id) {
                    $part_number = Partnumber::where('id', $id)
                        ->whereNull('sale_id')
                        ->first();
                    $part_number->update(['sale_id' => $sales->id]);
                }
            }

            $salesData = [];
        }
        $array['salesData'] = $salesData;

        return $array;
    }

    public function checkProductExist($product_id, $old_products, $unit_id = null)
    {
        //        dd($product_id, $old_products->unit->id, $unit_id);
        foreach ($old_products as $old_product) {
            if (($old_product->id === $product_id)) {
                return true;
            }
        }

        return false;
    }

    private function convertQuantity(Product $product, $item, $array)
    {
        $quantity = $item['quantity'];
        $fromUnitId = $item['unit'];
        $toUnitId = $product->base_unit_id;
        if ($fromUnitId == $toUnitId) {
            $array['quantity'] = $quantity;

            return $array;
        }
        $conversion = UnitConversion::where('from_unit_id', $fromUnitId)->where('to_unit_id', $toUnitId)->first();
        if ($conversion == null) {
            $array['message'] = 'Unit mapping doest exits';

            return $array;
        }
        $quantity = $quantity * $conversion->conversion_factor;
        $array['quantity'] = $quantity;

        return $array;
    }
}
