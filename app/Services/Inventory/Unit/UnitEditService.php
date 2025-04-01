<?php


namespace App\Services\Inventory\Unit;


use App\Models\Inventory\Unit;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $unit = Unit::whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$unit) {
            $result['message'] = "Selected Unit does not exists anymore.Please refresh and try again";
        }
        $result['units'] = $unit;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
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