<?php

Route::group(['prefix' => 'report', 'middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {
    Route::get('home', 'Inventory\ReportController@homeReport');
    Route::get('purchases', 'Inventory\ReportController@purchasesReport');
    Route::get('actualpurchases', 'Inventory\ReportController@actualPurchasesReport');
//    Route::get('suppliers', 'Inventory\ReportController@supplierReport');
    Route::get('sales', 'Inventory\ReportController@salesReport');
    Route::get('products', 'Inventory\ReportController@productReport');
    Route::get('warehouses/show', 'Inventory\ReportController@showWarehouseReport');
    Route::get('warehouses', 'Inventory\ReportController@warehouseReport');
    Route::get('expenses', 'Inventory\ReportController@expenseReport');
    Route::get('income', 'Inventory\ReportController@incomeReport');
    Route::get('overall', 'Inventory\ReportController@overallReport');
    Route::get('home', 'Inventory\ReportController@homeReport');
    Route::get('customers', 'Inventory\ReportController@customerReport');
    Route::get('suppliers', 'Inventory\ReportController@supplierReport');

    //charts
    Route::get('single-purchase-chart-by-date', 'Inventory\ReportController@single_purchase_chart_by_date');
    Route::get('best-top-product-purchases', 'Inventory\ReportController@best_top_product_purchases');
    Route::get('single-sale-chart-by-date', 'Inventory\ReportController@single_sale_chart_by_date');
    Route::get('best-top-product-sales', 'Inventory\ReportController@best_top_product_sales');
    Route::get('top-customer-by-total-purchase', 'Inventory\ReportController@top_customer_by_total_purchase');
    Route::get('top-customer-by-sale-count', 'Inventory\ReportController@top_customer_by_sale_count');
    Route::get('top-supplier-by-total-purchase', 'Inventory\ReportController@top_supplier_by_total_purchase');
    Route::get('top-supplier-by-purchase-count', 'Inventory\ReportController@top_supplier_by_purchase_count');
    Route::get('top-product-by-stock-profit','Inventory\ReportController@top_product_by_stock_profit');
    Route::get('top-product-by-sale-count-profit','Inventory\ReportController@top_product_by_sale_count_profit');
});
