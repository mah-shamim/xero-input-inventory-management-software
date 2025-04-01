<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/21/2017
 * Time: 11:01 AM
 */

namespace App\Services\Inventory\Brand;


use App\Models\Inventory\Brand;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class BrandUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $brand = Brand::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (!$brand) {
            $result['message'] = "Selected Brand does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['brand'] = $brand;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $brand = $array['brand'];
        $brand->name = $request->input('name');
        $brand->description = $request->input('description');
        $brand->save();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Brand has been updated successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}