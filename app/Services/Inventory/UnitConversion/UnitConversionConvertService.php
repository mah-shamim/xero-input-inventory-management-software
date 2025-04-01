<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/15/2018
 * Time: 12:26 PM
 */

namespace App\Services\Inventory\UnitConversion;

use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitConversionConvertService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $fromUnitId = $params['from_unit_id'];
        $toUnitId = $params['to_unit_id'];
        $result['quantity'] = $params['quantity'];
        $fromUnit = Unit::whereId($fromUnitId)->fromCompany()->first();
        $toUnit = Unit::whereId($toUnitId)->fromCompany()->first();
        if ($fromUnit == null) {
            $result['message'] = $fromUnit->key.' does not exist as an unit';

            return $result;
        }
        if ($toUnit == null) {
            $result['message'] = $toUnit->key.' does not exist as an unit';

            return $result;
        }
        $conversion = UnitConversion::with('from_unit', 'to_unit')
            ->fromCompany()
            ->where('from_unit_id', $fromUnitId)
            ->where('to_unit_id', $toUnitId)
            ->first();
        if ($conversion == null) {
            $result['message'] = 'Mapping for '.$fromUnit->key.' and '.$toUnit->key.' does not exist';

            return $result;
        }
        $result['fromUnit'] = $fromUnit;
        $result['toUnit'] = $toUnit;
        $result['conversion'] = $conversion;
        $result['companyId'] = compid();
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $conversion = $array['conversion'];
        $quantity = $array['quantity'] * $conversion->conversion_factor;
        if ($request->input('isPurchase') == true) {
            $product = Product::whereId($request->input('productId'))->fromCompany()->first();
            if ($array['toUnit']->is_primary) {
                $quantity = $product->convertToPurchaseQuantity($product, $quantity);
            } elseif ($array['fromUnit']->is_primary) {
                $quantity = $product->convertToActualQuantity($product, $quantity);
            }
        }
        if (is_numeric($quantity) && floor($quantity) != $quantity) {
            $roundQuantity = floor($quantity);
            $fraction = fmod($quantity, 1);
            $conversionNext = UnitConversion::with('from_unit', 'to_unit')->where('from_unit_id', $array['toUnit']->id)->where('to_unit_id', $array['fromUnit']->id)->fromCompany()->first();
            if ($conversionNext != null) {
                $array['conversionStr'] = $array['quantity'].' '.$array['fromUnit']->key.' = '.$roundQuantity.' '.$array['toUnit']->key.' '.round($fraction * $conversionNext->conversion_factor).' '.$conversionNext->to_unit->key;
            }
        }
        $array['quantity'] = $quantity;

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}