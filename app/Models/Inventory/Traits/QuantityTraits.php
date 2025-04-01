<?php

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitConversion;
use Illuminate\Support\Facades\DB;

trait QuantityTraits
{
    public function updateQuantity($model, $newValue, $warehouse, $module = null, $seedModule = null, $pp_id = null)
    {
        $oldQuantity = $this->getQuantity($model, $warehouse);
        $new_quantity = $module != null
            ? $this->forEditQuantity($model, $newValue, $module, $oldQuantity, $pp_id)
            : $this->setQuantity($oldQuantity, $newValue, $seedModule);

        return $this->store($model, $new_quantity, $warehouse);
    }

    public function store($model, $new_quantity, $warehouse)
    {

        return $model->warehouses()
            ->syncWithoutDetaching(
                [
                    $warehouse => [
                        'quantity' => $new_quantity,
                        'unit_id' => $model->base_unit_id,
                    ],
                ]
            );
    }

    /**
     * @return mixed
     */
    public function setQuantity($oldValue, $newValue, $seedModule)
    {
        if ($seedModule != null) {
            return ($seedModule == 'purchases') ? $oldValue + $newValue : $oldValue - $newValue;
        }

        return moduleName(routeName()) == 'purchases' ? $oldValue + $newValue : $oldValue - $newValue;
    }

    /**
     * @return mixed
     */
    public function getQuantity($model, $warehouse)
    {
        $quantity = $model->warehouses->first();
        if ($quantity) {
            $warehouses = $model->warehouses;
            foreach ($warehouses as $w) {
                if ($w->pivot->warehouse_id == $warehouse) {
                    return $w->pivot->quantity;
                }
            }
        } else {
            return 0;
        }
    }

    private function forEditQuantity($model, $newValue, $module, $oldQuantity, $model_id)
    {
        //        dd($model_id, class_basename($module));
        $pivot_id = 'pp_id';
        if (class_basename($module) === 'Sale') {
            $pivot_id = 'ps_id';
        }
        $id = $module->products->first(function ($item, $m) use ($model, $model_id, $pivot_id) {
            if ($model_id) {
                return $item->pivot[$pivot_id] === $model_id;
            }

            return $item->pivot->product_id === $model->id;
        });

        //        var_dump([$id->pivot->quantity, $model_id, $newValue, $oldQuantity]);
        $previous_quantity_converted = $this->convertQuantity($id->pivot->quantity, $id->pivot->unit_id, $id->base_unit_id);
        if (! $model_id) {
            $previous_quantity_converted = 0;
        }
        if (class_basename($module) === 'Purchase') {
            $new_quantity = ($oldQuantity + $newValue) - $previous_quantity_converted;
        } else {
            $new_quantity = ($oldQuantity + $previous_quantity_converted) - $newValue;
        }

        return $new_quantity;
    }

    public function convertToActualQuantity($model, $givenQuantity)
    {
        if ($model->measurement == null || $model->measurement == 0) {
            return $givenQuantity;
        }
        $quantity = ($givenQuantity * pow(30.48, 2)) / pow($model->measurement, 2);

        return $quantity;
    }

    public function convertToPurchaseQuantity($model, $givenQuantity)
    {
        if ($model->measurement == null || $model->measurement == 0) {
            return $givenQuantity;
        }
        $quantity = ($givenQuantity * pow($model->measurement, 2)) / pow(30.48, 2);

        return $quantity;
    }

    public function getQuantityWithConversionsGlobal($product, $quantity)
    {

        $quantityStr = '';
        $unitList = $product->units;
        if (count($unitList) == 0) {
            return $quantityStr;
        }
        $maxParentId = $unitList->max('pivot.parent_id');
        $fromUnit = $unitList->first(function ($item) use ($maxParentId) {
            return $item->pivot->parent_id == $maxParentId;
        });
        $toUnit = $unitList->first(function ($item) {
            return $item->pivot->parent_id == 0;
        });
        $conversion = UnitConversion::with('from_unit', 'to_unit')->where('from_unit_id', $fromUnit->id)->where('to_unit_id', $toUnit->id)->first();
        if ($conversion == null && count($unitList) == 1) {
            return round((float) number_format($quantity), 4).' '.$fromUnit->key;
        } elseif ($conversion == null) {
            $result = 'Mapping for '.$fromUnit->key.' and '.$toUnit->key.' does not exist';

            return $result;
        }
        // dd($conversion);
        $quantity = $quantity * $conversion->conversion_factor;
        $quantity = round($quantity, 2); // commented out
        //        dd($quantity);
        //  //Added by Razib because if quantity= 59.99999999 then it makes problem
        if (strlen(substr(strrchr($quantity, '.'), 1)) > 6) {
            $quantity = $this->ceiling($quantity, 2);
        }
        if (is_numeric($quantity) && floor($quantity) != $quantity) {
            $roundQuantity = floor($quantity);
            if ($roundQuantity > 0) {
                $quantityStr = $roundQuantity.' '.$toUnit->key;
            }
            $fraction = fmod($quantity, 1);
            $quantityStr = $this->convertAndAdd($fraction, $unitList, $toUnit, $quantityStr);
        } else {
            $quantityStr = $quantity.' '.$toUnit->key;
        }

        return $quantityStr;
    }

