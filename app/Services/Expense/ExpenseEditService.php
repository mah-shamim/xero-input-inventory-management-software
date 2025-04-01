<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/23/2018
 * Time: 2:44 PM
 */

namespace App\Services\Expense;

use App\Models\Expense\Expense;
use App\Http\Controllers\Expense\ExpenseController;
use App\Models\Inventory\Category;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use App\Traits\Ledger;
use Illuminate\Http\Request;

class ExpenseEditService implements ActionIntf
{
    use Ledger;

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];

        $expense = Expense::with('warehouse', 'userable', 'account')->whereId($id)->first();
        $accounts = (new ExpenseController())->getExpenseAccounts();
        if (! $expense) {
            $result['message'] = 'Selected Expense does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['expenses'] = $expense;
        $result['accounts'] = $accounts;
        $result['type'] = 'success';
        $result['banks'] = $this->listOfBankWithRunningBalance();

        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['warehouses'] = Warehouse::warehouseList()->get();
        $array['categories'] = Category::categoryList()->whereType('EXPENSE')->get();

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
