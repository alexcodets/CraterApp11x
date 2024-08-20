<?php

use Crater\Http\Controllers\V1\Avalara\AvalaraApiTaxTypesController;
use Crater\Http\Controllers\V1\Avalara\AvalaraCheckAdressLocationController;
use Crater\Http\Controllers\V1\Avalara\AvalaraCheckCredentialsController;
use Crater\Http\Controllers\V1\Avalara\AvalaraCheckLocationController;
use Crater\Http\Controllers\V1\Avalara\AvalaraConfigController;
use Crater\Http\Controllers\V1\Avalara\AvalaraExemptionController;
use Crater\Http\Controllers\V1\Avalara\AvalaraExemptionEnableController;
use Crater\Http\Controllers\V1\Avalara\AvalaraLocationDefaultController;
use Crater\Http\Controllers\V1\Avalara\AvalaraLocationsController;
use Crater\Http\Controllers\V1\Item\ItemsController;

Route::get('/service-types/{code}', [ItemsController::class, 'avalaraServiceTypes']);

Route::get('/default', [AvalaraConfigController::class, 'getAvalaraDefault']);
Route::put('/config/default/{id}', [AvalaraConfigController::class, 'setAvalaraDefault']);
Route::get('/config/logs', [AvalaraConfigController::class, 'getAvalaraLogs']);
Route::get('/config/avalara-items', [AvalaraConfigController::class, 'getAvalaraItems']);
Route::apiResource('/config', AvalaraConfigController::class);
Route::post('/location/update', [AvalaraLocationsController::class, 'updateLocationCustomer']);
Route::post('/location', [AvalaraLocationsController::class, 'store']);
Route::put('/location/{location_id}/set-default/company', [AvalaraLocationDefaultController::class, 'company']);
Route::put('/location/{location_id}/set-default/user', [AvalaraLocationDefaultController::class, 'user']);

// Config Customer
Route::get('/{user_id}/exemption', [AvalaraExemptionController::class, 'index']);
Route::post('/{user_id}/exemption', [AvalaraExemptionController::class, 'store']);
Route::put('/{user_id}/exemption/{exemption_id}', [AvalaraExemptionController::class, 'update']);
Route::put('/{user_id}/exemption/{exemption_id}/enable', [AvalaraExemptionEnableController::class, 'enable']);
Route::put('/{user_id}/exemption/{exemption_id}/disable', [AvalaraExemptionEnableController::class, 'disable']);

//Checks
Route::get('/config/{config}/check/credentials', AvalaraCheckCredentialsController::class);
Route::get('/config/{config}/check/location', AvalaraCheckLocationController::class);
Route::get('/config/{config}/check/address/{address_id}', [AvalaraCheckAdressLocationController::class, 'show']);

Route::get('/config/{config}/tspair', \Crater\Http\Controllers\AvalaraGetTsPairController::class)->name('avalara.tspair');
Route::get('/config/{config}/api/taxtypes', [AvalaraApiTaxTypesController::class, 'index'])->name('avalara.taxtypes');
