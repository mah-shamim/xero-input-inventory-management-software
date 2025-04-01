<?php

namespace App\Services\Inventory\Sales;

class SaleCrudMethods
{

    /**
     * @param mixed $item
     * @param array $location_detail
     * @return array
     */
    public function getArrForSale(mixed $item, array $location_detail): array
    {
        return [
            'quantity' => $item['quantity'],
            'price' => $item['unit_price'],
            'discount' => $item['discount'] ?? 0,
            'unit_id' => $item['unit'],
            'warehouse_id' => $item['warehouse'],
            'subtotal' => subTotalCalculation($item),
            'weight' => $item['weight'],
            'weight_total' => $item['weight_total'],
            'adjustment' => $item['adjustment'] ?? null,
            'locatable_type' => $location_detail ? $location_detail['class_location'] : 'fixed',
            'locatable_id' => $location_detail ? $location_detail['locatable']['id'] : null,
            'location_value' => $item['location'] ?? 0,
        ];
    }

}