<?php
/**
 * Created by PhpStorm.
 * User: mdit-2(Razib)
 * Date: 1/24/2018
 * Time: 11:26 AM
 */

namespace App\Models\Expense\Traits;

use App\Models\Expense\Expense;
use App\Http\Controllers\Expense\ExpenseController;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait ExpenseReportTraits
{
    public function expenseReport(Request $request)
    {
        $expense = Expense::with('warehouse:id,name', 'userable', 'account')
            ->leftjoin('payments as p', function ($join) {
                $join->on('p.paymentable_id', '=', 'expenses.id')
                    ->where('p.paymentable_type', '=', "App\Models\Expense\Expense");
            })
            ->select('expenses.*', 'expenses.amount as total')
            ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
            ->selectRaw('expenses.amount - coalesce(sum(p.paid),0) as due')
            ->where('expenses.company_id', compid());

        $amount = $expense->sum('expenses.amount');
        $paid_total = $expense->sum('p.paid');

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
                if ($key == 'category') {
                    $expense->where('category_id', 'like', '%'.$query[$key].'%');
                }
                if ($key == 'id') {
                    $expense->where('id', $query[$key]);
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

        $expenses = $expense->groupBy('expenses.id')->paginate(itemPerPage());

        return response()->json([
            'accounts' => (new ExpenseController())->getExpenseAccounts(),
            'total_amount' => $amount,
            'paid_total' => $paid_total,
            'expenses' => $expenses,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
