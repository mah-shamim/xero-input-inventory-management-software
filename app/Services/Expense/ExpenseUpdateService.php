<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/23/2018
 * Time: 4:41 PM
 */

namespace App\Services\Expense;

use App\Models\Expense\Expense;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ExpenseUpdateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $request->input('id');
        $expense = Expense::where('company_id', compid())
            ->with('payments.transaction')
            ->where('id', $id)
            ->first();

        if (! $expense) {
            $result['message'] = 'Selected Expense does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['expense'] = $expense;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $expense = $array['expense'];

        foreach ($expense->payments as $payment) {
            $payment->transaction->update([
                'userable_id' => $request->input('userable_id')['id'],
                'userable_type' => $request->input('userable_id')['model'],
                'account_id' => $request->input('account_id'),
            ]);
        }
        $expense->update(array_merge($request->all(), [
            'userable_id' => $request->input('userable_id')['id'],
            'userable_type' => $request->input('userable_id')['model'],
        ]));

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Expense has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
