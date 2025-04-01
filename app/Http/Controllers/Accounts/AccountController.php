<?php

namespace App\Http\Controllers\Accounts;

use App\Models\Account;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Inventory\Traits\ReturnsReport;
use App\Models\Payroll\Salary;
use App\Services\Inventory\UnitConversion\UnitConversionConvertService;
use App\Traits\BalanceSheet;
use App\Traits\CashFlow;
use App\Traits\CommonAccountMethods;
use App\Traits\Ledger;
use App\Traits\ProfitLoss;

/**
 * Class AccountController
 */
class AccountController extends Controller
{
    use CommonAccountMethods,
        ReturnsReport,
        Ledger,
        CashFlow,
        BalanceSheet,
        ProfitLoss;

    public UnitConversionConvertService $unitConversionConvertService;

    public function __construct(UnitConversionConvertService $unitConversionConvertService)
    {

        $this->unitConversionConvertService = $unitConversionConvertService;
    }

    public function get_accounts_by_parent($parent_name)
    {
        $parent = Account::where('company_id', compid())->where('name', trim(ucfirst($parent_name)))->where('type', 'group')->first();

        if (! $parent) {
            abort(404, 'Income account not found');
        } else {
            $parent = $parent->id;
        }

        return Account::leftjoin('accounts as a', 'a.id', 'accounts.parent_id')
            ->where('accounts.company_id', request()->input('company_id'))
            ->where('accounts.parent_id', '>', 0)
            ->where('accounts.parent_id', $parent)
            ->select('accounts.*', 'a.id as parent_id', 'a.name as group')
            ->groupBy('accounts.id')
            ->orderBy('accounts.parent_id')
            ->get();
    }

    public function dynamicLedger($account_id)
    {
        if (request()->get('with_total') && request()->get('with_total') == '1') {
            return response()->json([
                'total' => $this->account_total_debit_total_credit($account_id),
                'data' => $this->ledgerOfDynamicAccount($account_id),
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        } else {
            return response()->json($this->ledgerOfDynamicAccount($account_id))->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse|void
     */
    public function index()
    {
        $arr['accounts'] = Account::with('children')
            ->where('parent_id', 0)
            ->where('company_id', request()->input('company_id'));

        $arr['accounts'] = $arr['accounts']->get();

        return response()->json($arr)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show($id)
    {
        return response()->json(
            Account::with('children')->where('company_id', compid())->where('id', $id)->first()
        )->setEncodingOptions(JSON_NUMERIC_CHECK);

    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create()
    {
        $arr['parent_account'] = Account::where('parent_id', 0)
            ->where('company_id', request()->input('company_id'))
            ->get();

        return response()->json($arr)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AccountRequest $request)
    {
        return $request->save();
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $arr['account'] = Account::where('id', $id)->where('company_id', request()->input('company_id'))->first();

        return response()->json($arr)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AccountRequest $request, Account $account)
    {
        return $request->update($account);
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return response()->json(['type' => 'success', 'message' => 'account has been deleted successfully']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function payable()
    {
        [$purchaseTotal, $purchasePaid] = $this->purchaseTotal_purchasePaid();
        [$saleTotal, $salePaid] = $this->saleTotal_salePaid();

        return response()->json([
            'purchases' => [
                'purchase_total' => $purchaseTotal,
                'purchase_paid' => $purchasePaid,
            ],
            'sales' => [
                'sale_total' => $saleTotal,
                'sale_paid' => $salePaid,
            ],
            'purchase_return' => $this->purchase_return(),
            'sale_return' => $this->sale_return(),
            'payroll' => $this->payroll()->first(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return mixed
     */
    public function payroll()
    {
        return Salary::select('salaries.*')
            ->selectRaw('coalesce(sum(salaries.amount),0) as total_paid')
            ->selectRaw('coalesce(sum(salaries.current_salary),0) as total_current_salary')
            ->selectRaw('coalesce(sum(salaries.current_salary),0) - coalesce(sum(salaries.amount),0) as total_due')
            ->selectRaw('coalesce(sum(salaries.amount),0) - coalesce(sum(salaries.current_salary),0) as negative_total_due')
            ->where('company_id', request()->input('company_id'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function receivable()
    {

        [$purchaseTotal, $purchasePaid] = $this->purchaseTotal_purchasePaid();
        [$saleTotal, $salePaid] = $this->saleTotal_salePaid();

        return response()->json([
            'purchases' => [
                'purchase_total' => $purchaseTotal,
                'purchase_paid' => $purchasePaid,
            ],
            'sales' => [
                'sale_total' => $saleTotal,
                'sale_paid' => $salePaid,
            ],
            'purchase_return' => $this->purchase_return(),
            'sale_return' => $this->sale_return(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function purchase_return()
    {
        $purchaseReturn = $this->purchaseReturnQuery();
        $paid = $purchaseReturn->pluck('returns.total_return_amount')->first();
        $totalPrice = $purchaseReturn->groupBy('returns.returnable_id')->get()->sum('total_price');
        $balance = $purchaseReturn->groupBy('returns.returnable_id')->get()->sum('balance');
        $negative_balance = $purchaseReturn->groupBy('returns.returnable_id')->get()->sum('negative_balance');

        return ['paid' => $paid, 'total' => $totalPrice, 'balance' => $balance, 'negative_balance' => $negative_balance];
    }

    /**
     * @return array
     */
    private function sale_return()
    {
        $saleReturn = $this->saleReturnQuery();
        $paid = $saleReturn->pluck('returns.total_return_amount')->first();
        $totalPrice = $saleReturn->groupBy('returns.returnable_id')->get()->sum('total_price');
        $balance = $saleReturn->groupBy('returns.returnable_id')->get()->sum('balance');
        $negative_balance = $saleReturn->groupBy('returns.returnable_id')->get()->sum('negative_balance');

        return ['paid' => $paid, 'total' => $totalPrice, 'balance' => $balance, 'negative_balance' => $negative_balance];
    }

    public function total_debit_credit($account_id)
    {
        return response()->json($this->account_total_debit_total_credit($account_id))
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
