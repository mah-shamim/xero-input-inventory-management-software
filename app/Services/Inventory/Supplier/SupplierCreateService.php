<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/20/2017
 * Time: 2:25 PM
 */

namespace App\Services\Inventory\Supplier;


use App\Models\Inventory\Supplier;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class SupplierCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['supplier'] = Supplier::create($request->all());
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Supplier has been created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}