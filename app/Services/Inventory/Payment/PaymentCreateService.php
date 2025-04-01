<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/14/2018
 * Time: 2:45 PM
 */

namespace App\Services\Inventory\Payment;

use App\Http\Controllers\Bank\TransactionController;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class PaymentCreateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {

        $result = ['type' => 'error', 'message' => ''];
        $result['model'] = $this->getPaymentableType($params['model']);
        $result['paymentAbleId'] = $params['paymentableId'];
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        if ($array['model'] == Purchase::classPath()) {
            (new TransactionController())->create_bank_transaction($request, 'credit');
            Purchase::find($array['paymentAbleId'])->payments()->create($request->all());
        } elseif ($array['model'] == 'App\Models\Inventory\Sale') {
            (new TransactionController())->create_bank_transaction($request, 'debit');
            Sale::find($array['paymentAbleId'])->payments()->create($request->all());
        } else {
            return back();
        }

        return $array;

    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Payment created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

    private function getPaymentableType($model)
    {
        $paymentableType = '';
        switch ($model) {
            case 'purchases':
                $paymentableType = Purchase::classPath();
                break;
            case 'sales':
                $paymentableType = "App\Models\Inventory\Sale";
                break;
            default:
                break;
        }

        return $paymentableType;
    }
}
