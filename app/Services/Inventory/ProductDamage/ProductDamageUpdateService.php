<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/11/2018
 * Time: 11:06 AM
 */

namespace App\Services\Inventory\ProductDamage;


use App\Models\Inventory\Product;
use App\Models\Inventory\ProductDamage;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductDamageUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $productDamage = ProductDamage::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (!$productDamage) {
            $result['message'] = "Selected Damaged Product does not exists anymore.Please refresh and try again";
            return $result;
        }
        if ($productDamage->warehouse_id != $request->input('warehouse_id')) {
            $result['message'] = "Warehouse can't be changed during update. Please delete and recreate to change warehouse";
            return $result;
        }
        $result['productDamage'] = $productDamage;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $productDamage = $array['productDamage'];
        $oldProduct = Product::findOrFail($productDamage->product_id);
        $result = $this->convertQuantity($productDamage, $oldProduct);
        if ($result['message'] != '') {
            return ['type' => 'error', 'message' => $result['message']];
        }
        $oldQuantity = $result['quantity'];

        $productDamage->update($request->all());
        $productDamage->save();

        $newProduct = Product::findOrFail($productDamage->product_id);

        $result = $this->convertQuantity($productDamage, $newProduct);
        if ($result['message'] != '') {
            return ['type' => 'error', 'message' => $result['message']];
        }
        if ($oldProduct->id != $newProduct->id) {
            $this->adjustPreviousQuantity($oldProduct, $oldQuantity, $productDamage->warehouse_id);
            $oldQuantity = 0;
        }
        $this->updateQuantity($newProduct, $oldQuantity, $result['quantity'], $productDamage->warehouse_id);
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Product Damage has been updated successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function convertQuantity(ProductDamage $productDamage, Product $product)
    {
        $fromUnitId = $productDamage->unit_id;
        $toUnitId = $product->base_unit_id;
        $quantity = $productDamage->quantity;
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

    private function updateQuantity(Product $product, $oldQuantity, $quantity, $warehouseId)
    {
        $warehouseQuantity = $product->getQuantity($product, $warehouseId);
        $product->warehouses()->syncWithoutDetaching([$warehouseId => ['quantity' => $warehouseQuantity + $oldQuantity - $quantity, 'unit_id' => $product->base_unit_id]]);
    }

    private function adjustPreviousQuantity(Product $product, $quantity, $warehouseId)
    {
        $warehouseQuantity = $product->getQuantity($product, $warehouseId);
        $product->warehouses()->syncWithoutDetaching([$warehouseId => ['quantity' => $warehouseQuantity + $quantity, 'unit_id' => $product->base_unit_id]]);
    }
}