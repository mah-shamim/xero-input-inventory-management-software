<?php

namespace App\Models\Inventory\Traits;

use App\Enums\WarehouseTypeEnum;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Models\Inventory\Warehouse\WarehouseBin;
use App\Models\Inventory\Warehouse\WarehouseIsle;
use App\Models\Inventory\Warehouse\WarehousePicking;
use App\Models\Inventory\Warehouse\WarehouseRack;
use App\Services\Inventory\Purchase\PurchaseCreateService;
use App\Services\Inventory\Sales\SalesCreateService;
use Illuminate\Support\Facades\DB;

/**
 * Trait LocationTraits
 */
trait LocationTraits
{
    use QuantityTraits, WeightTraits;

    public function scopeGetSingleProductLocationByWarehouse($query, $product_id){
        $query->leftjoin('product_warehouse as pw', 'pw.warehouse_id', '=', 'warehouses.id')
        ->where('pw.product_id', $product_id);
        return $query;
    }

    public function scopeGetAllProductLocationByWarehouse($query){
        $query->leftjoin('product_warehouse as pw', 'pw.warehouse_id', '=', 'warehouses.id');
        return $query;
    }

    public function scopeCheckProductExistInLocation($query, $location, $warehouse_id): \Illuminate\Database\Query\Builder
    {
        return DB::table('product_warehouse')
            ->where('warehouse_id', $warehouse_id)
            ->whereNotNull('location->'.$location)
            ->where('location->'.$location.'->quantity', '>', 0);
    }

    public function getSaleLocationAttribute()
    {
        $location = '';
        $sale_id = $this->getOriginal('pivot_sale_id');
        if ($sale_id) {
            $location = $this->getOriginal('pivot_location_value');
        }

        return $location;
    }

    public function getPurchasedLocationAttribute()
    {
        $location = '';
        $purchased_id = $this->getOriginal('pivot_purchase_id');
        if ($purchased_id) {
            $location = $this->getOriginal('pivot_location_value');
        }

        return $location;
    }

    public function findLocatableType($str)
    {
        $arr = $this->getLocationStringAttribute($str);
        if (count($arr)) {
            return $arr['class_location'];
        } else {
            return '';
        }
    }

    public function getGlobalLocationStringToEloquent($str): array
    {
        $location = [];
        if ($str) {
            $arr = explode('-', $str);
            $warehouse = Warehouse::where('warehouses.id', (int) $arr[1])->first();
            $location['warehouse'] = $warehouse;

            if (count($arr) === 5) {
                $location['type'] = WarehouseTypeEnum::Storage;
                $location['isle'] = WarehouseIsle::where('warehouse_isles.id', (int) $arr[2])->first();
                $location['rack'] = WarehouseRack::where('warehouse_racks.id', (int) $arr[3])->first();
                $location['locatable'] = WarehouseBin::where('warehouse_bins.id', (int) $arr[4])->first();
                $location['class_location'] = pathinfo(WarehouseBin::class)['basename'];
            }
            if (count($arr) === 3) {
                $location['type'] = WarehouseTypeEnum::Picking;
                $location['locatable'] = WarehousePicking::where('warehouse_pickings.id', (int) $arr[2])->first();
                $location['class_location'] = pathinfo(WarehousePicking::class)['basename'];
            }
        }

        return $location;
    }

    /**
     * @return array
     */
    public function getLocationStringAttribute(): array
    {
        $locations = [];
        if ($this->relationLoaded('isles') && count($this['isles'])) {
            foreach ($this->isles as $isle) {
                foreach ($isle->racks as $rack) {
                    foreach ($rack->bins as $bin) {
                        $value = [
                            request()->company_id, $this['id'],
                            $isle->id, $rack->id, $bin->id,
                        ];
                        $locations[] = implode('-', $value);
                    }
                }
            }
        }
        if ($this->relationLoaded('pickings') && count($this['pickings'])) {
            foreach ($this->pickings as $picking) {
                $value = [request()->company_id, $this['id'], $picking->id];
                $locations[] = implode('-', $value);
            }
        }

        return $locations;
    }

    public function updateLocation($product, $item, $module = 'purchase'): void
    {
//        dd($product, $item);
        $old_quantity = 0;
        $operation = 'sum';
        $warehouse = $this->warehouse_product_rel($product, $item);
        if ($warehouse && $warehouse->location && array_key_exists($item['location'], $warehouse->location)) {
            $old_quantity = $warehouse->location[$item['location']]['quantity'];
        }
        if ($module === 'purchase') {
            $array = (new PurchaseCreateService())->convertQuantity($product, $item, []);
        }
        if ($module === 'sale') {
            $array = (new SalesCreateService())->convertQuantity($product, $item, []);
            $operation = 'subtract';
            $array['quantity'] = -$array['quantity'];
        }

        $quantity = $old_quantity + $array['quantity'];
        $weight = $product->weightConversionForLocation($item, $warehouse, $operation);
        $location = $this->updateLocationValue($warehouse, $item, $weight, $quantity);
        $product->warehouses()->syncWithoutDetaching(
            [
                $item['warehouse'] => ['location' => $location],
            ]
        );
    }

    public function model_product_rel($product, $model, $arr = null)
    {
        $model_product = $model->products->first(function ($p) use ($product, $arr) {
            if (($arr && array_key_exists('model_id', $arr) && $arr['model_id'])) {
                return $p->pivot[$arr['model_column_name']] === (int) $arr['model_id'];
            } else {
                return $p->id === $product->id;
            }
        });

        return $model_product ? $model_product->pivot : null;
    }

