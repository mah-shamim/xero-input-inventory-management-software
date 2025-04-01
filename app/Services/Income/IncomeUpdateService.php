<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/18/2019
 * Time: 11:29 AM
 */

namespace App\Services\Income;


use App\Models\Income\Income;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class IncomeUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $request->input('id');
        $income = Income::find($id);
        if (!$income) {
            $result['message'] = "Selected Income does not exists anymore.Please refresh and try again";
            return $result;
        }
        $result['income'] = $income;
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $income = $array['income'];
        $income->update($request->all());
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = "Income updated successfully";
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}