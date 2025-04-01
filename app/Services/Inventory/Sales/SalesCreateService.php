<?php

/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/2/2018
 * Time: 5:59 PM
 */

namespace App\Services\Inventory\Sales;

use App\Http\Controllers\Bank\TransactionController;
use App\Http\Controllers\Inventory\SalesController;
use App\Models\Inventory\Customer;
use App\Models\Inventory\Order;
use App\Models\Inventory\Partnumber;
use App\Models\Inventory\Product;
use App\Models\Inventory\Salequotation;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SalesCreateService extends SaleCrudMethods implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        //        dd($request->all());
        $array['type'] = 'error';
        $request->merge(['total_weight' => $request->input('total_weight') + $request->input('extra_weight')]);
        $sales = $this->createSales($request);
        $salesData = [];
        foreach ($request->input('items') as $item) {
            $product = Product::with('warehouses')->find($item['product_id']);
            $location_detail = (new Product())->getGlobalLocationStringToEloquent($item['location']);
            $salesData[$product->id] = $this->getArrForSale($item, $location_detail);
            $array = $this->convertQuantity($product, $item, $array);
            if ($array['message'] != '') {
                return $array;
            }
            $product->updateWeight('subtract', $product, $item['warehouse'], $item['weight_total']);
            $product->updateQuantity($product, $array['quantity'], $item['warehouse']);
//            $product->updateLocation($product, $item, 'sale');
            $product->update();
            $sales->products()->attach($salesData);

            if ($product->manufacture_part_number) {
                foreach ($item['selected_part_number'] as $id) {
                    $part_number = Partnumber::where('id', $id)
                        ->whereNull('sale_id')
                        ->first();
                    $part_number->update(['sale_id' => $sales->id]);
                }
            }

            $salesData = [];
        }

        $request->merge(['date' => $request->input('sales_date')]);
        (new TransactionController())->create_bank_transaction($request, 'debit');

        $sales->payments()->create($request->all());
        if ($request->get('quotation_id')) {
            $this->updateQuotation($sales->id);
        }

        if ($request->get('order_id')) {
            $this->updateOrder($sales->id);
        }

        $array['type'] = 'success';
        if (request()->input('selectedButton') == 'print' || request()->input('selectedButton') == 'pos-print') {
            $array['sale'] = (new SalesController())->show($sales->id);
        }
        $array['sale'] = $sales;

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Sale has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function createSales(Request $request)
    {
        $sales = Customer::fromCompany()->whereId($request->input('customer_id'))
            ->first()
            ->sales()->create(array_merge($request->except('items', 'sales_date'), [
                'sales_date' => $request->input('sales_date'),
                'ref' => getPurchaseRef($request->input('status'), auth()->id()),
            ]));

        return $sales;
    }

    public function convertQuantity(Product $product, $item, $array)
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

    private function updateQuotation($sale)
    {
        $saleQuotation = Salequotation::where('id', request()->get('quotation_id'))
            ->first();
        $saleQuotation->update([
            'sale_id' => $sale,
        ]);
    }

    private function updateOrder($sale)
    {
        $saleOrder = Order::where('id', request()->get('order_id'))
            ->first();
        $saleOrder->update([
            'sale_id' => $sale,
        ]);
    }
}
