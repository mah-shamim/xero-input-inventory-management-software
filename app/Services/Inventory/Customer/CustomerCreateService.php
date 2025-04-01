<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 12/14/2017
 * Time: 12:11 PM
 */

namespace App\Services\Inventory\Customer;


use App\Models\Inventory\Customer;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CustomerCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params): array
    {
        return ['type' => 'success', 'message' => ''];
    }

    public function execute($array, Request $request)
    {
        if ($request->input('is_default')) {
            customer_default();
        }
        if (! $request->input('is_default')) {
            $request->merge(['is_default' => false]);
        }
        $customer = Customer::create($request->all());
        $array['customer'] = Customer::whereId($customer->id)->first();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Customer has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}