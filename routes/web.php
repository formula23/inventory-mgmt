<?php

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

Auth::routes();

Route::group(['middleware' => ['auth','web']], function () {

    Route::get('/', 'HomeController@index')->name('dashboard');

    Route::get('/search', 'HomeController@search')->name('search');

    Route::get('/home', function() { return redirect('/'); });
    Route::get('/logout', function() { return redirect('/'); });

//    Route::get('/users/{type?}', 'UsersController@index')->defaults('type','all')->name('users.list');

    Route::resource('users', 'UsersController');

    Route::get('/users/{user}/licenses/create', 'LicenseController@create')->name('users.licenses.create');
    Route::post('/users/{user}/licenses', 'LicenseController@store')->name('users.licenses.store');

    Route::get('/users/{user}/licenses/{license}/edit', 'LicenseController@edit')->name('users.licenses.edit');
    Route::put('/users/{user}/licenses/{license}', 'LicenseController@update')->name('users.licenses.update');

    Route::resource('customers', 'CustomersController');

    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::put('/profile/update', 'ProfileController@update')->name('profile.update');

    Route::get('/purchase-orders', 'PurchaseOrdersController@index')->name('purchase-orders.index');
    Route::get('/purchase-orders/reset-filters', 'PurchaseOrdersController@resetFilters')->name('purchase-orders.reset-filters');

    Route::get('/purchase-orders/create/{vendor?}', 'PurchaseOrdersController@create')->name('purchase-orders.create');
    Route::get('/purchase-orders/upload/{vendor?}', 'PurchaseOrdersController@create')->name('purchase-orders.upload');

    Route::post('/purchase-orders/process-upload', 'PurchaseOrdersController@processUpload')->name('purchase-orders.process-upload');

    Route::get('/purchase-orders/{purchase_order}', 'PurchaseOrdersController@show')->name('purchase-orders.show');
    Route::post('/purchase-orders/{purchase_order}', 'PurchaseOrdersController@show')->name('purchase-orders.show-post');


    Route::get('/purchase-orders/{purchase_order}/print', 'PurchaseOrdersController@printPO')->name('purchase-orders.print_po');
    Route::get('/purchase-orders/{purchase_order}/print-qr', 'PurchaseOrdersController@printQR')->name('purchase-orders.print-qr');
    Route::post('/purchase-orders', 'PurchaseOrdersController@store')->name('purchase-orders.store');
    Route::put('/purchase-orders/{purchase_order}/update', 'PurchaseOrdersController@update')->name('purchase-orders.update');
    Route::put('/purchase-orders/{purchase_order}/update-batch/{batch}', 'PurchaseOrdersController@updateBatch')->name('purchase-orders.update-batch');
    Route::post('/purchase-orders/{purchase_order}/payment', 'PurchaseOrdersController@payment')->name('purchase-orders.payment');

    Route::get('/purchase-orders/{purchase_order}/retag', 'PurchaseOrdersController@retag')->name('purchase-orders.retag');
    Route::post('/purchase-orders/{purchase_order}/retag', 'PurchaseOrdersController@retag')->name('purchase-orders.retag');

    Route::post('/purchase-orders/{purchase_order}/remove', 'PurchaseOrdersController@remove')->name('purchase-orders.remove');


    Route::get('/sale-orders', 'SaleOrdersController@index')->name('sale-orders.index');
    Route::get('/sale-orders/reset-filters', 'SaleOrdersController@resetFilters')->name('sale-orders.reset-filters');

    Route::get('/sale-orders/{sale_order}', 'SaleOrdersController@show')->name('sale-orders.show');

    Route::get('/sale-orders/{sale_order}/retag-uids', 'SaleOrdersController@retagUids')->name('sale-orders.retag-uids');
    Route::post('/sale-orders/{sale_order}/retag-uids-process', 'SaleOrdersController@retagUidsProcess')->name('sale-orders.retag-uids-process');
//    Route::get('/sale-orders/{sale_order}/retag-uids-summary', 'SaleOrdersController@retagUidsSummary')->name('sale-orders.retag-uids-summary');

    Route::get('/sale-orders/{sale_order}/uid-export', 'SaleOrdersController@uidExport')->name('sale-orders.uid-export');

    Route::put('/sale-orders/{sale_order}', 'SaleOrdersController@update')->name('sale-orders.update');
    Route::put('/sale-orders/{sale_order}/apply_discount', 'SaleOrdersController@applyDiscount')->name('sale-orders.apply-discount');

    Route::post('/sale-orders/{sale_order}/open', 'SaleOrdersController@open')->name('sale-orders.open');
    Route::post('/sale-orders/{sale_order}/ready-for-delivery', 'SaleOrdersController@readyForDelivery')->name('sale-orders.ready-for-delivery');
    Route::post('/sale-orders/{sale_order}/in-transit', 'SaleOrdersController@inTransit')->name('sale-orders.in-transit');
    Route::post('/sale-orders/{sale_order}/close', 'SaleOrdersController@close')->name('sale-orders.close');
    Route::post('/sale-orders/{sale_order}/payment', 'SaleOrdersController@payment')->name('sale-orders.payment');
    Route::get('/sale-orders/{sale_order}/invoice', 'SaleOrdersController@invoice')->name('sale-orders.invoice');
    Route::get('/sale-orders/{sale_order}/shipping-manifest', 'SaleOrdersController@shippingManifest')->name('sale-orders.shipping-manifest');
    Route::post('/sale-orders/{sale_order}/remove', 'SaleOrdersController@remove')->name('sale-orders.remove');
    Route::post('/sale-orders/{sale_order}/remove/{order_detail}', 'SaleOrdersController@removeItem')->name('sale-orders.remove-item');
    Route::put('/sale-orders/{sale_order}/accept/{order_detail}', 'SaleOrdersController@acceptOrderDetail')->name('sale-orders.accept-order-detail');
    Route::put('/sale-orders/{sale_order}/accept-all', 'SaleOrdersController@acceptAll')->name('sale-orders.accept-all');

    Route::post('/order-details', 'OrderDetailsController@store')->name('order-details.store');
    Route::put('/order-details/{order_detail}', 'OrderDetailsController@update')->name('order-details.update');
    Route::put('/order-details/{order_detail}/retag', 'OrderDetailsController@retag')->name('order-details.retag');

    Route::get('/products', 'ProductsController@index')->name('products.index');
    Route::get('/products/{product}', 'ProductsController@show')->name('products.show');
    Route::post('/products/{product}/pickup', 'ProductsController@pickup')->name('products.pickup');
    Route::post('/products/{product}/approve-return', 'ProductsController@approveReturn')->name('products.approve-return');
    Route::get('/products/{product}/pickup-success', 'ProductsController@pickupSuccess')->name('products.pickup-success');

    Route::post('/products/{product}/sell-return', 'ProductsController@sellReturn')->name('products.sell_return');

    Route::get('/products/{product}/activity', 'ProductsController@activity')->name('products.activity');

    Route::get('/batches', 'BatchesController@index')->name('batches.index');
    Route::get('/batches/reset-filters', 'BatchesController@resetFilters')->name('batches.reset-filters');
    Route::get('/batches/search', 'BatchesController@search')->name('batches.search');

    Route::get('/batches/qr-code/{batch}', 'BatchesController@qrCode')->name('batches.qr-code');
    Route::get('/batches/qr-codes/{category}', 'BatchesController@qrCodes')->name('batches.qr-codes');
    Route::get('/batches/print-inventory/{remove_cost?}', 'BatchesController@printInventory')->name('batches.print-inventory');

    Route::get('/batches/reconcile', 'BatchesController@reconcile')->name('batches.reconcile-list');
    Route::get('/batches/reconcile/log', 'BatchesController@reconcileLog')->name('batches.reconcile-log');
    Route::get('/batches/reconcile/log/batch/{batch}', 'BatchesController@reconcileLog')->name('batches.reconcile-log-batch');
    Route::get('/batches/reconcile/{batch}', 'BatchesController@reconcile')->name('batches.reconcile-batch');
    Route::post('/batches/reconcile', 'BatchesController@reconcileProcess')->name('batches.reconcile');

    Route::get('/batches/{batch}/sales', 'BatchesController@sales')->name('batches.sales');
    Route::get('/batches/{batch}/edit', 'BatchesController@edit')->name('batches.edit');
    Route::get('/batches/{batch}/transfer', 'BatchesController@transfer')->name('batches.transfer');

    Route::get('/batches/{batch}/transfer-log', 'BatchesController@transfer_log')->name('batches.transfer-log');
    Route::post('/batches/{batch}/transfer-log/{transfer_log?}', 'BatchesController@transfer_log')->name('batches.transfer-log');

    Route::get('/batches/{batch}/labels', 'BatchesController@labels')->name('batches.labels');
    Route::post('/batches/{batch}/transfer', 'BatchesController@transfer')->name('batches.transfer');

    Route::get('/batches/{batch}/submit_for_testing', 'BatchesController@submit_for_testing')->name('batches.submit_for_testing');

    Route::post('/batches/{batch}/pickup', 'BatchesController@pickup')->name('batches.pickup');
    Route::post('/batches/{batch}/sell', 'BatchesController@sell')->name('batches.sell');
    Route::post('/batches/{batch}/release', 'BatchesController@release')->name('batches.release');
    Route::put('/batches/{batch}/update', 'BatchesController@update')->name('batches.update');

    Route::post('/batches/{batch}/submit_for_testing', 'BatchesController@submit_for_testing')->name('batches.submit_for_testing');
    Route::post('/batches/{batch}/testing_results', 'BatchesController@testing_results')->name('batches.testing_results');

    Route::get('/batches/{batch}/customer/{user}', 'BatchesController@show')->name('batches.show.customer');

    Route::get('/batches/{batch}/{vault_log_ref?}', 'BatchesController@show')->name('batches.show');

    Route::get('/transporters', 'TransportersController@index')->name('transporters.index');

    Route::get('/accounting/transactions/', 'AccountingController@transactions')->name('accounting.transactions');
    Route::get('/accounting/payables/', 'AccountingController@payables')->name('accounting.payables');
    Route::get('/accounting/receivables/', 'AccountingController@receivables')->name('accounting.receivables');
    Route::get('/accounting/receivables/aging', 'AccountingController@receivables_aging')->name('accounting.receivables_aging');
    Route::get('/accounting/payables/', 'AccountingController@payables')->name('accounting.payables');
    Route::get('/accounting/inventory-loss/', 'AccountingController@inventory_loss')->name('accounting.inventory-loss');
    Route::get('/accounting/sales-rep-commissions/', 'AccountingController@sales_rep_commissions')->name('accounting.sales_rep_commissions');
    Route::post('/accounting/sales-rep-commissions/', 'AccountingController@sales_rep_commissions_store')->name('accounting.sales_rep_commissions_store');


    Route::get('/prepack-logs', 'PrePackLogsController@index')->name('prepack-logs.index');

//    Route::get('/vault-logs/{vault_log_session?}', 'VaultLogController@index')->name('vault-logs.index');
    Route::get('/vault-logs', 'VaultLogController@index')->name('vault-logs.index');
    Route::get('/vault-logs/create/{ref_number?}', 'VaultLogController@create')->name('vault-logs.create');
    Route::get('/vault-logs/reset-filters', 'VaultLogController@resetFilters')->name('vault-logs.reset-filters');
    Route::post('/vault-logs', 'VaultLogController@store')->name('vault-logs.store');
    Route::post('/vault-logs/complete', 'VaultLogController@complete')->name('vault-logs.complete');
    Route::post('/vault-logs/{vault_log}/add_to_sale_order', 'VaultLogController@addToSaleOrder')->name('vault-logs.add_to_sale_order');
    Route::get('/vault-logs/{vault_log_session}/return_order', 'VaultLogController@returnOrder')->name('vault-logs.return_order');
    Route::delete('/vault-logs/{vault_log}', 'VaultLogController@destroy')->name('vault-logs.destroy');

});

Route::get('/vault-logs/login/{ref_number}', 'VaultLogController@login')->name('vault-logs.login');
Route::post('/vault-logs/forceLogin/{ref_number}', 'VaultLogController@forceLogin')->name('vault-logs.force-login');
