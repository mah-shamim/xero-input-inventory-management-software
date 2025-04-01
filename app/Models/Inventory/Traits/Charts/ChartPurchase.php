<?php


namespace App\Models\Inventory\Traits\Charts;


use Illuminate\Support\Facades\DB;

trait ChartPurchase
{
    public function single_purchase_chart_by_date()
    {
        $purchaseByProductChart = DB::select('SELECT date(purchase_date) AS purchase_date, SUM(TOTAL) AS total FROM purchases
WHERE purchase_date BETWEEN CURDATE() - INTERVAL 3600 DAY AND CURDATE() + INTERVAL 1 DAY AND company_id = :companyId
GROUP BY date(purchase_date) ORDER BY MAX(total) DESC LIMIT 10;', ['companyId' => request()->company_id]);

        list($purchase_date, $purchaseTotal) = $this->mappingData(collect($purchaseByProductChart), 'purchase_date');
        $purchaseByProductChart = [
            'purchase_date' => $purchase_date,
            'purchase_totals' => $purchaseTotal
        ];

        return response()->json($purchaseByProductChart);
    }

    public function best_top_product_purchases()
    {
        $best_products = DB::select('SELECT SUM(ps.subtotal) AS total, p.name AS  product_name FROM product_purchase ps 
LEFT JOIN products p ON p.id = ps.product_id WHERE p.company_id = :companyId
GROUP BY ps.product_id LIMIT 10;', ['companyId' => request()->company_id]);
        list($product_name, $purchaseTotal) = $this->mappingData(collect($best_products), 'product_name');
        $purchaseByProductChart = [
            'product_name' => $product_name,
            'purchase_totals' => $purchaseTotal
        ];
        return response()->json($purchaseByProductChart);
    }


}
