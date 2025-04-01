<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/20/2017
 * Time: 11:25 AM
 */

namespace App\Services\Inventory\Brand;


use App\Models\Inventory\Brand;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class BrandEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $brand = Brand::whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$brand) {
            $result['message'] = "Selected Brand does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['brands'] = $brand;
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