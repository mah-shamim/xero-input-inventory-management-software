<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/11/2018
 * Time: 11:02 AM
 */

namespace App\Services\Inventory\ProductDamage;


use App\Models\Inventory\Product;
use App\Models\Inventory\ProductDamage;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductDamageCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params): array
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $productDamage = ProductDamage::create($request->all());
        $product = Product::findOrFail($productDamage->product_id);
        $result = $this->convertQuantity($productDamage, $product);
        if ($result['message'] != '') {
            return ['type' => 'error', 'message' => $result['message']];
        }
        $this->updateQuantity($product, $result['quantity'], $productDamage->warehouse_id);
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Product Damage has been created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function convertQuantity(ProductDamage $productDamage, Product $product): array
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

    private function updateQuantity(Product $product, $quantity, $warehouseId): void
    {
        $oldQuantity = $product->getQuantity($product, $warehouseId);
        $product->warehouses()->syncWithoutDetaching([$warehouseId => ['quantity' => $oldQuantity - $quantity, 'unit_id' => $product->base_unit_id]]);
    }
}