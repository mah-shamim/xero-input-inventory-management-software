<?php

namespace App\Services\Inventory\Returns\Purchase;

use App\Http\Controllers\Bank\TransactionController;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Returns;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class PurchaseReturnUpdateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $companyId = $request->input('company_id');
        $purchase = Purchase::with(['products.units', 'payments'])->whereId($id)->whereCompanyId($companyId)->first();
        if ($purchase == null) {
            $result['message'] = 'This purchases information does not exist anymore. Please return back and try again';

            return $result;
        }
        $result['purchase'] = $purchase;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $requestArr = $request->input('return');

        $totalAmount = 0;
        $returnArr = [];
        for ($i = 0; $i < count($requestArr); $i++) {
            $requestObj = $requestArr[$i];
            $requestObj['company_id'] = $request->input('company_id');
            $totalAmount += $requestObj['amount'];
            $return = Returns::create($requestObj);
            $returnArr[] = $return;
            $product = Product::with('units')->findOrFail($return->product_id);
            $result = $this->convertQuantity($return, $product);
            if ($result['message'] != '') {
                return ['type' => 'error', 'message' => $result['message']];
            }
            $weight = $product->units->first(function ($u) use ($requestObj) {
                return $u->id == $requestObj['unit_id'];
            })->pivot->weight * $requestObj['quantity'];

            $product->updateWeight('subtract', $product, $return->warehouse_id, $weight);
            $this->updateQuantity($product, $result['quantity'], $return->warehouse_id);
        }

        $request->merge(['paid' => $totalAmount]);
        $request->merge(['payment_type' => 1]);
        (new TransactionController())->create_bank_transaction($request, 'debit');
        foreach ($returnArr as $item) {
            $item->update(['transaction_id' => $request->input('transaction_id')]);
        }

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Purchase return has been added successfully';

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
            return ['message' => 'Unit mapping doest exits'];
        }
        $quantity = $quantity * $conversion->conversion_factor;

        return ['quantity' => $quantity, 'message' => ''];
    }

    private function updateQuantity(Product $product, $quantity, $warehouseId)
    {
        $oldQuantity = $product->getQuantity($product, $warehouseId);
        if ($oldQuantity) {
            $product->warehouses()->syncWithoutDetaching([$warehouseId => ['quantity' => $oldQuantity - $quantity, 'unit_id' => $product->base_unit_id]]);
        }
    }
}
