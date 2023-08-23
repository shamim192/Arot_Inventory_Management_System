<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('/', function () { echo 'worked!'; });
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => ['auth:sanctum']], function() {
        Route::get('me', 'AuthController@me');
        Route::post('logout', 'AuthController@logout');

        Route::get('setting', 'MainController@setting');
        Route::post('setting', 'MainController@settingUpdate');
        Route::get('customer', 'MainController@customer');
        Route::get('supplier', 'MainController@supplier');
        Route::get('unit', 'MainController@unit');
        Route::get('product', 'MainController@product');
        Route::get('warehouse', 'MainController@warehouse');
        Route::get('warehouse-stock-list', 'MainController@warehouseStockList');
        Route::get('warehouse-receivable', 'MainController@warehouseReceivable');
        Route::get('warehouse-issuable', 'MainController@warehouseIssuable');
        Route::post('warehouse-receive-store', 'MainController@warehouseReceiveStore');
        Route::post('warehouse-issue-store', 'MainController@warehouseIssueStore');
        Route::get('warehouse-receive', 'MainController@warehouseReceive');
        Route::get('warehouse-issue', 'MainController@warehouseIssue');
    });
});
