<?php
/**
 * Created by PhpStorm.
 * User: Sharif
 * Date: 9/29/2019
 * Time: 7:31 PM
 */

namespace App\Services\Inventory\UnitConversion;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitConversionEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $brand = UnitConversion::with('from_unit', 'to_unit')->whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$brand) {
            $result['message'] = "Selected unit does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['unit'] = $brand;
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