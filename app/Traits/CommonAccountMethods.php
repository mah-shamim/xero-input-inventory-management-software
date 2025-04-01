<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\Bank\Transaction;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;
use Illuminate\Support\Facades\DB;

/**
 * Trait CommonAccountMethods
 */
trait CommonAccountMethods
{
    protected function findActualQuantity($product): float
    {
        if ($product->base_unit_id !== $product->pivot->unit_id) {
            $converted_quantity = $this->renderJsonOutput(
                $this->unitConversionConvertService,
                request(),
                [
                    'from_unit_id' => $product->pivot->unit_id,
                    'to_unit_id' => $product->base_unit_id,
                    'quantity' => $product->pivot->quantity,
                ]
            );
            $actual_quantity = floatval(json_decode($converted_quantity, true)['quantity']);
        } else {
            $actual_quantity = $product->pivot->quantity;
        }

        return $actual_quantity;
    }

    public function findProfit($products): int|float
    {
        $total_profit = 0.0;
        if (count($products)) {
            foreach ($products as $product) {
                $purchase_average_price = array_key_exists('purchase', $product) ? $product['purchase']['average_price'] : 0;
                $sale_amount = array_key_exists('sale', $product) ? $product['sale']['total_amount'] : 0;
                $sale_quantity = array_key_exists('sale', $product) ? $product['sale']['actual_quantity'] : 0;
                $total_cost_of_product = $sale_quantity * $purchase_average_price;
                $profit = $sale_amount - $total_cost_of_product;
                $product['profit'] = $profit;
                $total_profit += $profit;
            }
        }

        return $total_profit;
    }

    public function productCollectionSalePurchase($sales, $purchases): array
    {
        $products = [];
        foreach ([$sales, $purchases] as $items) {
            $modelType = ($items === $sales) ? 'sale' : 'purchase';

            foreach ($items as $item) {
                $totalWithoutDiscount = 0;
                $totalQuantity = 0;

                foreach ($item->products as $product) {
                    $totalWithoutDiscount += $product->pivot->subtotal;
                    $quantity = $this->findActualQuantity($product);
                    $product->actual_quantity = $quantity;
                    $totalQuantity += $quantity;
                }

                $discountedAmount = $item->shipping_cost - (($totalWithoutDiscount + $item->shipping_cost) * ($item->overall_discount * .01));

                foreach ($item->products as $product) {
                    $product->actual_subtotal = $product->pivot->subtotal + ($discountedAmount / $totalQuantity) * $product->actual_quantity;

                    if (array_key_exists($product->id, $products) && array_key_exists($modelType, $products[$product->id])) {
                        $qty = $products[$product->id][$modelType]['actual_quantity'] + $product->actual_quantity;
                        $subtotal = $products[$product->id][$modelType]['total_amount'] + $product->actual_subtotal;
                        $averagePrice = $subtotal / $qty;

                        $products[$product->id][$modelType] = [
                            'actual_quantity' => $qty,
                            'total_amount' => $subtotal,
                            'average_price' => $averagePrice,
                        ];
                    } else {
                        $products[$product->id][$modelType] = [
                            'actual_quantity' => $product->actual_quantity,
                            'total_amount' => $product->actual_subtotal,
                            'average_price' => $product->actual_subtotal / $product->actual_quantity,
                        ];
                    }
                }
            }
        }

        return $products;
    }

    public function account_transaction_all(): \Illuminate\Support\Collection
    {
        $transactions = $this->account_transaction_query()->get();

        return $this->account_transactions_collection($transactions);
    }

    /**
     * @param  string  $group_by
     */
    public function account_transactions_collection($transactions, $group_by = 'parent_name'): \Illuminate\Support\Collection
    {

        $transactions = collect($transactions);
        $transactions = $transactions->groupBy($group_by);

        return $transactions;
    }

    public function account_transaction_query()
    {
        $accounts = Account::accountByGroupText()
            ->select(
                'accounts.*',
                'a.id as parent_id',
                'a.name as group'
            )
            ->groupBy('accounts.id')
            ->orderBy('accounts.parent_id')
            ->pluck('accounts.id');

        /**TODO total_debit and total_credit should be subtracted in sql query**/
        $transactions = Transaction::fromCompany()
            ->whereIn('account_id', $accounts)
            ->join('accounts as child', 'child.id', '=', 'transactions.account_id')
            ->join('accounts as parent', 'parent.id', '=', 'child.parent_id')
            ->select('transactions.*',
                'child.name as account_name',
                'parent.name as parent_name',
                DB::raw("SUM(IF(transactions.type='debit', amount, 0)) as total_debit"),
                DB::raw("SUM(IF(transactions.type='credit', amount, 0)) as total_credit")
            )
            ->groupBy('account_id');

        return $transactions;

    }

    /**
     * @return mixed
     */
    public function sum_of_account($accounts)
    {
        $accounts_name = $accounts->mapWithKeys(function ($items, $key) {
            return [$key => $items->sum('total_debit') - $items->sum('total_credit')];
        })->toArray();

        return $accounts_name;
    }

    public function purchaseTotal_purchasePaid(): array
    {
        $purchaseTotal = Purchase::fromCompany()->sum('total');
        $purchasePaid = Payment::fromCompany()->purchaseOnly()->sum('paid');

        return [$purchaseTotal, $purchasePaid];
    }

    public function saleTotal_salePaid(): array
    {
        $saleTotal = Sale::fromCompany()->sum('total');

        $salePaid = Payment::fromCompany()->saleOnly()->sum('paid');

        return [$saleTotal, $salePaid];
    }
}
