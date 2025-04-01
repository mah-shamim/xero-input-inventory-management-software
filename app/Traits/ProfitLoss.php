<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\Expense\Expense;
use App\Models\Income\Income;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Returns;
use App\Models\Inventory\Sale;
use App\Models\Payroll\Salary;

trait ProfitLoss
{
    public function profitLoss()
    {

        $purchases = Purchase::with('products.units')->fromCompany()->get();
        $sales = Sale::with('products.units')->fromCompany()->get();
        $sale_returns = Returns::fromCompany()->saleOnly()->sum('amount');
        $products_arr = $this->productCollectionSalePurchase($sales, $purchases);
        $total_product_profit = $this->findProfit($products_arr);
        $salaries = Salary::fromCompany()->sum('total');
        $total_expense = Expense::fromCompany()->sumOfAccount()->get();
        $total_income = Income::fromCompany()->sumOfAccount()->get();
        $payments_expense = Payment::fromCompany()->expenseOnly()->pluck('transaction_id')->toArray();
        $payments_income = Payment::fromCompany()->incomeOnly()->pluck('transaction_id')->toArray();
        $account_transactions = $this->account_transaction_query()->whereNotIn('transactions.id', array_merge($payments_expense, $payments_income))->get();
        //        return $account_transactions;
        foreach ($account_transactions as $transaction) {
            if ($transaction['parent_name'] === 'Expense') {
                $transaction->total_amount = -$transaction->total_debit + $transaction->total_credit;
            } else {
                $transaction->total_amount = $transaction->total_debit - $transaction->total_credit;
            }
        }
        //        return $account_transactions;
        $account_transactions = $this->account_transactions_collection($account_transactions);

        $expense_accounts = [];
        $income_accounts = [];

        $expense_names = Account::fromCompany()->onlyExpense()->pluck('name');
        $income_names = Account::fromCompany()->onlyIncome()->pluck('name');

        $summation = [
            'expense' => 0,
            'income' => 0,
            'total_income' => 0,
            'gross_profit' => 0,
            'net_profit' => 0,
            'total_expense' => 0,
        ];

        $expense_accounts = [];
        $income_accounts = [];

        foreach ($expense_names as $expense_name) {
            $total_amount = 0;
            if (array_key_exists('Expense', $account_transactions->toArray())) {
                foreach ($account_transactions['Expense'] as $transaction) {
                    if ($expense_name === $transaction['account_name']) {
                        $total_amount += $transaction['total_amount'];
                        $expense_accounts[$expense_name] = $total_amount;
                    }
                }
            }
            foreach ($total_expense as $item) {
                if ($expense_name === $item['account_name']) {
                    $total_amount += $item['total_amount'];
                    $expense_accounts[$expense_name] = $total_amount;
                }
            }
        }

        foreach ($income_names as $income_name) {
            $total_amount = 0;
            if (array_key_exists('Income', $account_transactions->toArray())) {
                foreach ($account_transactions['Income'] as $transaction) {
                    if ($income_name === $transaction['account_name']) {
                        $total_amount += $transaction['total_amount'];
                        $income_accounts[$income_name] = $total_amount;
                    }
                }
            }
            foreach ($total_income as $item) {
                if ($income_name === $item['account_name']) {
                    $total_amount += $item['total_amount'];
                    $income_accounts[$income_name] = $total_amount;
                }
            }
        }

        //        dd($income_accounts, $expense_accounts);
        foreach ($income_accounts as $account) {
            $summation['income'] += $account;
        }
        foreach ($expense_accounts as $account) {
            $summation['expense'] += $account;
        }

        $summation['total_income'] = round($summation['income'] + $total_product_profit, 2);
        $summation['gross_profit'] = round($summation['total_income'] - $sale_returns, 2);
        $summation['total_expense'] = round($summation['expense'] + $salaries, 2);
        $summation['net_profit'] = round($summation['gross_profit'] - $summation['total_expense'], 2);
        //        dd($expense_accounts, $total_expense);

        return response()->json([
            //            'accounts'         => $account_transactions,
            'expense_accounts' => $expense_accounts,
            'income_accounts' => $income_accounts,
            'sale_profit' => round($total_product_profit, 2),
            //            'total_expense'    => $total_expense,
            //            'total_income'     => $total_income,
            'sale_returns' => $sale_returns,
            'salaries' => $salaries,
            'summation' => $summation,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
