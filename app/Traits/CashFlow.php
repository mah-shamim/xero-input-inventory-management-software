<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\Bank\Transaction;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Returns;
use App\Models\Payroll\Salary;
use Carbon\Carbon;

trait CashFlow
{
    /**
     * @return \Illuminate\Http\JsonResponse|null
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function cashFlow()
    {
        if (! (request()->get('selectedType') && request()->get('type'))) {
            return null;
        }

        $dateBetween = [];
        $explodedArr = explode('-', request()->get('selectedType'));
        if (request()->get('type') === 'Year') {
            $year = request()->get('selectedType');
            $month = 1;
            $day = 1;
            $date = Carbon::createFromDate($year, $month, $day);
            $dateBetween = [
                $date->copy()->startOfYear(),
                $date->copy()->endOfYear(),
            ];
        }
        if (request()->get('type') === 'Month') {
            $year = $explodedArr[0];
            $month = $explodedArr[1];
            $day = 1;
            $date = Carbon::createFromDate($year, $month, $day);
            $dateBetween = [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ];
        }
        if (request()->get('type') === 'Day') {
            $year = $explodedArr[0];
            $month = $explodedArr[1];
            $day = $explodedArr[2];
            $date = Carbon::createFromDate($year, $month, $day);
            $dateBetween = [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ];
        }
        [$sale_cash, $purchase_cash, $return_sale_cash, $return_purchase_cash, $salary_cash, $dynamic_accounts, $final_arr, $total_sum] = $this->balanceQueries($dateBetween);
        [$opening_sale_cash, $opening_purchase_cash, $opening_return_sale_cash, $opening_return_purchase_cash, $opening_salary_cash, $opening_dynamic_accounts, $opening_final_arr, $opening_total_sum] = $this->balanceQueries($this->openingBalanceDateBetween($dateBetween[0]));
        $openingBalance = $opening_total_sum + $opening_sale_cash + $opening_return_purchase_cash - $opening_salary_cash - $opening_purchase_cash - $opening_return_sale_cash;

        return response()->json([
            'sale_cash' => $sale_cash,
            'final_account' => $final_arr,
            'salary_cash' => $salary_cash,
            'purchase_cash' => $purchase_cash,
            'dynamic_account' => $dynamic_accounts,
            'return_sale_cash' => $return_sale_cash,
            'return_purchase_cash' => $return_purchase_cash,
            //            'income_cash'          => $income_cash,
            //            'expense_cash'         => $expense_cash,
            'date_between' => [
                $dateBetween[0]->format(request()->input('user_date_format_php').' H:i'),
                $dateBetween[1]->format(request()->input('user_date_format_php').' H:i'),
            ],
            'total_sum' => $total_sum + $sale_cash + $return_purchase_cash - $salary_cash - $purchase_cash - $return_sale_cash + $openingBalance,
            'opening_balance' => $openingBalance,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function openingBalanceDateBetween($date): array
    {
        return [
            Carbon::parse(request()->input('company_created_at')),
            Carbon::parse($date)->subSecond(),
        ];
    }

    public function add($name, $in, $out): float
    {
        return $in - $out;
    }

    protected function balanceQueries(array $dateBetween): array
    {
        $sale_cash = Payment::fromCompany()->saleOnly()->cash()->whereBetween('date', $dateBetween)->sum('paid');
        $purchase_cash = Payment::fromCompany()->purchaseOnly()->cash()->whereBetween('date', $dateBetween)->sum('paid');
        $return_sale_cash = Returns::fromCompany()->saleOnly()->whereBetween('created_at', $dateBetween)->sum('amount');
        $return_purchase_cash = Returns::fromCompany()->purchaseOnly()->whereBetween('created_at', $dateBetween)->sum('amount');
        $salary_cash = Salary::fromCompany()->cash()->whereBetween('salary_date', $dateBetween)->sum('total');

        $accountsParent = Account::with('children')->fromCompany()->where('parent_id', 0)->get();
        $dynamic_accounts = [];
        //        dd($dateBetween);
        foreach ($accountsParent as $item) {
            if (count($item->children)) {
                $children = $item->children;
                foreach ($children as $child) {
                    $transactions_out = Transaction::fromCompany()->cash()->where('account_id', $child->id)
                        ->typeCredit()
                        ->whereNotNull('ref_no')
                        ->whereNotNull('userable_model')
                        ->whereBetween('date', $dateBetween)
                        ->whereNotNull('userable_id')->sum('amount');
                    $transactions_in = Transaction::fromCompany()->cash()->where('account_id', $child->id)
                        ->typeDebit()
                        ->whereNotNull('ref_no')
                        ->whereNotNull('userable_model')
                        ->whereBetween('date', $dateBetween)
                        ->whereNotNull('userable_id')->sum('amount');
                    $dynamic_accounts[] = [
                        'name' => $child->name,
                        'parent' => $item->name,
                        'amount' => $this->add($item->name, $transactions_in, $transactions_out),
                    ];
                }
            }
        }
        $final_arr = [];
        $total_sum = 0;
        if (count($dynamic_accounts)) {
            foreach ($accountsParent as $item) {
                $sum = 0;
                foreach ($dynamic_accounts as $account) {
                    if ($account['parent'] === $item->name) {
                        $sum = $sum + $account['amount'];
                    }
                }
                $final_arr[$item->name] = $sum;
                $total_sum = $total_sum + $sum;
            }
        }

        return [$sale_cash, $purchase_cash, $return_sale_cash, $return_purchase_cash, $salary_cash, $dynamic_accounts, $final_arr, $total_sum];
    }
}