    public function getQuantityWithConversions($product, $fromProduct = false, $warehouseId = null)
    {
        $unitConversionCount = DB::table('product_unit')
            ->whereProductId($product->id)->count();

        if ($unitConversionCount > 1) {
            $quantityStr = '';
            $unitList = $product->units;
            if (count($unitList) == 0) {
                return $quantityStr;
            }
            $maxParentId = $unitList->max('pivot.parent_id');
            $productUnit = $fromProduct ? Unit::find($product->warehouses[0]->pivot->unit_id) : Unit::find($product->pivot->unit_id);
            $fromUnit = $unitList->first(function ($item) use ($maxParentId) {
                return $item->pivot->parent_id == $maxParentId;
            });

            $toUnit = $unitList->first(function ($item) {
                return $item->pivot->parent_id == 0;
            });

            if ($fromProduct) {
                $quantity = 0;

                foreach ($product->warehouses as $war) {

                    if ($war->id == $warehouseId) {
                        $quantity = $war->pivot->quantity;
                    }
                }
            } else {
                $quantity = $product->pivot->quantity;
            }

            if ($fromUnit->id != $productUnit->id) {
                $conversion = UnitConversion::with('from_unit', 'to_unit')->where('from_unit_id', $productUnit->id)->where('to_unit_id', $fromUnit->id)->first();
                if ($conversion == null) {
                    $result = 'Mapping for '.$productUnit->key.' and '.$fromUnit->key.' does not exist';

                    return $result;
                }
                $quantity = $quantity * $conversion->conversion_factor;
            }
            $conversion = UnitConversion::with('from_unit', 'to_unit')->where('from_unit_id', $fromUnit->id)->where('to_unit_id', $toUnit->id)->first();
            if ($conversion == null) {
                $result = 'Mapping for '.$fromUnit->key.' and '.$toUnit->key.' does not exist';

                return $result;
            }

            $quantity = $quantity * $conversion->conversion_factor;
            $quantity = round($quantity, 2);  //Added by Razib because if quantity= 59.99999999 then it makes problem

            if (strlen(substr(strrchr($quantity, '.'), 1)) > 6) {

                $quantity = $this->ceiling($quantity, 2);
            }
            if (is_numeric($quantity) && floor($quantity) != $quantity) {
                $roundQuantity = floor($quantity);
                if ($roundQuantity > 0) {
                    $quantityStr = $roundQuantity.' '.$toUnit->key;
                }
                $fraction = fmod($quantity, 1);
                $quantityStr = $this->convertAndAdd($fraction, $unitList, $toUnit, $quantityStr);
            } else {
                $quantityStr = $quantity.' '.$toUnit->key;
            }
        } else {
            if ($fromProduct) {
                $quantity = 0;
                foreach ($product->warehouses as $war) {

                    if ($war->id == $warehouseId) {
                        $quantity = $war->pivot->quantity;
                    }
                }
            } else {
                $quantity = $product->pivot->quantity;
            }
            $unit = Unit::find($product->base_unit_id);
            $quantityStr = number_format(floatval($quantity), 2).' '.$unit->key;
        }

        return $quantityStr;
    }

    private function convertAndAdd($quantity, $unitList, $fromUnit, $quantityStr)
    {
        $toUnit = $unitList->first(function ($item) use ($fromUnit) {
            return $item->pivot->parent_id == $fromUnit->id;
        });
        if ($toUnit == null) {
            return $quantityStr;
        }
        $conversion = UnitConversion::with('from_unit', 'to_unit')->where('from_unit_id', $fromUnit->id)->where('to_unit_id', $toUnit->id)->first();
        if ($conversion == null) {
            $result = 'Mapping for '.$fromUnit->key.' and '.$toUnit->key.' does not exist';

            return $result;
        }

        //        $quantity = round($quantity, 2) * $conversion->conversion_factor; //Commented By Razib
        $quantity = $quantity * $conversion->conversion_factor; //Added by Razib

        if (is_numeric($quantity) && floor($quantity) != $quantity) {
            $roundQuantity = floor($quantity);

            if ($roundQuantity > 0) {
                $quantityStr = $quantityStr.' '.$roundQuantity.' '.$toUnit->key;
            }

            $fraction = fmod($quantity, 1);
            $fraction = round($fraction, 2); // Extra Line added by Razib
            $quantityStr = $this->convertAndAdd($fraction, $unitList, $toUnit, $quantityStr);
        } else {

            $quantityStr = $quantityStr.' '.$quantity.' '.$toUnit->key;
        }

        return $quantityStr;
    }

    public function ceiling($value, $precision = 0)
    {
        return ceil($value * pow(10, $precision)) / pow(10, $precision);
    }

    public function convertQuantity($quantity, $fromUnitId, $toUnitId)
    {
        $conversion = UnitConversion::with('from_unit', 'to_unit')->where('from_unit_id', $fromUnitId)->where('to_unit_id', $toUnitId)->first();
        if ($conversion == null) {
            return $quantity;
        }
        $quantity = $quantity * $conversion->conversion_factor;

        return $quantity;
    }

    public function getBaseQuantity($product, $givenQuantity, $unitId)
    {
        if ($product->base_unit_id == $unitId) {
            return $givenQuantity;
        }
        $conversion = UnitConversion::where('from_unit_id', $unitId)->where('to_unit_id', $product->base_unit_id)->where('company_id', $product->company_id)->first();
        if ($conversion == null) {
            return 'Unit conversion does not exist';
        }
        $givenQuantity = $givenQuantity * $conversion->conversion_factor;

        return $givenQuantity;
    }
}
