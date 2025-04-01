<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/18/2019
 * Time: 11:14 AM
 */

namespace App\Services\Income;


use App\Models\Income\Income;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncomeCreateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {

        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }


    public function execute($array, Request $request)
    {
        $request->merge(array('income_date' => Carbon::parse($request->input('income_date'))->format('Y-m-d H:m:s')));
        Income::create($request->all());
        return $array;

    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Income created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}