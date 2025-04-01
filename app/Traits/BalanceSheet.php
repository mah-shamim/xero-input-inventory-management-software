<?php

namespace App\Traits;

use App\Models\Bank\Bank;
use App\Models\Bank\Transaction;
use App\Models\Expense\Expense;
use App\Models\Income\Income;
use App\Models\Inventory\BuildEvent;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;
use App\Models\Inventory\Warehouse;
use App\Models\Payroll\Salary;

trait BalanceSheet
{
    public function balanceSheet()
    {
        //        $dateBetween   = dateLowToHighWithTime(request()->input('date'));
        $banks = Bank::fromCompany()->pluck('id');
        $bank_balances = $this->transaction_builder($banks, $dateBetween = null);
        [$total_purchase, $total_purchase_payment] = $this->purchaseTotal_purchasePaid();
        [$total_sales, $total_sale_payment] = $this->saleTotal_salePaid();
        $total_income = Income::fromCompany()->sum('amount');
        $total_income_payment = Payment::fromCompany()->incomeOnly()->sum('paid');
        $total_expense = Expense::fromCompany()->sum('amount');
        $total_expense_payment = Payment::fromCompany()->expenseOnly()->sum('paid');
        $total_salaries = Salary::fromCompany()->sum('total');
        $total_income_receivable = $total_income - $total_income_payment;
        $total_expense_payable = $total_expense - $total_expense_payment;
        $total_sale_receivable = $total_sales - $total_sale_payment;
        $total_purchase_payable = $total_purchase - $total_purchase_payment;
        $build_query = BuildEvent::with('product')->fromCompany()->select();
        $bom_products = $build_query->get()->toArray();
        //        @important: these are important lines Please dont delete them
        //        dd($bom_products);

        //        $bom_products = collect($bom_products)->map(function ($bm) {
        //            return $bm['product'];
        //        })->unique()->toArray();

        //        dd($bom_products);

        //        $total_expense_bom = $build_query->sum('expense_total');
        $total_expense_bom = 0;

        $purchases = Purchase::with('products.units')->fromCompany()->get();
        $sales = Sale::with('products')->fromCompany()->get();

        $cash_in_hand = 0;
        $cash_in_bank = 0;
        foreach ($bank_balances as $bank_balance) {
            if ($bank_balance->bank->type === 'cr') {
                $cash_in_hand += $bank_balance->running_balance;
            } else {
                $cash_in_bank += $bank_balance->running_balance;
            }
        }

        $accounts = $this->account_transaction_all();
        $accounts_name = $this->sum_of_account($accounts);

        $account_receivable = $total_income_receivable + $total_sale_receivable;
        $account_payable = $total_expense_payable + $total_purchase_payable;
        $account_income = 0;
        $account_expense = 0;
        $account_asset = 0;
        $account_equity = 0;
        $account_liability = 0;

        if (count($accounts_name)) {
            $account_income = array_key_exists('Income', $accounts_name) ? $accounts_name['Income'] : 0;
            $account_expense = array_key_exists('Expense', $accounts_name) ? -$accounts_name['Expense'] : 0;
            $account_asset = array_key_exists('Asset', $accounts_name) ? -$accounts_name['Asset'] : 0;
            $account_equity = array_key_exists('Equity', $accounts_name) ? $accounts_name['Equity'] : 0;
            $account_liability = array_key_exists('Liability', $accounts_name) ? $accounts_name['Liability'] : 0;
        }

        $products_arr = $this->productCollectionSalePurchase($sales, $purchases);
        //        dd($products_arr);
        $total_product_profit = $this->findProfit($products_arr);

        $bom_merchandise_inventory = 0;

        $merchandise_inventory_products_quantity = $this->findMerchandise_inventory_quantity();
        $merchandise_inventory = $this->findMerchandise_inventory_total($merchandise_inventory_products_quantity, $products_arr);
        $bom_merchandise_inventory = $this->findMerchandise_inventory_total_bom($merchandise_inventory_products_quantity, $bom_products);
        //        dd($merchandise_inventory, $bom_merchandise_inventory, $bom_products);

        //        dd($total_income, $account_income, $total_income_payment);

        $owner_investment = $account_equity;
        $actual_total_expense = $total_expense + $account_expense - $total_expense_payment + $total_expense_bom;
        $actual_total_income = $total_income + $account_income - $total_income_payment;
        $actual_account_payable = $account_payable + $account_liability;
        $profit = $actual_total_income - $actual_total_expense - $total_salaries + $total_product_profit;
        $total_current_asset = $cash_in_hand + $cash_in_bank + $account_receivable + $merchandise_inventory;
        $merchandise_inventory = $merchandise_inventory + $bom_merchandise_inventory;

        return response()->json([
            'cash_in_hand' => round($cash_in_hand, 2),
            'cash_in_bank' => round($cash_in_bank, 2),
            'account_receivable' => round($account_receivable, 2),
            'account_payable' => round($actual_account_payable, 2),
            'merchandise_inventory' => round($merchandise_inventory, 2),
            'total_current_asset' => round($total_current_asset, 2),
            'accounts' => $accounts,
            'profit' => round($profit, 2),

            'fixed_asset' => round($account_asset, 2),
            'total_assets' => round($cash_in_hand + $cash_in_bank + $account_receivable + $merchandise_inventory + $account_asset, 2),
            'owner_investment' => round($owner_investment, 2),
            'total_owner_equity' => round($profit + $owner_investment, 2),
            'total_liabilities' => round($account_payable + $account_liability, 2),
            'accounts_sum' => $accounts_name,
        ]);
    }

