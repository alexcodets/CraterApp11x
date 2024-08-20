<?php

use Crater\Http\Controllers\V1\Avalara\AvalaraItemsTaxController;
use Crater\Http\Controllers\V1\Avalara\AvalaraItemTaxController;
use Crater\Http\Controllers\V1\Avalara\AvalaraVoidInvoiceController;
use Crater\Http\Controllers\V1\Avalara\PbxAvalaraAdjustmentController;

Route::post('/avalara-tax/{user}/{item}', [AvalaraItemTaxController::class, 'show'])->name('avalaraTax.show')
    ->where(['user' => '[0-9]+', 'item' => '[0-9]+']);
Route::post('/avalara-tax/{user}', [AvalaraItemsTaxController::class, 'show'])->name('avalaraTaxes.show')
    ->where('user', '[0-9]+');

// VOID
Route::put('/{invoice}/avalara/void', [AvalaraVoidInvoiceController::class, 'void']);
Route::put('/{invoice}/avalara/adjustment', [PbxAvalaraAdjustmentController::class, 'update']);
Route::get('/{invoice}/avalara/status', [AvalaraVoidInvoiceController::class, 'status']);