    public function updateLocationEdit($product, $arr, $model): void
    {
        $arr['class_name'] = class_basename($model);
        if ($arr['class_name'] === 'Sale') {
            $arr['model_id'] = $arr['ps_id'];
            $arr['model_column_name'] = 'ps_id';
        }
        if ($arr['class_name'] === 'Purchase') {
            $arr['model_id'] = $arr['pp_id'];
            $arr['model_column_name'] = 'pp_id';
        }

        $warehouse_product = $this->warehouse_product_rel($product, $arr);
        $model_product = $this->model_product_rel($product, $model, $arr);

        $location_value = $model_product ? $model_product['location_value'] : null;
        $quantity = 0;
        $location = $warehouse_product->location;
        if ($arr['location'] === $location_value) {
            $old_quantity = $arr['class_name'] === 'Sale' ? $model_product['quantity'] : -$model_product['quantity'];
            $quantity = $arr['class_name'] === 'Sale' ? -$arr['quantity'] : $arr['quantity'];
            $quantity = $warehouse_product['location'][$arr['location']]['quantity'] + $old_quantity + $quantity;
            $operation = $arr['class_name'] === 'Sale' ? 'subtract' : 'sum';
            $weight = $product->weightConversionForLocationEdit($arr, $model_product, $warehouse_product, $operation);
            $location[$arr['location']] = [
                'weight' => $weight,
                'quantity' => $quantity,
            ];
        }

        if ($arr['model_id'] && $warehouse_product && $arr['location'] !== $location_value) {
            if ($arr['warehouse'] === $warehouse_product->warehouse_id && $model_product) {
                $old_quantity = $arr['class_name'] === 'Sale' ? -$model_product['quantity'] : $model_product['quantity'];
                $old_weight = $arr['class_name'] === 'Sale' ? -$model_product['weight_total'] : $model_product['weight_total'];

//                dd(
//                    $warehouse_product,
//                    $model_product,
//                    $warehouse_product['location'][$model_product['location_value']]['quantity'],
//                    $old_quantity
//                );

                $adjusted_quantity = $warehouse_product['location'][$model_product['location_value']]['quantity'] - $old_quantity;
                $adjusted_weight = $warehouse_product['location'][$model_product['location_value']]['weight'] - $old_weight;
                $item['location'] = $model_product['location_value'];
                $location[$item['location']] = [
                    'weight' => $adjusted_weight,
                    'quantity' => $adjusted_quantity,
                ];
                //                dd($location);

                $operation = $arr['class_name'] === 'Sale' ? 'subtract' : 'sum';
                $weight = $product->weightConversionForLocationEdit($arr, $model_product, $warehouse_product, $operation);
                $input_quantity = $arr['class_name'] === 'Sale' ? -$arr['quantity'] : $arr['quantity'];
                $quantity = array_key_exists($arr['location'], $warehouse_product['location'])
                    ? $warehouse_product['location'][$arr['location']]['quantity'] + $input_quantity
                    : $arr['quantity'];

                //                dd($weight, $quantity);

                $location[$arr['location']] = [
                    'weight' => $weight,
                    'quantity' => $quantity,
                ];
            }
        }

        if (! $model_product || ! $arr['model_id']) {
            $quantity = $arr['quantity'];
            $weight = $arr['weight_total'];
            $location[$arr['location']] = [
                'weight' => $weight,
                'quantity' => $quantity,
            ];
        }

        if (isset($location) && count($location)) {
            $this->doSyncWithoutDetaching($product, $arr['warehouse'], $location);
        }
    }

    /**
     * @return array|mixed|null
     */
    public function updateLocationValue($warehouse, $item, $weight, $quantity)
    {
        $location = [];
        if ($warehouse->location) {
            if ($warehouse && array_key_exists($item['location'], $warehouse->location)) {
                $location = $this->updateLocationItemValue($warehouse, $weight, $item, $quantity);
            }
            if ($warehouse && ! array_key_exists($item['location'], $warehouse->location) && count($warehouse->location)) {
                $location = $warehouse->location;
                $location = $this->storeLocationValue($weight, $quantity, $location, $item);
            }
            if (! $warehouse) {
                $location = $this->storeLocationValue($weight, $quantity, $location, $item);
            }
        } else {
            $location = $this->storeLocationValue($weight, $quantity, $location, $item);
        }

        return count($location) ? $location : null;
    }

    public function updateLocationItemValue($warehouse, $weight, $item, $quantity): array
    {
        $location = $warehouse->location;
        $location[$item['location']]['weight'] = $weight;
        $location[$item['location']]['quantity'] = $quantity;

        return $location;
    }

    public function storeLocationValue($weight, int $quantity, $location, $item): mixed
    {
        $location[$item['location']] = [
            'weight' => $weight,
            'quantity' => $quantity,
        ];

        return $location;
    }

    /**
     * @return mixed
     */
    public function warehouse_product_rel($product, $item): mixed
    {
        $warehouse = $product->warehouses()->where('warehouse_id', $item['warehouse'])->first();
        //        $warehouse = $product->warehouses->first(function ($warehouse) use ($item) {
        //            return $warehouse->id === (int) $item['warehouse'];
        //        });

        return $warehouse ? $warehouse->pivot : null;
    }

    public function doSyncWithoutDetaching($product, $warehouse_id, ?array $location): void
    {
        $product->warehouses()->syncWithoutDetaching(
            [
                $warehouse_id => ['location' => $location],
            ]
        );
    }
}
