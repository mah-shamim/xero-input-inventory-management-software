<?php
/**
 * Created by PhpStorm.
 * User: Sharif
 * Date: 9/17/2019
 * Time: 9:41 AM
 */

use App\Http\Controllers\Accounts\AccountController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Bank\BankController;
use App\Http\Controllers\Bank\TransactionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\CustomerController;
use App\Http\Controllers\Inventory\PaymentCrudController;
use App\Http\Controllers\Inventory\PaymentsController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\ProductDamageController;
use App\Http\Controllers\Inventory\ProductUnitController;
use App\Http\Controllers\Inventory\PurchaseController;
use App\Http\Controllers\Inventory\PurchaseReturnController;
use App\Http\Controllers\Inventory\ReportController;
use App\Http\Controllers\Inventory\SalesController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\UnitController;
use App\Http\Controllers\Inventory\UnitConversionController;
use App\Http\Controllers\Inventory\WarehouseController;
use App\Http\Controllers\Inventory\WarrantyController;
use App\Http\Controllers\Payroll\DepartmentController;
use App\Http\Controllers\Payroll\EmployeeController;
use App\Http\Controllers\Payroll\SalaryController;
use App\Http\Controllers\SettingsController;
use Laravel\Dusk\Http\Controllers\UserController;

Route::group(['middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {
    Route::get('supplier/ledger/{id}', [SupplierController::class, 'supplierReport']);
    Route::get('activity-log', [ActivityLogController::class, 'index']);
    Route::get('get-authable-user', [UserController::class, 'get_user']);
    Route::get('get-bank', [BankController::class, 'list']);
    Route::get('get-warehouse', [WarehouseController::class, 'getWarehouseList']);
    Route::get('get-customer', [CustomerController::class, 'customer_query']);
    Route::get('get_any_user_by_dynamic_column_and_model', [Controller::class, 'get_any_user_by_dynamic_column_and_model']);
    Route::get('get-any-user', [Controller::class, 'get_any_user']);
    Route::get('get-any-user/{id}', [Controller::class, 'show_any_user']);

    Route::get('bank/reconcile', [BankController::class, 'reconcile']);
    Route::post('bank-transfer', [TransactionController::class, 'transfer']);
    Route::get('bank-total', [BankController::class, 'total']);
    Route::resource('bank', BankController::class);
    Route::resource('transaction', TransactionController::class);
});


Route::group(['prefix' => 'inventory', 'middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {

    Route::get('product-unit/getProductUnits', [ProductUnitController::class, 'getProductUnits']);
    Route::get('products/getProducts', [ProductController::class, 'getProducts'])->name('product.query');
    Route::get('get-suppliers', [SupplierController::class, 'get_supplier']);

    Route::post('unitconversions/{fromUnitId}/{toInitId}/{quantity}', [UnitConversionController::class, 'convert']);
    Route::resource('productunit', ProductUnitController::class);

    Route::post('products/getProducts', [ProductController::class, 'getProducts']);
    Route::post('units/getUnits', [UnitController::class, 'getUnits']);
    Route::get('customer/get-due/{id}', [CustomerController::class, 'getCustomerDue']);


    Route::resource('settings', SettingsController::class, [
        'only' => ['index', 'store'],
    ]);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('products', ProductController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('unitconversions', UnitConversionController::class);
    Route::resource('productunit', ProductUnitController::class);
    Route::resource('warranty', WarrantyController::class);
    Route::resource('productdamages', ProductDamageController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SalesController::class);

    Route::resource('purchase-return', PurchaseReturnController::class, [
        'only' => ['show', 'store', 'update'],
    ]);
});

Route::group(['prefix' => 'payments', 'middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {
    Route::resource('crud', PaymentCrudController::class, ['as' => 'pay']);
    Route::get('received', [PaymentsController::class, 'received']);
    Route::get('received/{id}', [PaymentsController::class, 'received_show']);
    Route::get('bill-paid', [PaymentsController::class, 'bill_paid']);
    Route::get('income-payment-received', [PaymentsController::class, 'income_payment_received']);
    Route::get('expense-bill-paid', [PaymentsController::class, 'expense_bill_paid']);
    Route::get('bill-paid/{id}', [PaymentsController::class, 'bill_paid_show']);
    Route::resource('model.model_id.payment_id', PaymentsController::class);
});

Route::group(['prefix' => 'payroll', 'middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {
    Route::get('salary-total', [SalaryController::class, 'total_sum']);
    Route::resource('employee', EmployeeController::class);
    Route::resource('salary', SalaryController::class);
    Route::resource('department', DepartmentController::class);
});

Route::group(['prefix' => 'report', 'middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {
    Route::get('purchases', [ReportController::class, 'purchasesReport']);
    Route::get('sales', [ReportController::class, 'salesReport']);
    Route::get('expenses', [ReportController::class, 'expenseReport']);
    Route::get('customers', [ReportController::class, 'customerReport']);
    Route::get('suppliers', [ReportController::class, 'supplierReport']);
    Route::get('products', [ReportController::class, 'productReport']);
    Route::get('warehouses/show', [ReportController::class, 'showWarehouseReport']);
    Route::get('warehouses', [ReportController::class, 'warehouseReport']);
    Route::get('expenses', [ReportController::class, 'expenseReport']);
    Route::get('overall', [ReportController::class, 'overallReport']);
    Route::get('home', [ReportController::class, 'homeReport']);
});
Route::group(['middleware'=>\App\Providers\RouteServiceProvider::roleMethod()], function(){
    Route::resource('expenses', ExpenseController::class);
});

Route::group(['prefix' => 'accounts', 'middleware' => \App\Providers\RouteServiceProvider::roleMethod()], function () {
    Route::get('get-accounts-by-parent/{parent_name}', [AccountController::class, 'get_accounts_by_parent']);
});


//Route::resource('settings', 'SettingsController',[
//    'only'=>['index', 'store']
//])->middleware(['company']);
//


