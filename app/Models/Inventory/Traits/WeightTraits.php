<?php

namespace App\Models\Inventory\Traits;

/**
 * Trait WeightTraits
 */
trait WeightTraits
{
    /**
     * $operation should be sum or subtract
     *
     * @param [type] $operation
     * @param [type] $product
     * @param [type] $warehouse_id
     * @param [type] $weight
     * @param [type] $method
     * @return void
     */
    public function updateWeight($operation, $product, $warehouse_id, $weight)
    {
        $warehouse = $product->warehouses()->where('warehouse_id', $warehouse_id)->first();
        $old_weight = $warehouse
            ? $warehouse->pivot->weight
            : 0;

        $item = [];
        $new_weight = $this->operation($operation, $old_weight, $weight);
        $item['weight'] = $new_weight;

        if (! $warehouse) {
            $item['quantity'] = 0;
            $item['unit_id'] = $product->base_unit_id;
        }

        $product->warehouses()->syncWithoutDetaching([$warehouse_id => $item]);
    }

    /**
     * $model should be 'purhcase', or 'sale'
     * $operation should be 'sum' or 'subtract'
     *
     * @param [type] $operation
     * @param [type] $product
     * @param [type] $model
     * @param [type] $warehouse_id
     * @param [type] $weight
     * @param [type] $arr
     * @return void
     */
    public function editUpdateWeight($product, $model, $warehouse_id, $weight, $operation, $model_id = null) //from sale and purchase
    {
        $pivot_id = 'pp_id';
        if (class_basename($model) === 'Sale') {
            $pivot_id = 'ps_id';
        }
        $previous_purchase = $model->products->first(function ($p) use ($product, $model_id, $pivot_id) {
            return $model_id
                ? $p->pivot[$pivot_id] === (int) $model_id
                : $p->id === $product->id;
        });

        $previous_purchase = $previous_purchase ? $previous_purchase->pivot : 0;
        $previous_purchase_weight = $previous_purchase ? $previous_purchase->weight_total : 0;
        if (! $model_id) {
            $previous_purchase_weight = 0;
        }
        $adjusted_weight = $weight - $previous_purchase_weight;

        $warehouse = $product->warehouses()->where('warehouse_id', $warehouse_id)->first();

        $existing_weight = $warehouse->pivot->weight;
        if ($operation === 'sum') {
            $new_weight = $existing_weight + $adjusted_weight;

            $product->warehouses()
                ->syncWithoutDetaching([$warehouse_id => ['weight' => $new_weight]]);
        }

        if ($operation === 'subtract') {
            $new_weight = $existing_weight - $adjusted_weight;
            $product->warehouses()
                ->syncWithoutDetaching([$warehouse_id => ['weight' => $new_weight]]);
        }
    }

    /**
     * @return bool|int|mixed
     */
    public function weightConversionForLocation($location, $warehouse, $operation)
    {
        $adjusted_weight = $warehouse->location && array_key_exists($location['location'], $warehouse->location)
            ? $warehouse->location[$location['location']]['weight']
            : 0;

        //        dd($adjusted_weight, $warehouse);

        return $this->operation($operation, $adjusted_weight, $location['weight_total']);

    }

    public function weightConversionForLocationEdit($arr, $purchase_product, $warehouse, $operation)
    {
        $adjusted_weight = 0;
        $new_weight = 0;

        if ($arr['class_name'] === 'Sale') {

        }
        //        dd($warehouse['location'][$arr['location']]['weight']);
        //        dd(
        //            $arr['location'],
        //            $purchase_product['location_value'],
        //            $arr['warehouse'],
        //            $warehouse,
        //            $warehouse['warehouse_id'],
        //            $warehouse['location'[$arr['location']]['weight']],
        //            $warehouse->warhouse_id
        //        );

        if (($arr['warehouse'] === $warehouse['warehouse_id']) && ($arr['location'] === $purchase_product['location_value'])) {
            $old_weight = $arr['class_name'] === 'Sale' ? $purchase_product['weight_total'] : -$purchase_product['weight_total'];
            $adjusted_weight = $warehouse['location'][$arr['location']]['weight'] + $old_weight;
        }

        if (($arr['warehouse'] === $warehouse['warehouse_id']) && ($arr['location'] !== $purchase_product['location_value'])) {
            $adjusted_weight = array_key_exists($arr['location'], $warehouse['location'])
                ? $warehouse['location'][$arr['location']]['weight']
                : 0;
        }

        if ($warehouse && ($arr['warehouse'] !== $warehouse['warehouse_id'])) {
            $adjusted_weight = 0;
        }

        $new_weight = $this->operation($operation, $adjusted_weight, $arr['weight_total']);

        return $new_weight;
    }

    /**
     * @return bool|int|float
     */
    public function operation($operation, $adjusted_weight, $changed_weight)
    {
        $new_weight = 0;
        if ($operation === 'sum') {
            $new_weight = $adjusted_weight + $changed_weight;
        }
        if ($operation === 'subtract') {
            $new_weight = $adjusted_weight - $changed_weight;
        }

        return $new_weight;
    }
}
