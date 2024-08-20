<?php

use Crater\Http\Controllers\V1\CorePBX\PbxServerCheckConnectionController;
use Crater\Http\Controllers\V1\CorePBX\PbxServerCheckLicenseController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceCheckConnectionController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailAvalaraController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailCallHistoryIndiController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailCdrController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailCommandController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailDidUpdateController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceDetailExtUpdateController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceExtensionController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceInvoiceController;
use Crater\Http\Controllers\V1\CorePBX\PbxServicesController;
use Crater\Http\Controllers\V1\CorePBX\PbxServiceTenantController;
use Crater\Http\Controllers\V1\CorePBX\PbxTenantAppsController;
use Illuminate\Support\Facades\Route;

//pbx service details
Route::get('/pbx/service-detail/list/{pbx_service_id}', [PbxServiceDetailController::class, 'listServiceDetail']); //unused
Route::get('/pbx/service-detail/ext/{pbx_service_id}', [PbxServiceDetailController::class, 'serviceDetailExtensions'])->name('pbx.detail.ext'); //done

Route::get('/pbx/service-detail/did/{pbx_service_id}', [PbxServiceDetailController::class, 'serviceDetailDids']); //done
Route::get('/pbx/service-detail/cdr/{pbx_service_id}', [PbxServiceDetailController::class, 'serviceDetailCdrs']); //done
//Route::get('/pbx/service-detail/cdr/{pbx_service_id}/inbound', [PbxServiceDetailController::class, 'serviceDetailCdrsInbound']);
//Route::get('/pbx/service-detail/cdr/{pbx_service_id}/outbound', [PbxServiceDetailController::class, 'serviceDetailCdrsOutbound']);
//Route::get('/pbx/service-detail/cdr/{pbx_service_id}/totals', [PbxServiceDetailController::class, 'getServiceTotal'])->name('serviceTotals');
Route::get('/pbx/service-detail/cdr/{pbxService}/commandos', PbxServiceDetailCommandController::class)->name('serviceCommand');
Route::get('/pbx/service-detail/cdr/{pbxService}/avalara', PbxServiceDetailAvalaraController::class)->name('serviceDetailAvalara');
Route::post('/pbx/service-detail/cdr/download', [PbxServiceDetailController::class, 'downloadCalls']);

Route::post('/pbx/service-detail/update-price-extension', [PbxServiceDetailController::class, 'updatePriceExtension']); //done
Route::post('/pbx/service-detail/update-price-did', [PbxServiceDetailController::class, 'updatePriceDid']); //done
Route::post('/pbx/recalculate-service/{id}', [PbxServiceDetailController::class, 'recalculateTotalsPbxService']); //done
//--
//Route::get('pbxware/tenants',[PbxTestController::class, 'calculateTotal'])->name('calculateTotal');
//Route::get('/pbxware/cdr-totals/{service}', [PbxTestController::class, 'calculateTotal'])->name('calculateTotal');
//Route::get('/pbxware/service/{service}/extensions', [PbxTestController::class, 'extensions'])->name('pbxservice.ext');
//Route::get('/pbxware/service/{service}/dids', [PbxTestController::class, 'dids'])->name('pbxservice.did');

//--------Nuevos------------//

Route::get('/pbx/service-detail/service/{pbx_service_id}', [PbxServiceDetailController::class, 'getService']); //omit
Route::get('/pbx/service-detail/avalara-taxes-items/{pbx_package_id}', [PbxServiceDetailController::class, 'getAvalaraTaxesItems']); //omit
Route::get('/pbx/service-detail/avalara-taxes/{invoice_id}', [PbxServiceDetailController::class, 'getAvalaraTaxes']); //omit
Route::get('/pbx/service-detail/item/{pbx_service_id}', [PbxServiceDetailController::class, 'serviceDetailItems']); //Done?;
Route::get('/pbx/service-detail/charges/{pbx_service_id}', [PbxServicesController::class, 'getAdditionalCharges']); //Done?
Route::get('/pbx/service-detail/get-additional-charges/{pbx_service_id}', [PbxServicesController::class, 'getAdditionalChargesService']); //Done?
Route::put('/pbx/service-detail/update-status/{pbx_service_id}', [PbxServiceDetailController::class, 'updateServiceStatus']);
Route::post('/pbx/service-detail/delete', [PbxServiceDetailController::class, 'delete']);

Route::get('/pbx/service-detail/inv/{pbx_service_id}', [PbxServiceDetailController::class, 'invoicesPerServicePbx']); //Done?;

Route::get('/pbx/service-detail/custom-app-rate/{pbx_service_id}', [PbxServiceDetailController::class, 'customAppRate']);

// Create o Edit (PbxService)
Route::get('/pbx/service-detail/custom-app-rate-pbx-service/{pbx_service_id}', [PbxServiceDetailController::class, 'customAppRateForPbxService']);

//tenant
Route::put('/pbx/service/{pbxService}/tenant/suspend', [PbxServiceTenantController::class, 'suspend'])->where('pbxService', '[0-9]+');
Route::put('/pbx/service/{pbxService}/tenant/unsuspend', [PbxServiceTenantController::class, 'unsuspend'])->where('pbxService', '[0-9]+');

//DID
Route::put('/pbx/service/{pbxService}/did/{did}', PbxServiceDetailDidUpdateController::class)->where('ext', '[0-9]+');

//Ext
Route::put('/pbx/service/{pbxService}/ext/{ext}', PbxServiceDetailExtUpdateController::class)->where('ext', '[0-9]+');

//CallHistoryIndi
Route::get('/pbx/service/{pbxService}/callhistoryindi', [PbxServiceDetailCallHistoryIndiController::class, 'index']);

//CDR Advance search.
Route::get('/pbx/service/{pbxService}/cdr', [PbxServiceDetailCdrController::class, 'index']);

//PbxExtSuspend
Route::put('/pbx/service/{pbxService}/ext/suspend', [PbxServiceExtensionController::class, 'suspend']);
Route::put('/pbx/service/{pbxService}/ext/unsuspend', [PbxServiceExtensionController::class, 'unsuspend']);

Route::get('/pbx/service/{pbxService}/invoices', [PbxServiceInvoiceController::class, 'index'])->name('pbx.service.invoice.index');
Route::get('/service/{service}/invoices', [PbxServiceInvoiceController::class, 'index'])->name('service.invoice.index');

//Tenant
//Apps
Route::get('/pbx/tenant/{pbxTenant}/apps', [PbxTenantAppsController::class, 'index']);

//CheckConnection

Route::get('/pbx/service/{pbxService}/check-connection', PbxServiceCheckConnectionController::class)->name('pbxCheckConnection');

Route::get('/pbx/server/{pbxServer}/check-connection', PbxServerCheckConnectionController::class)->name('pbxCheckConnection');

Route::get('/pbx/server/{pbxServer}/license', PbxServerCheckLicenseController::class)->name('pbxCheckLicense');
