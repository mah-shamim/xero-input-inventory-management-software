<?php


namespace App\Models\Inventory\Traits\Charts;


trait Charts
{
    use ChartPurchase, ChartSale, ChartCustomer, ChartSupplier, ChartProduct;
}
