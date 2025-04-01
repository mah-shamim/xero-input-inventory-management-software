<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/28/2017
 * Time: 12:26 PM
 */

namespace App\Services\Inventory\UnitConversion;


use App\Models\Inventory\Unit;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ShowUnitConversionService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $units = Unit::whereCompanyId($request->input('company_id'))->get();
        $array['units'] = $units;

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