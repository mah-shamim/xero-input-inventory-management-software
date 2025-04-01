<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 12/28/2017
 * Time: 11:15 AM
 */

namespace App\Services\Inventory\Unit;


use App\Models\Inventory\Unit;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $unit = Unit::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (!$unit) {
            $result['message'] = "Selected Unit does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['unit'] = $unit;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $unit = $array['unit'];
        $unit->key = $request->input('key');
        $unit->description = $request->input('description');
        $unit->is_primary = $request->input('is_primary');
        $unit->save();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Unit has been updated successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}