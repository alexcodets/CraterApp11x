<?php

use Crater\Http\Controllers\V1\CorePBX\PbxCdrTenantController;
use Crater\Http\Controllers\V1\CorePBX\PbxCdrTenantEnableController;
use Crater\Http\Controllers\V1\CorePBX\PbxCdrTenantJobsController;
use Crater\Http\Controllers\V1\CorePBX\PbxCommandController;

//Route::post('/invoices/avalara-tax/{user}/{item}', [AvalaraItemTaxController::class, 'show'])->name('avalaraTax.show');
//Route::get('/tenant-cdr/{pbx_service_id}', [PbxServiceDetailController::class, 'serviceDetailExtensions']);
//Route::update('/cdr-tenant/{pbx_service_id}', [PbxServiceDetailController::class, 'cdrTenant.update']);
Route::get('/cdr-tenant', [PbxCdrTenantController::class, 'index'])->name('cdrTenant.index');
//Route::put('/cdr-tenant/status', [PbxCdrTenantController::class, 'index'])->name('cdrTenant.index');
Route::put('/cdr-tenant/{id}/enable', [PbxCdrTenantEnableController::class, 'enable'])->name('cdrTenant.enable');
Route::put('/cdr-tenant/{id}/disable', [PbxCdrTenantEnableController::class, 'disable'])->name('cdrTenant.disable');

Route::delete('/cdr-tenant/{id}/clean-jobs', [PbxCdrTenantJobsController::class, 'clean'])->name('cdrTenant.cleanJobs');
Route::put('/cdr-tenant/{id}/enable-jobs', [PbxCdrTenantJobsController::class, 'reactive'])->name('cdrTenant.enableJobs');
Route::delete('/cdr-tenant/clean-jobs', [PbxCdrTenantJobsController::class, 'cleanAll'])->name('cdrTenant.cleanAllJobs');
Route::put('/cdr-tenant/enable-jobs', [PbxCdrTenantJobsController::class, 'reactiveAll'])->name('cdrTenant.enableAllJobs');
Route::post('/command/pbx/tenant-import-cdr/{tenant}', [PbxCommandController::class, 'tenantImportCdr'])->name('commandPbx.tenantCdr');
