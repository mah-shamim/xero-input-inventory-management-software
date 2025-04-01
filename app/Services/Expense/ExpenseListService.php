<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/8/2018
 * Time: 4:56 PM
 */

namespace App\Services\Expense;

use App\Models\Expense\Expense;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseListService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $expense = Expense::with('warehouse:id,name', 'userable', 'account')
            ->leftjoin('payments as p', function ($join) {
                $join->on('p.paymentable_id', '=', 'expenses.id')
                    ->where('p.paymentable_type', '=', Expense::classPath());
            })
            ->select('expenses.*', 'expenses.amount as total')
            ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
            ->selectRaw('expenses.amount - coalesce(sum(p.paid),0) as due')
            ->where('expenses.company_id', compid())
            ->groupBy('expenses.id');
        $query = request()->query();

        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'ref' || $key == 'amount') {
                    $expense->where('expenses.'.$key, 'like', '%'.$query[$key].'%');
                }

                if ($key == 'warehouse') {
                    $expense->whereHas('warehouse', function ($w) use ($key, $query) {
                        $w->where('name', 'like', '%'.$query[$key].'%');

                    });
                }

                if ($key == 'id') {
                    $expense->where('expenses.id', $query[$key]);
                }
            }
        }
        if ($request->get('account_id')) {
            $expense->where('expenses.account_id', $request->get('account_id'));
        }

        if (request()->get('expense_date')) {
            if (count(request()->input('expense_date')) === 1) {
                $expense->whereDate('expenses.expense_date', request()->input('expense_date')[0]);
            }
            if (count(request()->input('expense_date')) == 2) {

                if (Carbon::parse(request()->input('expense_date')[0]) < Carbon::parse(request()->input('expense_date')[1])) {
                    $expense->whereBetween('expenses.expense_date', request()->get('expense_date'));
                } else {
                    $expense->whereBetween('expenses.expense_date', array_reverse(request()->get('expense_date')));
                }

            }
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $expense->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }
        $array['expenses'] = $expense->paginate(itemPerPage());

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
