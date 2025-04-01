<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/1/2018
 * Time: 11:36 AM
 */

namespace App\Services\Inventory\Customer;

use App\Models\Inventory\Customer;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CustomerDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $customer = Customer::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        $count = $customer->sales()->count();
        if ($count > 0) {
            $result['message'] = $count.' Sales exist with this customer. Please delete corresponding sales first';

            return $result;
        }
        $result['customer'] = $customer;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $customer = $array['customer'];
        $customer->delete();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Customer has been deleted successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}