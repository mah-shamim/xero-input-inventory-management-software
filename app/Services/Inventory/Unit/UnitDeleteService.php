<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/1/2018
 * Time: 12:20 PM
 */

namespace App\Services\Inventory\Unit;

use App\Models\Inventory\Unit;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $unit = Unit::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        $count = $unit->conversions()->count();
        if ($count > 0) {
            $result['message'] = $count . " Unit exist with this Conversions. Please delete corresponding conversions first";
            return $result;
        }
        $result['unit'] = $unit;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $unit = $array['unit'];
        $unit->delete();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Unit has been deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}