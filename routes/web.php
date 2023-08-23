<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/shop-closed', function () {
    return view('shop-closed');
});

Route::get('/','App\Http\Controllers\Auth\LoginController@showLoginForm')->name('logout');

Auth::routes(['verify' => true, 'register' =>false]);
//
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth', 'verified', 'open']], function () {    
    Route::get('dashboard','DashboardController@index')->name('dashboard');

    Route::get('profile','ProfileController@index')->name('profile');
    Route::post('profile', 'ProfileController@update');
    Route::get('profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/password', 'ProfileController@passwordUpdate');

    Route::resource('user', 'UserController')->middleware('isAdmin');
    Route::resource('customer', 'CustomerController');
    Route::resource('supplier', 'SupplierController');
    
    Route::resource('unit', 'UnitController');
    Route::resource('bank', 'BankController');
    Route::resource('product', 'ProductController');
    // Route::resource('warehouse', 'WarehouseController');

    Route::resource('invest', 'InvestController');
    Route::resource('fund-transfer', 'FundTransferController');

    Route::resource('income', 'Income\IncomeController');
    Route::resource('income-category', 'Income\IncomeCategoryController');
    
    Route::resource('expense', 'Expense\ExpenseController');
    Route::resource('expense-category', 'Expense\CategoryController');

    Route::get('stock/print/{id}', 'Stock\StockController@prints')->name('stock.print');
    Route::resource('stock', 'Stock\StockController');

    Route::post('supplier-wise-stock-ajax', 'Stock\StockReturnController@supplierWiseStock')->name('supplier-wise-stock-ajax');
    Route::post('stock-item-ajax', 'Stock\StockReturnController@stockItem')->name('stock-item-ajax');
    Route::get('stock-return/print/{id}', 'Stock\StockReturnController@prints')->name('stock-return.print');
    Route::resource('stock-return', 'Stock\StockReturnController');
    
    Route::get('supplier-payment/print/{id}', 'Stock\SupplierPaymentController@prints')->name('supplier-payment.print');
    Route::resource('supplier-payment', 'Stock\SupplierPaymentController');

    Route::get('sale/print/{id}', 'Sale\SaleController@prints')->name('sale.print');
    Route::resource('sale', 'Sale\SaleController');

    Route::post('customer-wise-sale-ajax', 'Sale\SaleReturnController@customerWiseSale')->name('customer-wise-sale-ajax');
    Route::post('sale-item-ajax', 'Sale\SaleReturnController@saleItem')->name('sale-item-ajax');
    Route::get('sale-return/print/{id}', 'Sale\SaleReturnController@prints')->name('sale-return.print');
    Route::resource('sale-return', 'Sale\SaleReturnController');
    
    Route::get('customer-payment/print/{id}', 'Sale\CustomerPaymentController@prints')->name('customer-payment.print');
    Route::resource('customer-payment', 'Sale\CustomerPaymentController');

    
    // Route::resource('warehouse-transfer', 'Warehouse\TransferController');

    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {   
        Route::get('product-stock','Report\ProductLedgerController@stock')->name('product-stock');
        Route::get('product-ledger','Report\ProductLedgerController@ledger')->name('product-ledger');
        
        // Route::get('warehouse-stock','Report\ProductLedgerController@warehouseStock')->name('warehouse-stock');
        // Route::get('warehouse-ledger','Report\ProductLedgerController@warehouseLedger')->name('warehouse-ledger');
        // Route::get('warehouse-ledger/{type}/{id}','Report\ProductLedgerController@warehouseLedgerDetails')->name('warehouse-ledger-details');

        Route::get('bank','Report\CommonController@bank')->name('bank');
        Route::get('bank-transactions','Report\CommonController@bankTransactions')->name('bank-transactions');

        Route::get('supplier','Report\CommonController@supplier')->name('supplier');
        Route::get('supplier-transactions','Report\CommonController@supplierTransactions')->name('supplier-transactions');

        Route::get('customer','Report\CommonController@customer')->name('customer');
        Route::get('customer-transactions','Report\CommonController@customerTransactions')->name('customer-transactions');

        Route::get('expense','Report\CommonController@expenseReport')->name('expense');
        Route::get('income','Report\CommonController@incomeReport')->name('income');

        Route::get('expense-transactions','Report\CommonController@expenseTransaction')->name('expense-transactions');
        Route::get('income-transactions','Report\CommonController@incomeTransaction')->name('income-transactions');
        
    });
});
