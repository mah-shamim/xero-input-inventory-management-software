<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 12/27/2017
 * Time: 4:02 PM
 */

namespace App\Services\Inventory\Unit;


use App\Models\Inventory\Unit;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        Unit::create($request->all());
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Unit has been created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}