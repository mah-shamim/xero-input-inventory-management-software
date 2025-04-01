<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/4/2018
 * Time: 4:07 PM
 */

namespace App\Services\Expense;

use App\Models\Expense\Expense;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseCreateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {

        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    /**
     * @return mixed
     */
    public function execute($array, Request $request)
    {
        $request->merge([
            'expense_date' => Carbon::parse($request->input('expense_date'))->format('Y-m-d H:m:s'),
            'payment_type' => $request->input('payment_method'),
            'ref_no' => $request->input('ref'),
        ]);

        if (gettype($request->input('userable_id')) === 'array' && array_key_exists('id', $request->input('userable_id'))) {
            $request->merge([
                'userable_id' => $request->input('userable_id')['id'],
                'userable_type' => $request->input('userable_id')['model'],
            ]);
        }
        $request->merge(['payment_method' => $request->input('payment_type')]);
        $array['expense'] = Expense::create($request->all());

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Expense has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
