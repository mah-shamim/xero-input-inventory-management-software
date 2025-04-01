<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/21/2017
 * Time: 11:46 AM
 */

namespace App\Services\Inventory\Warehouse;


use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarehouseUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $warehouse = Warehouse::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (!$warehouse) {
            $result['message'] = "Selected Warehouse does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['warehouse'] = $warehouse;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $warehouse = $array['warehouse'];
        $warehouse->name = $request->input('name');
        $warehouse->code = $request->input('code');
        $warehouse->phone = $request->input('phone');
        $warehouse->email = $request->input('email');
        $warehouse->address = $request->input('address');
        if($request->input('is_default')){

            toggle_default(Warehouse::class);
        }
        $warehouse->is_default = $request->input('is_default');
        $warehouse->save();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Warehouse updated successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}