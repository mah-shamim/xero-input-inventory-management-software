<?php

namespace App\Services\Inventory\Purchase;

use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitConversion;

trait PurchaseServicesCommonMethods
{
    private function updateBuyingPrice($product, $item, $array, $isPrimaryUnit)
    {
        if ($isPrimaryUnit) {
            return $item['unit_price'];
        } else {
            $purchaseQuantity = $product->convertToPurchaseQuantity($product, $array['quantity']);

            return ($item['unit_price'] / $purchaseQuantity) * $item['quantity'];
        }
    }

    public function convertQuantity(Product $product, $item, $array): mixed
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
    public function processProductData($item, $array): array
    {
        $product = Product::with('warehouses')->find($item['product_id']);
        $subtotal = subTotalCalculation($item);
        $purchaseQuantity = $item['quantity'];
        $isPrimaryUnit = Unit::find($item['unit'])->is_primary;
        if ($isPrimaryUnit) {
            $item['quantity'] = $product->convertToActualQuantity($product, $item['quantity']);
        }
        $array = $this->convertQuantity($product, $item, $array);
        if ($array['message'] != '') {
            return $array;
        }
        $location_detail = (new Product())->getGlobalLocationStringToEloquent($item['location']);
        return [$product, $purchaseQuantity, $subtotal, $location_detail];

    }
    /**
     * @param mixed $item
     * @param mixed $purchaseQuantity
     * @param mixed $subtotal
     * @param array $location_detail
     * @return array
     */
    public function getArr(mixed $item, mixed $purchaseQuantity, mixed $subtotal, array $location_detail): array
    {
        return [
            'quantity' => $item['quantity'],
            'purchase_quantity' => $purchaseQuantity,
            'price' => $item['unit_price'],
            'discount' => $item['discount'] ?? 0,
            'unit_id' => $item['unit'],
            'warehouse_id' => $item['warehouse'],
            'subtotal' => $subtotal,
            'weight' => $item['weight'],
            'weight_total' => $item['weight_total'],
            'adjustment' => $item['adjustment'] ?? null,
            'locatable_type' => $location_detail ? $location_detail['class_location'] : 'fixed',
            'locatable_id' => $location_detail ? $location_detail['locatable']['id'] : null,
            'location_value' => $item['location'] ?? 0,
        ];
    }
}