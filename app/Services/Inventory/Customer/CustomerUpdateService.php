<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/2/2018
 * Time: 11:41 AM
 */

namespace App\Services\Inventory\Customer;


use App\Models\Inventory\Customer;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CustomerUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $customer = Customer::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();

        if (! $customer) {
            $result['message'] = 'Selected Customer does not exists anymore. Please refresh and try again';

            return $result;
        }
        $result['customer'] = $customer;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {

        $customer = $array['customer'];
        if ($request->input('is_default')) {
            customer_default();
        }
        $customer->update($request->all());

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Customer has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}