    public function findQuantityNumber($str): int|float
    {
        $val = explode(' ', $str);
        if (count($val)) {
            return floatval($val[0]);
        }

        return 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function transaction_builder($banks, $date_between): array|\Illuminate\Database\Eloquent\Collection
    {
        return Transaction::with('bank')->whereIn('bank_id', $banks)
            ->select('transactions.*')
            ->selectRaw('
            sum(coalesce(case when type=\'debit\' then amount end, 0)) as total_debits,
            sum(coalesce(case when type=\'credit\' then amount end, 0)) as total_credits,
            SUM(COALESCE(CASE WHEN type = \'debit\' THEN amount END,0)) - SUM(COALESCE(CASE WHEN type = \'credit\' THEN amount END,0)) running_balance
            ')
            ->where('company_id', request()->input('company_id'))
            ->groupBy('transactions.bank_id')
            ->havingRaw('running_balance<>0')
            ->get();
    }

    public function findMerchandise_inventory_quantity(): array
    {
        //return unique product id with quantity
        $products = [];
        $warehouses = Warehouse::with('products')->fromCompany()->get();
        foreach ($warehouses as $warehouse) {
            foreach ($warehouse->products as $product) {
                $quantity = $this->findActualQuantity($product);
                if (count($products) && array_key_exists($product->id, $products)) {
                    $products[$product->id] += $quantity;
                } else {
                    $products[$product->id] = $quantity;
                }

            }
        }

        return $products;
    }

    public function findMerchandise_inventory_total(array $merchandise_inventory_products_quantity, array $products_arr): float|int
    {
        $merchandise_inventory = 0;
        foreach ($merchandise_inventory_products_quantity as $warehouse_product_id => $warehouse_quantity_remain) {
            foreach ($products_arr as $purchased_product_id => $purchased_product) {
                if ($purchased_product_id === $warehouse_product_id) {
                    $merchandise_inventory += $purchased_product['purchase']['average_price'] * $warehouse_quantity_remain;
                }
            }
        }

        return $merchandise_inventory;
    }

    public function findMerchandise_inventory_total_bom(array $merchandise_inventory_products_quantity, array $products_arr): float|int
    {
        //        dd($products_arr);
        $merchandise_inventory = 0;
        foreach ($merchandise_inventory_products_quantity as $warehouse_product_id => $warehouse_quantity_remain) {
            foreach ($products_arr as $product) {
                if ($product['product_id'] === $warehouse_product_id) {
                    $merchandise_inventory += $product['total_cost'] * $warehouse_quantity_remain;
                }
            }
        }

        return $merchandise_inventory;

    }
}
