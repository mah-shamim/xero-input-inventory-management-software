<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/28/2017
 * Time: 1:06 PM
 */

namespace App\Services\Inventory\UnitConversion;


use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitConversionCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {

        $from_unit_id = $request->input('from_unit_id');
        $to_unit_id = $request->input('to_unit_id');
        $request->merge(['conversion_factor' => $to_unit_val = $request->input('to_unit_val') / $request->input('from_unit_val')]);

        UnitConversion::create($request->except('from_unit_val', 'to_unit_val'));

        $request->merge(['from_unit_id' => $to_unit_id]);
        $request->merge(['to_unit_id' => $from_unit_id]);
        $request->merge(['conversion_factor' => $request->input('from_unit_val') / $to_unit_val = $request->input('to_unit_val')]);
        UnitConversion::create($request->except('from_unit_val', 'to_unit_val'));

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Unit mapping has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}