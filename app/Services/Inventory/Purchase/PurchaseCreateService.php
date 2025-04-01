<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/14/2017
 * Time: 11:48 AM
 */

namespace App\Services\Inventory\Purchase;

use App\Http\Controllers\Bank\TransactionController;
use App\Models\Inventory\Orderpurchase;
use App\Models\Inventory\Partnumber;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchasequotation;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class PurchaseCreateService implements ActionIntf
{
    use PurchaseServicesCommonMethods;

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['type'] = 'error';
        $request->merge(['total_weight' => $request->input('total_weight') + $request->input('extra_weight')]);
        $purchase = $this->createPurchase($request);
        $purchaseData = [];
        foreach ($request->input('items') as $item) {
            list($product, $purchaseQuantity, $subtotal, $location_detail)=$this->processProductData($item, $array);
            $purchaseData[$product->id] = $this->getArr($item, $purchaseQuantity, $subtotal, $location_detail);

            $product->updateWeight('sum', $product, $item['warehouse'], $item['weight_total']);
            $product->updateQuantity($product, $item['quantity'], $item['warehouse']);
//            $product->updateLocation($product, $item); //dont delete this
            $product->update();
            $purchase->products()->attach($purchaseData);
            $this->createPartNumber($product, $item, $purchase);

            $purchaseData = [];
        }
        if ($request->input('paid')) {
            (new TransactionController())->create_bank_transaction($request, 'credit');
            $request->merge(['date', $request->input('purchase_date_formatted')]);
            $purchase->payments()->create($request->all());
        }

        if ($request->get('quotation_id')) {
            $this->updateQuotation($purchase->id);
        }

        if ($request->input('order')) {
            $this->updateOrder($purchase->id);
        }

        $array['type'] = 'success';
        $array['purchase'] = $purchase;

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Purchase has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    /**
     * @return mixed
     */
    private function createPurchase(Request $request)
    {
        //        dd(Carbon::createFromFormat('Y-m-d H:i:s', $request->input('purchase_date'))->format('Y-m-d H:i:s'));
        $purchase = Supplier::find($request->input('supplier_id'))
            ->purchases()->create(array_merge($request->except('items', 'purchase_date_formatted'), [
                'purchase_date' => $request->input('purchase_date_formatted'),
                'ref' => getPurchaseRef($request->input('status'), auth()->id()),
            ]));

        return $purchase;
    }

    private function updateQuotation($purchase)
    {
        $purchaseQuotation = Purchasequotation::where('id', request()->get('quotation_id'))
            ->first();
        $purchaseQuotation->update([
            'purchase_id' => $purchase,
        ]);
    }

    private function updateOrder($id)
    {
        $purchaseOrder = Orderpurchase::where('id', request()->input('order'))
            ->where('company_id', compid())
            ->first();

        $purchaseOrder->update([
            'purchase_id' => $id,
        ]);
    }

    private function createPartNumber($product, $item, $purchase): void
    {
        if ($product->manufacture_part_number) {
            foreach ($item['part_number'] as $part_number) {
                if ($part_number) {
                    Partnumber::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $item['warehouse'],
                        'purchase_id' => $purchase->id,
                        'part_number' => $part_number,
                    ]);
                }
            }
        }
    }

}