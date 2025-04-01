<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\ExpenseRequest;
use App\Models\Account;
use App\Models\Expense\Expense;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\Expense\ExpenseCreateService;
use App\Services\Expense\ExpenseEditService;
use App\Services\Expense\ExpenseListService;
use App\Services\Expense\ExpenseUpdateService;
use App\Traits\Ledger;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{

    use Ledger;

    public function total_sum()
    {
        $expense = Expense::selectRaw('coalesce(sum(expenses.amount), 0) as total')
            ->where('company_id', request()->input('company_id'))->first();

        return response()->json($expense)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(ExpenseListService $expenseListService, Request $request)
    {
//        $this->authorize('viewAny', Expense::class);
        $result = $this->renderArrayOutput($expenseListService, $request, null);
        $result['accounts'] = $this->getExpenseAccounts();

        return response()->json($result)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Expense::class);
        $accounts = $this->getExpenseAccounts();

        return response()->json([
            'warehouses' => Warehouse::warehouseList()->get(),
            'banks' => $this->listOfBankWithRunningBalance(),
            'accounts' => $accounts,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);

    }

    /**
     * @return string
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ExpenseCreateService $expenseCreateService, ExpenseRequest $request)
    {
//        $this->authorize('create', Expense::class);

        return $this->renderJsonOutput($expenseCreateService, $request, null);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
//        $this->authorize('view', Expense::find($id));
        $expense = Expense::with(
            'warehouse:id,name',
            'account',
            'userable',
            'payments.transaction.bank'
        )
            ->find($id);

        return response()->json($expense)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * //     * @return string|Object
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id, ExpenseEditService $expenseEditService, Request $request)
    {
//        $this->authorize('update', Expense::find($id));

        return $this->renderJsonOutput($expenseEditService, $request, ['id' => $id]);

    }

    /**
     * @return string
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ExpenseUpdateService $expenseUpdateService, ExpenseRequest $request, $id)
    {
//        $this->authorize('update', Expense::find($id));

        return $this->renderJsonOutput($expenseUpdateService, $request, null);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
//        $this->authorize('delete', Expense::find($id));
        $expense = Expense::where('company_id', compid())->where('id', $id)->first();
        foreach ($expense->payments()->get() as $value) {
            $value->transaction()->delete();
            $value->forceDelete();
        }
        $expense->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Expense has been deleted Successfully',
        ]);
    }

    /**
     * @return mixed
     */
    public function getExpenseAccounts()
    {
        $expense_parent = Account::where('company_id', compid())->where('name', 'Expense')->where('type', 'group')->first();
        if (!$expense_parent) {
            abort(404, 'Expense account not found');
        } else {
            $expense_parent = $expense_parent->id;
        }

        $accounts = Account::leftjoin('accounts as a', 'a.id', 'accounts.parent_id')
            ->where('accounts.company_id', compid())
            ->where('accounts.parent_id', '>', 0)
            ->where('accounts.parent_id', $expense_parent)
            ->select('accounts.*', 'a.id as parent_id', 'a.name as group')
            ->groupBy('accounts.id')
            ->orderBy('accounts.parent_id')
            ->get();

        return $accounts;
    }
}
