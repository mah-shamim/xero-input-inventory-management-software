<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/24/2017
 * Time: 3:51 PM
 */

namespace App\Services\Inventory\Customer;


use App\Models\Inventory\Customer;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CustomerEditService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $customer = Customer::whereId($id)->whereCompanyId($request->input('company_id'))->first();
        if (!$customer) {
            $result['message'] = "Selected Customer does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['customers'] = $customer;
        $result['type'] = $customer;
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