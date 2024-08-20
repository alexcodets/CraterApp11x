<?php

use Crater\Http\Controllers\V1\Customer\CustomerConfigController;
use Crater\Http\Controllers\V1\Customer\CustomerPbxServiceController;
use Crater\Http\Controllers\V1\CustomerProfile\CustomerProfileController;

// Customer dashboard

Route::get('/stats', [CustomerProfileController::class, 'mainInformation']);

Route::get('/services', [\Crater\Http\Controllers\V1\Customer\CustomerServiceController::class, 'index']);

Route::get('/invoices', [CustomerProfileController::class, 'getInvoices']);

Route::get('/estimates', [\Crater\Http\Controllers\V1\Customer\CustomerEstimateController::class, 'index']);

Route::get('/expenses', [CustomerProfileController::class, 'getExpenses']);

Route::get('/payments', [\Crater\Http\Controllers\V1\Customer\CustomerPaymentController::class, 'index']);

Route::get('/pbx-services', [CustomerPbxServiceController::class, 'index']);

Route::get('/{customer}/get-config', [CustomerConfigController::class, 'getConfig']);
