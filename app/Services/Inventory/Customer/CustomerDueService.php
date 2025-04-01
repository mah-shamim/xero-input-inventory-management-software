<?php
/**
 * Created by PhpStorm.
 * User: Sharif
 * Date: 11/5/2019
 * Time: 8:09 PM
 */

namespace App\Services\Inventory\Customer;


use App\Models\Inventory\Sale;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CustomerDueService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        $sales = Sale::with('payments')->whereCustomerId($params['id'])->whereCompanyId(request()
            ->input('company_id'))->get();

        $totalSales = 0;
        $totalPayment = 0;
//        $totalReturnAmount = 0;
        $saleIds = [];
        foreach ($sales as $sale) {
            $totalSales = $totalSales + (float)$sale->total;
            $saleIds[] = $sale->id;
            if ($sale->payments) {
                foreach ($sale->payments as $item) {
                    $totalPayment = $totalPayment + (float)$item->paid;
                }
            }

        }
//        $returns = Returns::whereReturnableType("App\Models\Inventory\Sale")
//            ->whereCompanyId(request()
//            ->input('company_id'))->whereIn('returnable_id', $saleIds)->get();
//        if (count($returns)) {
//            foreach ($returns as $sRitem) {
//                $totalReturnAmount = $totalReturnAmount + (float)$sRitem->amount;
//            }
//        }

        $due = (float)$totalSales - (float)$totalPayment;
        $result['due']=$due;

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
        $array['message'] = 'Customer created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}