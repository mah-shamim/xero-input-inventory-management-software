<?php

namespace App\Models\Inventory\Traits;

use Illuminate\Support\Facades\DB;

trait HomeReport
{
    public function homeReport(): array
    {
        $purchase = DB::select('SELECT date(purchase_date) AS purchase_date, SUM(TOTAL) AS total FROM purchases
WHERE purchase_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 1 DAY AND company_id = :companyId
GROUP BY date(purchase_date);', ['companyId' => request()->company_id]);


        $sales = DB::select('SELECT date(sales_date) AS sales_date, SUM(TOTAL) AS total FROM sales 
WHERE sales_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 1 DAY AND company_id = :companyId
GROUP BY date(sales_date);', ['companyId' => request()->company_id]);

        $expenses = DB::select('SELECT date(expense_date) AS expense_date, SUM(AMOUNT) AS amount FROM expenses 
WHERE expense_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 1 DAY AND company_id = :companyId
GROUP BY date(expense_date);', ['companyId' => request()->company_id]);

        $best_products = DB::select('SELECT SUM(ps.price) AS total, p.name AS  product_name FROM product_sale ps 
LEFT JOIN products p ON p.id = ps.product_id WHERE p.company_id = :companyId
GROUP BY ps.product_id LIMIT 10;', ['companyId' => request()->company_id]);


        $best_quantities = DB::select('SELECT SUM(ps.quantity) AS quantity, p.name AS  product_name FROM product_sale ps 
LEFT JOIN products p ON p.id = ps.product_id WHERE p.company_id = :companyId
GROUP BY ps.product_id LIMIT 10;', ['companyId' => request()->company_id]);

        $purchase = collect($purchase);
        $sales = collect($sales);
        $expenses = collect($expenses);
        $products = collect($best_products);
        $best_quantities = collect($best_quantities);

        list($purchase_date, $purchaseTotal) = $this->mappingData($purchase, 'purchase_date');
        list($sales_date, $salesTotal) = $this->mappingData($sales, 'sales_date');
        list($expenses_date, $expensesTotal) = $this->mappingData($expenses, 'expense_date', 'amount');
        list($product_name, $product_total) = $this->mappingData($products, 'product_name');
        list($product_name_by_quantity, $product_quantity_total) = $this->mappingData($best_quantities, 'product_name', 'quantity');


        return [
            'purchases' => [
                'purchase_dates' => $purchase_date,
                'purchase_totals' => $purchaseTotal
            ],
            'sales' => [
                'sale_dates' => $sales_date,
                'sale_totals' => $salesTotal
            ],
            'expenses' => [
                'expense_dates' => $expenses_date,
                'expense_totals' => $expensesTotal
            ],
            'products' => [
                'product_name' => $product_name,
                'product_total' => $product_total
            ],
            'product_quantity' => [
                'product_quantity_name' => $product_name_by_quantity,
                'product_quantity_total' => $product_quantity_total
            ]
        ];
    }


}
