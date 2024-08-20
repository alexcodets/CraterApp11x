<?php

use Crater\Authorize\Controllers\{AuthorizeController, AuthorizeSettingsController};
use Crater\CorePos\Controllers\CorePosController;
use Crater\Http\Controllers\AppVersionController;
use Crater\Http\Controllers\CustomAppRateController;
use Crater\Http\Controllers\CustomSearchController;
use Crater\Http\Controllers\CustomSearchReportController;
use Crater\Http\Controllers\DocumentVerificationController;
use Crater\Http\Controllers\ExpenseTemplateController;
use Crater\Http\Controllers\ItemCategoriesController;
use Crater\Http\Controllers\NoteTicketController;
use Crater\Http\Controllers\StripeSettingController;
use Crater\Http\Controllers\UserAdmissionListController;
use Crater\Http\Controllers\V1;
use Crater\Http\Controllers\V1\Address\AddressController;
use Crater\Http\Controllers\V1\Auth\ForgotPasswordController;
use Crater\Http\Controllers\V1\Auth\ResetPasswordController;
use Crater\Http\Controllers\V1\Backup\BackupsController;
use Crater\Http\Controllers\V1\Backup\DownloadBackupController;
use Crater\Http\Controllers\V1\Company\CompanyLogoController;
use Crater\Http\Controllers\V1\Contacts\ContactsController;
use Crater\Http\Controllers\V1\CorePBX\{AditionalChargesController,
    CustomDidGroupController,
    PbxCategorieController,
    PbxDIDController,
    PbxExtensionsController,
    PbxHostedManage,
    PbxServiceJobsController,
    PbxServicesController,
    PbxTenantController,
    ProfileDIDController,
    ProfileDidTollFreeController,
    ProfileExtensionsController,
    ProfileInternacionalRateController};
use Crater\Http\Controllers\V1\CorePOS\{CashHistoryController,
    CorePosHistoryController,
    DashboardCorePosController,
    HoldInvoiceController,
    PosItemCategoryController,
    PosPaymentMethodsController,
    StoreController,
    TableController};
use Crater\Http\Controllers\V1\Customer\{
    CustomerConfigController,
    CustomerPushNotificationLogController,
    CustomerStatsController,
    CustomersController
};
use Crater\Http\Controllers\V1\CustomerNote\CustomerNoteController;
use Crater\Http\Controllers\V1\CustomerTicket\CustomerTicketController;
use Crater\Http\Controllers\V1\CustomField\CustomFieldsController;
use Crater\Http\Controllers\V1\Dashboard\DashboardController;
use Crater\Http\Controllers\V1\DownloadGuideController;
use Crater\Http\Controllers\V1\Estimate\{
    ChangeEstimateStatusController,
    ConvertEstimateController,
    EstimateTemplatesController,
    EstimatesController,
    SendEstimateController
};
use Crater\Http\Controllers\V1\EstimatesCustomer\EstimateCustomersController;
use Crater\Http\Controllers\V1\Expense\{ExpenseCategoriesController,
    ExpensesController,
    ShowDocsController,
    ShowReceiptController,
    UploadReceiptController};
use Crater\Http\Controllers\V1\General\{BootstrapController,
    CountriesController,
    CurrenciesController,
    DateFormatsController,
    FiscalYearsController,
    LanguagesController,
    NextNumberController,
    NotesController,
    SearchController,
    StatesController,
    TimezonesController};
use Crater\Http\Controllers\V1\Invoice\ChangeInvoiceStatusController;
use Crater\Http\Controllers\V1\Invoice\CloneInvoiceController;
use Crater\Http\Controllers\V1\Invoice\InvoiceCustomerController;
use Crater\Http\Controllers\V1\Invoice\InvoicesController;
use Crater\Http\Controllers\V1\Invoice\InvoiceTemplatesController;
use Crater\Http\Controllers\V1\Invoice\SendInvoiceController;
use Crater\Http\Controllers\V1\Item\ItemsController;
use Crater\Http\Controllers\V1\Item\UnitsController;
use Crater\Http\Controllers\V1\ItemGroups\ItemGroupController;
use Crater\Http\Controllers\V1\Lead\LeadController;
use Crater\Http\Controllers\V1\LeadNoteController;
use Crater\Http\Controllers\V1\Mobile\AuthController;
use Crater\Http\Controllers\V1\Mobile\LogsController;
use Crater\Http\Controllers\V1\Mobile\PushNotificationsController;
use Crater\Http\Controllers\V1\Onboarding\DatabaseConfigurationController;
use Crater\Http\Controllers\V1\Onboarding\FinishController;
use Crater\Http\Controllers\V1\Onboarding\OnboardingWizardController;
use Crater\Http\Controllers\V1\Onboarding\PermissionsController;
use Crater\Http\Controllers\V1\Onboarding\RequirementsController;
use Crater\Http\Controllers\V1\Package\PackageGroupController;
use Crater\Http\Controllers\V1\Packages\PackagesController;
use Crater\Http\Controllers\V1\Payment\FailedPaymentHistoryController;
use Crater\Http\Controllers\V1\Payment\PaymentMethodsController;
use Crater\Http\Controllers\V1\Payment\PaymentsController;
use Crater\Http\Controllers\V1\Payment\SendPaymentController;
use Crater\Http\Controllers\V1\PaymentAccount\PaymentAccountController;
use Crater\Http\Controllers\V1\PaymentAccount\PaymentAccountCustomerController;
use Crater\Http\Controllers\V1\PaymentGatewaysFeeController;
use Crater\Http\Controllers\V1\PaymentsCustomer\PaymentCustomersController;
use Crater\Http\Controllers\V1\Paypal\PaypalSettingsController;
use Crater\Http\Controllers\V1\PaypalPro\PaymentPaypalProController;
use Crater\Http\Controllers\V1\PrefixGroup\PrefixGroupController;
use Crater\Http\Controllers\V1\Provider\ProviderController;
use Crater\Http\Controllers\V1\Report\TaxSummaryReportController;
use Crater\Http\Controllers\V1\Role\RoleController;
use Crater\Http\Controllers\V1\Service\ServiceController;
use Crater\Http\Controllers\V1\Service\ServiceInvoiceController;
use Crater\Http\Controllers\V1\Settings\BandwidthController;
use Crater\Http\Controllers\V1\Settings\CompanyController;
use Crater\Http\Controllers\V1\Settings\DiskController;
use Crater\Http\Controllers\V1\Settings\EmailLogsController;
use Crater\Http\Controllers\V1\Settings\GetCompanySettingsController;
use Crater\Http\Controllers\V1\Settings\GetUserSettingsController;
use Crater\Http\Controllers\V1\Settings\MailConfigurationController;
use Crater\Http\Controllers\V1\Settings\MobileSettings;
use Crater\Http\Controllers\V1\Settings\ModuleLogsController;
use Crater\Http\Controllers\V1\Settings\ModulesController;
use Crater\Http\Controllers\V1\Settings\PaymentGatewayController;
use Crater\Http\Controllers\V1\Settings\PbxPackagesController;
use Crater\Http\Controllers\V1\Settings\PbxServersController;
use Crater\Http\Controllers\V1\Settings\RetentionsController;
use Crater\Http\Controllers\V1\Settings\TaxAgencyController;
use Crater\Http\Controllers\V1\Settings\TaxCategoryController;
use Crater\Http\Controllers\V1\Settings\TaxTypesController;
use Crater\Http\Controllers\V1\Settings\TicketDepartamentController;
use Crater\Http\Controllers\V1\Settings\UpdateCompanySettingsController;
use Crater\Http\Controllers\V1\Settings\UpdateUserSettingsController;
use Crater\Http\Controllers\V1\TaxGroups\TaxGroupController;
use Crater\Http\Controllers\V1\Update\CheckVersionController;
use Crater\Http\Controllers\V1\Update\CopyFilesController;
use Crater\Http\Controllers\V1\Update\DeleteFilesController;
use Crater\Http\Controllers\V1\Update\DownloadUpdateController;
use Crater\Http\Controllers\V1\Update\FinishUpdateController;
use Crater\Http\Controllers\V1\Update\MigrateUpdateController;
use Crater\Http\Controllers\V1\Update\UnzipUpdateController;
use Crater\Http\Controllers\V1\Users\UsersController;
use Crater\Http\Controllers\V2;
use Crater\Models\PbxServerTenant;
use Illuminate\Support\Facades\Route;

// Core Pos

// settings

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

// ping
//----------------------------------
Route::get('ping', function () {
    return response()->json([
        'success' => 'crater-self-hosted',
    ]);
})->name('ping');
Route::post('/hold_invoice/pdf', [HoldInvoiceController::class, 'getPdfHoldInvoice']);


Route::get('/download-guide', [DownloadGuideController::class, 'downloadGuide'])->name('download.guide');

Route::get('/download-csvexample', [DownloadGuideController::class, 'downloadCsv'])->name('download.csvexample');

// Route::get('/pbx/recalculate-service/{id}', [PbxServiceDetailController::class, 'recalculateTotalsPbxService']);
// Version 1 endpoints
// --------------------------------------
Route::prefix('/v1')->group(function () {

    // App version
    // ----------------------------------

    Route::get('/app/version', AppVersionController::class);
    // Authentication & Password Reset
    //----------------------------------

    Route::prefix('auth')->group(function () {

        Route::post('login', [AuthController::class, 'login']);

        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

        // Send reset password mail
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:10,2');

        // handle reset password form process
        Route::post('reset/password', [ResetPasswordController::class, 'reset']);
    });

    Route::get('/countries', CountriesController::class);
    Route::get('/states/{code}', StatesController::class);

    Route::get('/company-logo', [CompanyController::class, 'getCompanyLogo']);
    Route::get('/company/{company}/logo', [CompanyLogoController::class, 'show']);

    // Approval Status Estimate
    Route::get('/estimates/approval/{inique_hash}', [EstimatesController::class, 'approval']);

    // Payment invoice without login
    //--------------
    Route::get('/invoices_unique_hash/{unique_hash}', [InvoicesController::class, 'ViewInvoiceUniqueHash']);
    Route::get('/next-number', NextNumberController::class);
    Route::apiResource('payment-methods-without-login', PaymentMethodsController::class);
    Route::post('without-login', [PaymentMethodsController::class, 'paymentModesWithoutLogin']);
    Route::get('/payment-gateways-without-login', [PaymentGatewayController::class, 'getPaymentGateways']);
    Route::get('/payment-gateways-without-login', [PaymentGatewayController::class, 'getPaymentGateways']);
    Route::get('/customers-without-login/{customer}', [CustomersController::class, 'showCustomerWithoutLogin']);
    Route::post('without-login', [PaymentMethodsController::class, 'paymentModesWithoutLogin']);
    Route::post('/authorize-charge-without-login', [AuthorizeController::class, 'charge']);
    Route::apiResource('failed-payment-history-without', FailedPaymentHistoryController::class);
    Route::post('/authorize-save-without-login', [AuthorizeController::class, 'saveCharge']);
    Route::apiResource('payments-without-login', PaymentsController::class);

    // Onboarding
    //----------------------------------
    Route::middleware(['redirect-if-installed'])->group(function () {

        Route::get('/onboarding/wizard-step', [OnboardingWizardController::class, 'getStep']);

        Route::post('/onboarding/wizard-step', [OnboardingWizardController::class, 'updateStep']);

        Route::get('/onboarding/requirements', [RequirementsController::class, 'requirements']);

        Route::get('/onboarding/permissions', [PermissionsController::class, 'permissions']);

        Route::post('/onboarding/database/config', [DatabaseConfigurationController::class, 'saveDatabaseEnvironment']);

        Route::get('/onboarding/database/config', [DatabaseConfigurationController::class, 'getDatabaseEnvironment']);

        Route::post('/onboarding/finish', FinishController::class);
    });

    // test
    // Route::get('invoices-customer/index-by-status', [InvoiceCustomerController::class, 'indexByStatus']);

    // Only Admin
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {

        // Bootstrap
        //----------------------------------
        Route::get('/bootstrap', BootstrapController::class);

        // Dashboard
        //----------------------------------
        Route::get('/dashboard', DashboardController::class);
        //-- Invoices Due
        Route::get('/dashboard-invoices-due', [DashboardController::class, 'fetchInvoicesDue']);
        //-- Recent Estimates
        Route::get('/dashboard-recent-estimates', [DashboardController::class, 'fetchRecentEstimates']);

        // MISC
        //----------------------------------
        Route::get('/currencies', CurrenciesController::class);
        Route::get('/timezones', TimezonesController::class);
        Route::get('/timezones/contienents', [TimezonesController::class, 'getContinents']);
        Route::get('/timezones/zonebycontinent/{continent}', [TimezonesController::class, 'getZoneByContinent']);
        Route::get('/timezones/fulltimezone', [TimezonesController::class, 'getFullTimezone']);

        Route::get('/date/formats', DateFormatsController::class);
        Route::get('/fiscal/years', FiscalYearsController::class);
        Route::get('/languages', LanguagesController::class);

        //aux vault Settings
        Route::post('/users/{user}/aux-vault/validate', V1\CheckAuxVaultSettingsController::class);
        Route::apiResource('aux-vault-settings', V1\AuxVaultSettingController::class)->parameters([
            'aux-vault-settings' => 'auxVaultSetting'
        ]);

        Route::post('/aux-vault-settings/delete', [V1\AuxVaultSettingController::class, 'delete']);
        Route::put('aux-vault-settings/set-default/{id}', [V1\AuxVaultSettingController::class, 'setDefault']);


        // Self Update
        //----------------------------------
        Route::get('/check/update', CheckVersionController::class);
        Route::post('/update/download', DownloadUpdateController::class);
        Route::post('/update/unzip', UnzipUpdateController::class);
        Route::post('/update/copy', CopyFilesController::class);
        Route::post('/update/delete', DeleteFilesController::class);
        Route::post('/update/migrate', MigrateUpdateController::class);
        Route::post('/update/finish', FinishUpdateController::class);

        // leads
        //----------------------------------
        Route::resource('leads', LeadController::class);
        Route::get('customer-lead', [LeadController::class, 'getCustomersLeads']);
        Route::post('lead-email', [LeadController::class, 'sendemail']);

        // leadsnotes
        //----------------------------------
        Route::resource('lead-notes', LeadNoteController::class);
        Route::post('/lead-notes/delete', [ LeadNoteController::class, 'delete']);
        // Customers
        //----------------------------------
        Route::put('/customers-restore/{id}', [CustomersController::class, 'restoreCustomer']);
        Route::get('/customers-disabled', [CustomersController::class, 'customersDisabled']);
        Route::post('/customers/fetch-prefix', [CustomersController::class, 'fetchPrefix']);
        Route::post('/customers/set-prefix', [CustomersController::class, 'setPrefix']);
        Route::post('/customers/delete', [CustomersController::class, 'delete']);
        Route::get('customers/{customer}/stats', CustomerStatsController::class);
        Route::post('customers/package/save', [CustomersController::class, 'savePackage']);
        Route::get('customers/{customer}/packages', [CustomersController::class, 'getPackages']);
        Route::get('customers/{customer}/invoices', [CustomersController::class, 'getInvoices']);
        Route::post('/customer/services', [CustomersController::class, 'getServices']);
        Route::get('customers/{customer}/pbx-services', [CustomersController::class, 'getPbxServices']);
        Route::get('customers/{customer}/estimates', [CustomersController::class, 'getEstimates']);
        Route::post('customers/set-config', [CustomerConfigController::class, 'setConfig']);
        Route::get('customers/{customer}/get-config', [CustomerConfigController::class, 'getConfig']);
        Route::get('customers/username/{username}', [CustomersController::class, 'customerUsernameAvailable']);
        Route::post('customers/{customer}/send-password', [CustomersController::class, 'sendpassword']);
        Route::post('customers/billing-validation', [CustomersController::class, 'billingValidation']);
        Route::resource('customers', CustomersController::class);
        Route::get('/customers-selector', [CustomersController::class, 'indexselectcustomer']);

        // Customers - Note
        //----------------------------------
        Route::post('/customer-note/delete', [CustomerNoteController::class, 'delete']);
        Route::apiResource('customer-note', CustomerNoteController::class);

        // Customers - Ticket
        //----------------------------------
        Route::apiResource('customer-ticket', CustomerTicketController::class);
        Route::post('/customer-ticket/delete', [CustomerTicketController::class, 'delete']);
        Route::get('/customer-ticket-list-users/list-users', [CustomerTicketController::class, 'getListUsers']);
        Route::get('/customerticketstatus/index-by-status', [CustomerTicketController::class, 'getListByStatus']);
        Route::get('/customer-ticket-list-custom/list-users-customers', [CustomerTicketController::class, 'getListUsersCustomers']);
        Route::get('/customer-ticket/index', [CustomerTicketController::class, 'index']);
        //Route::apiResource('customer-ticket', CustomerTicketController::class);
        Route::get('/customer-ticket/{customer}/services', [CustomerTicketController::class, 'getServicesByCustomer']);
        Route::get('/customer-ticket/{customer}/pbx-services', [CustomerTicketController::class, 'getPbxServicesByCustomer']);

        Route::resource('/notes-ticket', NoteTicketController::class);
        Route::get('/get-notes-ticket/{ticket_id}', [ NoteTicketController::class, 'getNotesTickets'])->name('get-notes-ticket');
        Route::post('/notes-ticket/delete', [ NoteTicketController::class, 'delete'])->name('notes-ticket.delete');

        // Customer - Address
        Route::apiResource('/customer-address', AddressController::class);
        Route::post('/customer-address/delete', [AddressController::class, 'delete']);
        Route::put('/customer-address/update', [AddressController::class, 'update']);

        // Contacts
        Route::apiResource('/contacts', ContactsController::class);
        Route::get('/customer/{customer_id}/contacts', [ContactsController::class, 'index']);
        Route::post('/contacts/delete', [ContactsController::class, 'destroy']);
        Route::put('/contacts/update', [ContactsController::class, 'update']);

        Route::get('customer/{customer}/push-notifications', [CustomerPushNotificationLogController::class, 'index'])->name('customer.pushNotification.index');

        // CorePos
        Route::resource('/core-pos/cash-history', CashHistoryController::class);
        Route::get('/core-pos/get-cash-history', [ CashHistoryController::class, 'getCashRegisterHistories']);
        Route::post('/core-pos/confirm-open/{id}', [ CashHistoryController::class, 'confirmOpenCashRegister']);
        Route::post('/core-pos/register-income-withdrawal', [ CashHistoryController::class, 'storeIncomeWithdrawalCash']);
        Route::get('/core-pos/show-income-withdrawal', [ CashHistoryController::class, 'showIncomeWithdrawalCash']);

        // Cash Register Dashboard //
        Route::resource('/core-pos/dashboard', DashboardCorePosController::class);
        Route::post('/dashboard-pos/cash-histories', [DashboardCorePosController::class, 'dataCashHistories']);
        Route::post('/dashboard-pos/income-withdrawal', [DashboardCorePosController::class, 'dataIncomeWithdrawalCash']);

        // Cash Register //
        Route::get('/core-pos/cash-register/getCashAmountPayments', [CorePosController::class, 'getInvoicesCashPayment']);
        Route::post('/core-pos/cash-register/getUserAssignCashRegister', [CorePosController::class, 'getUserAssignCashRegister']);
        Route::post('/core-pos/cash-register/userAssignCashRegister', [CorePosController::class, 'userAssignCashRegister']);
        Route::get('/core-pos/cash-register/getCashRegistersUser', [CorePosController::class, 'getCashRegistersUser']);
        Route::get('/core-pos/cash-register/getCashRegistersUserall', [CorePosController::class, 'getCashRegistersUserall']);
        Route::post('/core-pos/cash-register/getCashRegisters', [CorePosController::class, 'getCashRegisters']);
        Route::get('/core-pos/cash-register/getCashRegister/{id}', [CorePosController::class, 'getCashRegister']);
        Route::post('/core-pos/cash-register/getUsers', [CorePosController::class, 'getUsers']);
        Route::post('/core-pos/cash-register/addCashRegister', [CorePosController::class, 'addCashRegister']);
        Route::post('/core-pos/cash-register/updateCashRegister', [CorePosController::class, 'updateCashRegister']);
        Route::get('/core-pos/cash-register/deleteCashRegister/{id}', [CorePosController::class, 'deleteCashRegister']);
        // Money //
        Route::post('/core-pos/money/getMoney', [CorePosController::class, 'getMoney']);
        Route::post('/core-pos/money/addMoney', [CorePosController::class, 'addMoney']);
        Route::post('/core-pos/money/updateMoney', [CorePosController::class, 'updateMoney']);
        Route::get('/core-pos/money/deleteMoney/{id}', [CorePosController::class, 'deleteMoney']);
        Route::resource('/core-pos/item-categories/', PosItemCategoryController::class);
        Route::get('/core-pos/get-item-categories', [PosItemCategoryController::class, 'getPosItemCategoriesCompany']);
        Route::post('/core-pos/sections', [CorePosController::class, 'getSections']);
        Route::post('/core-pos/sections/create', [CorePosController::class, 'createSections']);
        Route::post('/core-pos/sections/update', [CorePosController::class, 'updateSections']);
        // Table //

        Route::get('/core-pos/table-cash-register/{id}', [TableController::class , 'getTablesCashRegister']);
        Route::post('/core-pos/get-tables', [TableController::class , 'getTables']);
        Route::resource('/core-pos/tables', TableController::class);
        //
        Route::resource('/core-pos/payment-methods/', PosPaymentMethodsController::class);
        Route::get('/core-pos/get-payment-methods', [PosPaymentMethodsController::class, 'getPosPaymentMethodsCompany']);
        // hold invoices core pos
        Route::resource('/core-pos/hold-invoices', HoldInvoiceController::class);
        Route::post('core-pos/hold-invoice/delete', [HoldInvoiceController::class, 'deleteHoldInvoice']);


        // Items
        //----------------------------------
        Route::post('/items/delete', [ItemsController::class, 'delete']);
        Route::post('/items/upload-picture', [ItemsController::class, 'uploadPicture']);
        Route::post('/items/setprefix', [ItemsController::class, 'setPrefix']);
        Route::get('/items/usage/{id}', [ItemsController::class, 'getUsage']);
        Route::get('/items-selector', [ItemsController::class, 'indexselectitem']);
        Route::resource('items', ItemsController::class);
        Route::resource('units', UnitsController::class);

        // Item Categories
        Route::post('/items/item-categories-is-item', [ItemsController::class, 'getItemCategories']);
        Route::post('/items/item-categories-is-group', [ItemGroupController::class, 'getItemCategories']);
        //

        // Get Items (Report-Sales)
        Route::post('/items/get-items', [ItemsController::class, 'getItems']);
        Route::post('/items/get-customers', [CustomersController::class, 'getCustomers']);
        Route::post('/items/get-items-by-filters', [ItemsController::class, 'getItemsByFilters']);

        // Invoices
        //-------------------------------------------------
        Route::post('/invoices/{invoice}/send', SendInvoiceController::class);
        Route::post('/invoices/{invoice}/clone', CloneInvoiceController::class);
        Route::post('/invoices/{invoice}/status', ChangeInvoiceStatusController::class);
        Route::post('/invoices/delete', [InvoicesController::class, 'delete']);
        Route::put('/invoices/{invoice}/invoice-archived', [InvoicesController::class, 'restoreInvoice']);
        Route::get('/invoices/archived', [InvoicesController::class, 'indexArchived']);
        Route::get('/invoices/archived/{invoice}', [InvoicesController::class, 'showArchived']);
        Route::get('/invoices/indexpayments', [InvoicesController::class, 'indexforpayments']);

        Route::post('/invoices/fetch-invoices-customer-payments', [InvoicesController::class, 'fetchInvoicesCustomerPayments']);

        Route::get('/invoices/templates', InvoiceTemplatesController::class);
        Route::apiResource('invoices', InvoicesController::class);
        Route::apiResource('invoices-customer', InvoiceCustomerController::class);
        Route::get('invoicescustomerstatus/index-by-status', [InvoiceCustomerController::class, 'indexByStatus']);
        Route::get('invoices/app_rates/{invoiceId}', [InvoicesController::class, 'getAppsRatesInvoice']);
        // Fecth Invoice Late Fees
        Route::get('/invoice/late-fees/{id}', [InvoicesController::class, 'getInvoiceLateFees']);

        // Estimates
        //-------------------------------------------------
        Route::post('/estimates/{estimate}/send', SendEstimateController::class);
        Route::post('/estimates/{estimate}/status', ChangeEstimateStatusController::class);
        Route::post('/estimates/{estimate}/convert-to-invoice', ConvertEstimateController::class);
        Route::get('/estimates/templates', EstimateTemplatesController::class);
        Route::post('/estimates/delete', [EstimatesController::class, 'delete']);
        Route::get('/estimates/list-users', [EstimatesController::class, 'getListUsers']);
        Route::apiResource('estimates', EstimatesController::class);

        // Expenses
        //----------------------------------
        Route::get('/expenses/{expense}/show/receipt', ShowReceiptController::class);
        Route::get('/expenses/{expense}/view/receipt', [ShowReceiptController::class, 'view']);
        Route::get('/expenses/{expense}/show/docs', [ShowDocsController::class, 'show']);
        Route::get('/expenses/download/{expense_id}/{doc_id}/doc', [ShowDocsController::class, 'download']);
        Route::post('/expenses/{expense}/upload/receipts', UploadReceiptController::class);
        Route::post('/expenses/save-massive', [ExpensesController::class, 'saveMassiveExpenses']);
        Route::get('/valid-payment-methods', [ExpensesController::class, 'getValidPaymentMethods']);
        Route::post('/expenses/set-prefix', [ExpensesController::class, 'setPrefix']);
        Route::post('/expenses/set-prefix-template', [ExpensesController::class, 'setPrefixTemplate']);
        Route::post('/expenses/delete', [ExpensesController::class, 'delete']);
        Route::apiResource('expenses', ExpensesController::class);
        Route::post('/expenses/is-valid-invoice-and-provider', [ExpensesController::class, 'isValidInvoiceAndProvider']);
        Route::apiResource('expenses-template', ExpenseTemplateController::class);
        Route::post('expenses-template/delete', [ExpenseTemplateController::class, 'delete']);
        Route::apiResource('categories', ExpenseCategoriesController::class);

        // Payments
        //----------------------------------
        Route::post('/payments/{payment}/send', SendPaymentController::class);
        Route::post('/payments/delete', [PaymentsController::class, 'delete']);
        Route::apiResource('payments', PaymentsController::class);
        Route::apiResource('payment-methods', PaymentMethodsController::class);
        Route::get('/payment-methods-pos-money', [PaymentMethodsController::class, 'getPaymentModesCorePosMoney']);
        Route::apiResource('failed-payment-history', FailedPaymentHistoryController::class);
        Route::post('/paypal-save-payment', [PaymentsController::class, 'savePaymentPaypal']);

        Route::get('/payment-methods-active', [PaymentMethodsController::class, 'paymentMethodActive'])->name('payment-methods-active');

        // Payments Multiple
        //----------------------------------
        Route::post('/payments/multiple/create', [PaymentsController::class, 'addMultiplePayment']);
        Route::get('/payments/multiple/show/{payment_id}', [PaymentsController::class, 'showMultiplePayment']);
        Route::post('/payments/multiple/get-payment-methods', [PaymentsController::class, 'getPaymentMethods']);

        // Paypal
        //----------------------------------
        /*Route::get('/paypal-token', function () {
            $gateway = new Braintree\Gateway([
                'environment' => config('services.braintree.environment'),
                'merchantId'  => config('services.braintree.merchantId'),
                'publicKey'   => config('services.braintree.publicKey'),
                'privateKey'  => config('services.braintree.privateKey')
            ]);

            $token = $gateway->ClientToken()->generate();

            return $token;
        });*/
        Route::post('/paypal-checkout', [AuthorizeController::class, 'paypalCheckout']);

        Route::post('/payment-paypalpro', [PaymentPaypalProController::class, 'payment']);

        // Custom fields
        //----------------------------------
        Route::resource('custom-fields', CustomFieldsController::class);

        // Backup & Disk
        //----------------------------------
        Route::apiResource('backups', BackupsController::class);
        Route::apiResource('/disks', DiskController::class);
        Route::get('download-backup', DownloadBackupController::class);
        Route::get('/disk/drivers', [DiskController::class, 'getDiskDrivers']);

        // Settings
        //----------------------------------
        Route::put('/me', [CompanyController::class, 'updateProfile']);
        Route::get('/me/settings', GetUserSettingsController::class);
        Route::put('/me/settings', UpdateUserSettingsController::class);
        Route::post('/me/upload-avatar', [CompanyController::class, 'uploadAvatar']);
        Route::put('/company', [CompanyController::class, 'updateCompany']);
        Route::post('/company/upload-logo', [CompanyController::class, 'uploadCompanyLogo']);
        // upload-wallpaper
        Route::post('/company/upload-wallpaper', [CompanyController::class, 'uploadWallpaper']);
        Route::get('/company/settings', GetCompanySettingsController::class);
        Route::post('/company/settings', UpdateCompanySettingsController::class);
        Route::post('/company/upload-page-title', [CompanyController::class, 'uploadCompanyPageTitle']);
        Route::post('/company/upload-favicon', [CompanyController::class, 'uploadCompanyFavicon']);

        // settings - mobile
        Route::post('/mobile-settings', [MobileSettings::class, 'store']);
        Route::get('/mobile-settings/{idCompany}', [MobileSettings::class, 'show']);
        Route::post('/mobile/logs/login', [LogsController::class, 'store']);
        Route::post('/mobile/logs/login/list', [LogsController::class, 'index']);
        Route::post('/mobile/messaging/customer/list', [LogsController::class, 'indexCustomersMessaging']);
        Route::post('/mobile/logs/notifications/list', [PushNotificationsController::class, 'indexLogs']);
        Route::post('/mobile/notification/push', [PushNotificationsController::class, 'sendToDevice']);

        // settings - retentions
        Route::apiResource('/retentions', RetentionsController::class);
        Route::get('/retention/{id}', [RetentionsController::class, 'show']);
        Route::post('/retentions/delete/{id}', [RetentionsController::class, 'destroy']);
        Route::put('/retentions/update', [RetentionsController::class, 'update']);

        // modules
        Route::get('/modules', [ModulesController::class, 'getModules']);
        Route::get('/pbx/jobs/logs', [PbxHostedManage::class, 'getPbxJobsLogs']);
        // modules -add ons
        Route::get('/add-ons', [ModulesController::class, 'getAddOns']);
        Route::post('/add-ons/update/{id}', [ModulesController::class, 'updateAddOnStatus']);

        // pbx servers
        Route::get('/pbx/servers', [PbxServersController::class, 'index']);
        Route::post('/pbx/servers/insert', [PbxServersController::class, 'store']);
        Route::put('/pbx/servers/update/{id}', [PbxServersController::class, 'update']);
        Route::delete('/pbx/servers/delete/{id}', [PbxServersController::class, 'destroy']);
        Route::get('/pbx/servers/{id}', [PbxServersController::class, 'show']);

        // pbx packages
        Route::get('/pbx/packages/{id}/services', [PbxPackagesController::class, 'getPbxServicesRelation']);
        Route::get('/pbx/packages/list/{idCustomer}', [PbxPackagesController::class, 'index']);
        Route::get('/pbx/packages/list/createpbx/{idCustomer}', [PbxPackagesController::class, 'indexPBX']);
        Route::post('/pbx/packages/insert', [PbxPackagesController::class, 'store']);
        Route::put('/pbx/packages/update/{id}', [PbxPackagesController::class, 'update']);
        Route::post('/pbx/packages/delete/{id}', [PbxPackagesController::class, 'destroy']);
        Route::get('/pbx/packages/{id}', [PbxPackagesController::class, 'show']);
        Route::get('/pbx/packages-param/list-param/{idCustomer}', [PbxPackagesController::class, 'index']);

        // custom Search
        Route::get('/custom-search/pbx-tenant', [CustomSearchController::class, 'pbxTenants']);
        Route::get('/custom-search/pbx-tenant-service', [CustomSearchController::class, 'pbxTenantsservice']);
        Route::get('/custom-search/reports-cdr', CustomSearchReportController::class);

        //Route::post('/custom-search/reports-cdr', [CustomSearchController::class, 'reportCDR']);
        Route::post('/custom-search/extension', [CustomSearchController::class, 'pbxExtensions']);
        Route::resource('custom-search', CustomSearchController::class);

        // pbx services
        Route::post('/pbx/services/insert/', [PbxServicesController::class, 'store']);
        Route::post('/pbx/services/update/', [PbxServicesController::class, 'update']);
        Route::get('/pbx/ware/tenant/{idPbxPackage}', [PbxServicesController::class, 'listTenant']);
        Route::get('/pbx/ware/packageinfo/{idPbxPackage}', [PbxServicesController::class, 'packageInfo']);
        Route::get('/pbx/ware/prefixrate/groups', [PbxServicesController::class, 'listPrefixRateGroups']);
        Route::get('/pbx/ware/ext', [PbxServicesController::class, 'listExtByTenant']);
        Route::get('/pbx/ware/did', [PbxServicesController::class, 'listDIDByTenant']);

        Route::post('/pbx/services/ext/insert', [PbxServicesController::class, 'storePbxServiceExtensions']);
        Route::post('/pbx/services/did/insert', [PbxServicesController::class, 'storePbxServiceDID']);

        Route::post('/pbx/services/daysToRenewal/', [PbxServicesController::class, 'getDaysToRenewal']);

        Route::post('/pbx/services/additem', [PbxServicesController::class, 'addPbxServiceItem']);

        Route::get('/pbx/services/ext/{idPbxService}', [PbxServicesController::class, 'showEXTListByService']);
        Route::get('/pbx/services/did/{idPbxService}', [PbxServicesController::class, 'showDIDListByService']);
        //Prueba
        Route::get('/pbx/services/taxtype/{idPbxTaxType}', [PbxServicesController::class, 'showPbxTaxType']);

        Route::get('/pbx/services/includes/insert', [PbxServicesController::class, 'storeIncludeLists']);

        // pbx tenant
        Route::post('/pbx/tenant/insert/', [PbxTenantController::class, 'store']);
        Route::get('/pbx/tenant/{tenantId}', [PbxTenantController::class, 'show']);
        Route::get('/pbx/tenantall', [PbxTenantController::class, 'tenantsAll']);
        Route::apiResource('/pbx/tenant', PbxTenantController::class);

        // pbx extension
        Route::post('/pbx/ext/insert', [PbxExtensionsController::class, 'store']);
        Route::get('/pbx/ext/{extName}', [PbxExtensionsController::class, 'show']);

        // pbx did
        Route::post('/pbx/did/insert', [PbxDIDController::class, 'store']);
        Route::get('/pbx/did/{didNumber}', [PbxDIDController::class, 'show']);

        // Core POS
        Route::resource('pos/store', StoreController::class);
        Route::post('pos/store/delete', [StoreController::class, 'deleteStore']);
        Route::post('pos/get-stores', [StoreController::class, 'getStores']);
        // Routes grouped.
        Route::group(
            [],
            base_path('routes/groups/serviceDetails.php'),
        );
        Route::group(
            [],
            base_path('routes/groups/tenantCdr.php'),
        );
        Route::prefix('modules')->as('modules:')->group(
            base_path('routes/groups/modules.php'),
        );
        Route::prefix('avalara')->as('avalara:')->group(
            base_path('routes/groups/avalara.php'),
        );
        Route::prefix('invoices')->as('avalara.invoice:')->group(
            base_path('routes/groups/avalaraInvoice.php'),
        );
        Route::get('/avalara-service-types/{code}', [ItemsController::class, 'avalaraServiceTypes']);

        //Jobs
        Route::get('/pbx/service/{pbxService}/jobs/import-cdr', [PbxServiceJobsController::class, 'index']);
        Route::delete('/pbx/service/{pbxService}/jobs/import-cdr/delete', [PbxServiceJobsController::class, 'destroy']);
        Route::delete('/pbx/service/{pbxService}/jobs/import-cdr/delete-old-way', [PbxServiceJobsController::class, 'fullDestroy']);


        // profile extension
        Route::get('/profile/extensions', [ProfileExtensionsController::class, 'index']);
        Route::get('/profile/extensions/np', [ProfileExtensionsController::class, 'indexnp']);
        Route::post('/profile/extensions/insert', [ProfileExtensionsController::class, 'store']);
        Route::put('/profile/extensions/update/{id}', [ProfileExtensionsController::class, 'update']);
        Route::post('/profile/extensions/delete/{id}', [ProfileExtensionsController::class, 'destroy']);
        Route::get('/profile/extensions/{id}', [ProfileExtensionsController::class, 'show']);

        // aditional charges
        Route::get('/aditional-charges', [AditionalChargesController::class, 'index']);

        // profile DID
        Route::get('/profile/did', [ProfileDIDController::class, 'index']);
        Route::get('/profile/did/np', [ProfileDIDController::class, 'indexnp']);
        Route::post('/profile/did/insert', [ProfileDIDController::class, 'store']);
        Route::put('/profile/did/update/{id}', [ProfileDIDController::class, 'update']);
        Route::post('/profile/did/delete/{id}', [ProfileDIDController::class, 'destroy']);
        Route::get('/profile/did/{id}', [ProfileDIDController::class, 'show']);

        // profile DID-TOLL-FREE
        Route::get('/profile/did-toll-free', [ProfileDidTollFreeController::class, 'index']);
        Route::post('/profile/did-toll-free/insert', [ProfileDidTollFreeController::class, 'store']);
        Route::put('/profile/did-toll-free/update/{id}', [ProfileDidTollFreeController::class, 'update']);
        Route::post('/profile/did-toll-free/delete/{id}', [ProfileDidTollFreeController::class, 'destroy']);
        Route::get('/profile/did-toll-free/{id}', [ProfileDidTollFreeController::class, 'show']);
        Route::apiResource('categories-toll-free', PbxCategorieController::class);
        Route::post('/categories-toll-free/delete/{id}', [PbxCategorieController::class, 'destroy']);

        // Custom App Rate
        // package-associate-custom-app-rate
        Route::get('/profile/package-associate-custom-app-rate', [CustomAppRateController::class, 'packageAssociateCustomAppRate']);
        Route::apiResource('/profile/custom-app-rate', CustomAppRateController::class);

        // profile INTERNACIONAL RATE
        /* Route::get('/profile/international-rate', [ProfileInternacionalRateController::class, 'index']);
        Route::post('/profile/international-rate/insert', [ProfileInternacionalRateController::class, 'store']);
        Route::put('/profile/international-rate/update/{id}', [ProfileInternacionalRateController::class, 'update']);
        Route::post('/profile/international-rate/delete/{id}', [ProfileInternacionalRateController::class, 'destroy']);
        Route::get('/profile/international-rate/{id}', [ProfileInternacionalRateController::class, 'show']); */
        Route::post('/profile/international-rate/delete', [ProfileInternacionalRateController::class, 'delete']);
        Route::get('/profile/international-rate/prefix-rate', [ProfileInternacionalRateController::class, 'loadPrefixRate']);
        Route::get('/profile/international-rate/csv', [ProfileInternacionalRateController::class, 'export']);
        Route::post('/profile/international-rate/import-excel', [ProfileInternacionalRateController::class, 'importCsv']);
        Route::apiResource('/profile/international-rate', ProfileInternacionalRateController::class);


        // update and delete (prefix "prefix group")
        Route::put('/profile/update-prefix-international', [ProfileInternacionalRateController::class, 'updatePrefixInternational']);
        Route::post('/profile/delete-prefix-international', [ProfileInternacionalRateController::class, 'deletePrefixInternational']);
        // modify Select and All
        Route::post('/profile/modify-selected-prefix-international', [ProfileInternacionalRateController::class, 'modifySelected']);
        Route::post('/profile/modify-all-prefix-international', [ProfileInternacionalRateController::class, 'modifyAll']);


        // Tickets departaments
        Route::post('/ticket/departaments/delete/{id}', [TicketDepartamentController::class, 'destroy']);
        Route::get('/ticket/departaments/list-users', [TicketDepartamentController::class, 'getUsers']);
        Route::get('/ticket/departaments/{department}/users', [TicketDepartamentController::class, 'getUsersByDepartment']);
        Route::get('/ticket/departaments/{department}/tickets', [TicketDepartamentController::class, 'getTicketsByDepartment']);
        Route::apiResource('/ticket/departaments', TicketDepartamentController::class);

        // Mails
        //----------------------------------
        Route::get('/mail/drivers', [MailConfigurationController::class, 'getMailDrivers']);
        Route::get('/mail/config', [MailConfigurationController::class, 'getMailEnvironment']);
        Route::post('/mail/config', [MailConfigurationController::class, 'saveMailEnvironment']);
        Route::post('/mail/test', [MailConfigurationController::class, 'testEmailConfig']);
        Route::apiResource('notes', NotesController::class);

        // Tax Agency
        //----------------------------------
        Route::apiResource('tax-agency', TaxAgencyController::class);

        // Tax Categories
        //----------------------------------
        Route::apiResource('tax-categories', TaxCategoryController::class);

        // Tax Types
        //----------------------------------
        Route::apiResource('tax-types', TaxTypesController::class);

        // Users
        //----------------------------------
        Route::post('/users/delete', [UsersController::class, 'delete']);
        Route::get('/users/users-admin', [UsersController::class, 'getUserAdmin']);
        Route::get('/users/show/{id}', [UsersController::class, 'showById']);
        Route::get('/users/estimates-assigned/{id}', [UsersController::class, 'estimatesAssignedUser']);
        Route::post('/users/update-permissions/{id}', [UsersController::class, 'updatePermissions']);
        Route::apiResource('/users', UsersController::class);

        // Roles
        //----------------------------------
        Route::post('/assign-role', [RoleController::class, 'assignRole']);
        Route::get('/permissions', [RoleController::class, 'permissions']);
        Route::apiResource('/roles', RoleController::class);

        // Packages
        //----------------------------------
        Route::delete('/destroy-item-packages-group', [PackagesController::class, 'destroyItemPackagesGroup']);
        Route::get('/add-groups', [PackagesController::class, 'addNewItemGroups']);
        Route::get('/packages/tax-groups', [PackagesController::class, 'packageTaxGroups']);
        Route::get('/packages/groups', [PackagesController::class, 'packageGroups']);
        Route::get('/packages/item-groups', [PackagesController::class, 'listItemGroups']);
        Route::get('/packages/item-groups-pos', [PackagesController::class, 'listItemGroupsPos']);
        Route::get('/packages-by-group', [PackagesController::class, 'getPackagesByGroups']);
        Route::post('/packages/add-groups', [PackagesController::class, 'addGroups']);
        Route::apiResource('/packages', PackagesController::class);
        Route::get('/packages-services/{id}/services', [PackagesController::class, 'getPackagesServices']);

        // Items Groups
        //----------------------------------
        Route::post('/item-groups/delete', [ItemGroupController::class, 'delete']);
        Route::post('/item-groups/upload-picture', [ItemGroupController::class, 'uploadPicture']);
        Route::apiResource('/item-groups', ItemGroupController::class);

        // Package groups
        //----------------------------------
        Route::post('/groups/delete', [PackageGroupController::class, 'delete']);
        Route::get('/groups/packages', [PackageGroupController::class, 'packages']);
        Route::post('/groups/add-packages', [PackageGroupController::class, 'addPackages']);
        Route::apiResource('/groups', PackageGroupController::class);

        // Tax groups
        //----------------------------------
        Route::post('/tax-groups/delete', [TaxGroupController::class, 'delete']);
        Route::get('/tax-groups/taxes', [TaxGroupController::class, 'taxes']);
        Route::apiResource('/tax-groups', TaxGroupController::class);

        // Logs
        //----------------------------------
        Route::get('/module-logs', [ModuleLogsController::class, 'index']);
        Route::get('/module-logs/search-lists', [ModuleLogsController::class, 'getSearchLists']);
        Route::get('/email-logs', [EmailLogsController::class, 'index']);

        // Providers
        //----------------------------------
        Route::post('/providers/delete', [ProviderController::class, 'delete']);
        Route::apiResource('/providers', ProviderController::class);
        Route::post('/providers/set-prefix', [ProviderController::class, 'setPrefix']);
        Route::get('/providers-select', [ProviderController::class, 'indexselectprovider']);

        // Items Categories
        //----------------------------------
        Route::resource('/items-categories', ItemCategoriesController::class);

        // modules
        Route::get('/modules', [ModulesController::class, 'getModules']);

        // Payment Gateways
        //--------------
        Route::get('/payment-gateways', [PaymentGatewayController::class, 'getPaymentGateways']);
        Route::get('/payment-gateways-ach', [PaymentGatewayController::class, 'getPaymentGatewaysAch']);
        Route::post('/gateways-index', [PaymentGatewayController::class, 'gatewaysIndex']);
        Route::get('/payment-gateways/change-status/{id}', [PaymentGatewayController::class, 'changeStatus']);
        Route::post('/payment-gateways/change-default', [PaymentGatewayController::class, 'changeDefault']);

        // Authorize Settings
        //--------------
        Route::post('/authorize-settings/delete', [AuthorizeSettingsController::class, 'delete']);
        Route::apiResource('/authorize-settings', AuthorizeSettingsController::class);
        Route::post('/authorize-settings/change-status', [AuthorizeSettingsController::class, 'changeStatus']);
        Route::put('authorize-settings/set-default/{id}', [AuthorizeSettingsController::class, 'setAuthorizeDefault']);
        Route::put('authorize-settings/check-Authorize', [AuthorizeSettingsController::class, 'checkAuthorize']);


        // Authorize
        //--------------
        Route::post('/authorize-charge', [AuthorizeController::class, 'charge']);
        Route::post('/authorize-save', [AuthorizeController::class, 'saveCharge']);
        Route::post('/authorize-void', [AuthorizeController::class, 'void']);
        Route::post('/authorize-refunded', [AuthorizeController::class, 'refunded']);
        Route::post('/authorize-ach', [AuthorizeController::class, 'ach']);
        Route::post('/authorize-save-ach', [AuthorizeController::class, 'saveChargeACH']);
        Route::post('/authorize-paypal', [AuthorizeController::class, 'paypal']);

        // Services
        //----------------------------------

        Route::post('/services/delete', [ServiceController::class, 'delete']);
        Route::get('/services-all', [ServiceController::class, 'servicesAll']);
        Route::apiResource('/services', ServiceController::class);
        Route::get('/services/inv/{customer_package_id}', [ServiceController::class, 'invoicesPerService']); //done

        // My route
        //Route::get('/services-all', [ServiceController::class, 'servicesAll']);
        //Route::get('/pbx/service-detail/ext/{pbx_service_id}', [PbxServiceDetailController::class, 'serviceDetailExtensions']); //done

        // Services
        Route::prefix('services')->group(function () {
            Route::get('/{service}/invoices', [ServiceInvoiceController::class, 'index'])->name('service.invoice.index');
        });

        // Verify
        Route::prefix('verify')->group(function () {
            Route::post('/document', [DocumentVerificationController::class, 'verifyDocument']);
            Route::post('/selfie', [DocumentVerificationController::class, 'selfie']);
        });


        // Payment Accounts
        //----------------------------------
        Route::post('/payment-accounts/{id}/default-pay-account', [PaymentAccountController::class, 'defaultPayAccount']);
        Route::post('/payment-accounts/delete', [PaymentAccountController::class, 'delete']);
        Route::apiResource('payment-accounts', PaymentAccountController::class)->except('create', 'edit', 'delete');

        // Payment Accounts
        //----------------------------------
        Route::post('/payment-accounts-customer/{id}/default-pay-account', [PaymentAccountCustomerController::class, 'defaultPayAccount']);
        Route::post('/payment-accounts-customer/delete', [PaymentAccountCustomerController::class, 'delete']);
        Route::apiResource('payment-accounts-customer', PaymentAccountCustomerController::class)->except('create', 'edit', 'delete');

        // Paypal Settings
        //----------------------------------
        Route::post('/paypal-settings/delete', [PaypalSettingsController::class, 'delete']);
        Route::apiResource('/paypal-settings', PaypalSettingsController::class)->except('delete');
        Route::post('/paypal-settings/change-status', [PaypalSettingsController::class, 'changeStatus']);
        Route::post('/paypal-settings/public-key-paypal', [PaypalSettingsController::class, 'getPublicKeyPaypal']);



        // Stripe Settings
        //----------------------------------
        // stripe-settings/default
        Route::get('/stripe-settings/default', [StripeSettingController::class, 'getDefaultSetting']);
        Route::get('/stripe/request-ids', [StripeSettingController::class, 'requestIds']);
        Route::apiResource('/stripe-settings', StripeSettingController::class);

        // Prefixes Groups
        //----------------------------------
        Route::post('/prefix-groups/delete', [PrefixGroupController::class, 'delete']);
        Route::apiResource('/prefix-groups', PrefixGroupController::class);
        Route::get('/prefix-groups-new', [PrefixGroupController::class, 'showNew']);

        // Custom did groups
        // --------------------------------
        Route::post('/custom-did-groups/import-parse', [CustomDidGroupController::class, 'importParse']);
        Route::post('/custom-did-groups/import-process', [CustomDidGroupController::class, 'importProcess']);
        Route::post('/custom-did-groups/delete', [CustomDidGroupController::class, 'delete']);
        Route::get('/custom-did-groups/export-process', [CustomDidGroupController::class, 'exportProcess']);
        Route::apiResource('/custom-did-groups', CustomDidGroupController::class);

        Route::get('/user-admission-list', [UserAdmissionListController::class, 'index']);

        // Reports
        Route::prefix('reports')->group(function () {

            // report for tax summary csvs
            //----------------------------------
            Route::get('/tax-summary-csv', [TaxSummaryReportController::class, 'exportCsv']);

        });

        Route::post('/bandwidth/delete', [BandwidthController::class, 'delete']);
        Route::post('/bandwidth/update-default', [BandwidthController::class, 'updateDefault']);
        Route::apiResource('/bandwidth', BandwidthController::class);

        // Coreposhistory
        // --------------------------------

        Route::get('/corepos-history-index', [CorePosHistoryController::class, 'index']);

        // payment fess
        // --------------------------------

        Route::apiResource('/payment-fees', PaymentGatewaysFeeController::class);
        Route::post('/payment-fees/delete', [PaymentGatewaysFeeController::class, 'delete']);

    });

    // Common Admin and Customer
    Route::middleware(['auth:sanctum', 'commonuser'])->group(function () {

        // Search users
        //----------------------------------
        Route::get('/search', SearchController::class);

        // Settings
        //----------------------------------
        Route::get('/me', [CompanyController::class, 'getUser']);

        // Users
        //----------------------------------
        Route::post('/get-user-permission', [UsersController::class, 'getUserPermission']);
    });

    // Only Customer
    Route::middleware(['auth:sanctum', 'customer'])->group(function () {

        Route::prefix('customer')->group(
            base_path('routes/groups/customer.php'),
        );

        //Estimates Customer
        Route::apiResource('estimates-customer', EstimateCustomersController::class);
        //Payment Customer
        Route::apiResource('payments-customer', PaymentCustomersController::class);

    });

});

Route::prefix('/v2')->middleware(['auth:sanctum'])->group(function () {
    #Multi Purpose Payment
    Route::post('payments/multiple', V2\Invoice\MultiInvoicePaymentController::class)->name('generic.multi-payment');
    Route::post('payments/{invoice}', V2\Payments\MultiPurposeInvoicePaymentController::class)->name('generic.payment-invoice')->where('invoice', '[0-9]+');
    Route::post('payments/', V2\Payments\MultiPurposeRechargePaymentController::class)->name('generic.payment-recharge');
    Route::get('payments/{payment}/associated', V2\Payments\PaymentsAssociatedController::class)->name('payments.associated');

    Route::post('payments/{payment}/void', V2\Payments\PaymentVoidController::class)->name('generic.void');
    Route::post('payments/{payment}/refund', V2\Payments\PaymentRefundController::class)->name('refund.void');
    Route::get('reports/invoices/csv', V2\Reports\Invoices\CsvController::class)->name('invoice.csv');
    Route::post('invoices/import/csv', V2\Invoice\InvoiceImportController::class)->name('invoices.import.csv');

    //PbxService
    Route::post('pbx-services/{pbxService}/pbx-extensions', [V2\CorePbx\Service\PbxExtensionController::class, 'store'])->name('pbx-services.pbx-extensions');
    Route::post('pbx-services/{pbxService}/pbx-did', [V2\CorePbx\Service\PbxDidController::class, 'store'])->name('pbx-services.pbx-extensions');
    Route::get('pbx-services/{pbxService}/pbx-trunks', [V2\CorePbx\Service\PbxTrunkController::class, 'index'])->name('pbx-services.pbx-trunks');


    Route::get('/pbx-server/{pbxServer}/tenant-packages', V2\CorePbx\GetTenantPackageController::class)->name('pbx-server.tenant-packages');
    Route::get('/pbx-server/{pbxServer}/routes', V2\CorePbx\GetRoutesController::class)->name('pbx-server.routes');
    Route::get('/pbx-server/{pbxServer}/tenant-codes', V2\CorePbx\GetTenantCodeController::class)->name('pbx-server.routes');
    Route::get('/pbx-server/{pbxServer}/did-groups', [V2\CorePbx\PbxDidGroupsController::class, 'index'])->name('pbx-server.did-groups');

    //Route::get('/pbx-server/{pbxServer}/tenants', [V2\CorePbx\PbxServerTenantController::class, 'index'])->name('pbx-server.tenant.index');
    Route::get('/pbx-server-tenants', [V2\CorePbx\PbxServerTenantController::class, 'index'])->name('pbx-server.tenant.index');
    Route::get('/pbx-server-tenants/{pbxServerTenant}', [V2\CorePbx\PbxServerTenantController::class, 'show'])->name('pbx-server.tenant.index');
    Route::post('/pbx-server/{pbxServer}/tenants', V2\CorePbx\PbxServerTenantStoreController::class)->name('pbx-server.tenant.store');

    //Dashboard
    Route::get('/pbx-server-tenants/{tenant}/pbx-services', [V2\CorePbx\ServerTenant\PbxServicesController::class, 'index'])->name('pbx-server.tenant.services.index');
    Route::get('/pbx-server-tenants/{tenant}/pbx-services/{service}', [V2\CorePbx\ServerTenant\PbxServicesController::class, 'show'])->name('pbx-server.tenant.services.show');
    Route::get('/pbx-server-tenants/{tenant}/pbx-extensions', [V2\CorePbx\ServerTenant\PbxExtensionController::class, 'index'])->name('pbx-server.tenant.ext.index');
    Route::get('/pbx-server-tenants/{tenant}/pbx-extensions/{extension}', [V2\CorePbx\ServerTenant\PbxExtensionController::class, 'show'])->name('pbx-server.tenant.ext.show');
    Route::put('/pbx-server-tenants/{tenant}/pbx-extensions/{extension}', [V2\CorePbx\ServerTenant\PbxExtensionController::class, 'update'])->name('pbx-server.tenant.ext.update');
    Route::get('/pbx-server-tenants/{tenant}/user-agent-devices', V2\CorePbx\ServerTenant\GetUserAgentDeviceController::class)->name('pbx-server.user-agents-devices');


    Route::get('/pbx-server-tenants/{tenant}/pbx-did/destination-types/{type}', [V2\CorePbx\ServerTenant\PbxDidDestinationTypesController::class, 'index'])
        ->name('pbx-server.tenant.did.types')->where('type', '^(extension|forward-did|ring-groups|ivr|queues|external_number|ivr_tree)');
    Route::get('/pbx-server-tenants/{tenant}/pbx-did', [V2\CorePbx\ServerTenant\PbxDidController::class, 'index'])->name('pbx-server.tenant.did.index');
    Route::get('/pbx-server-tenants/{tenant}/pbx-did/{did}', [V2\CorePbx\ServerTenant\PbxDidController::class, 'show'])->name('pbx-server.tenant.did.show');
    Route::put('/pbx-server-tenants/{tenant}/pbx-did/{did}', [V2\CorePbx\ServerTenant\PbxDidController::class, 'update'])->name('pbx-server.tenant.did.show');


    Route::get('/pbx-server-tenants/{tenant}/pbx-app-rates', [V2\CorePbx\ServerTenant\PbxAppRateController::class, 'index'])->name('pbx-server.tenant.app-rate.index');
    Route::get('/pbx-server-tenants/{tenant}/pbx-app-rates/{appRates}', [V2\CorePbx\ServerTenant\PbxDidController::class, 'show'])->name('pbx-server.tenant.app-rate.show');

    Route::put('/pbx-server-tenants/{tenant}/complete', [V2\CorePbx\PbxServerTenantIncompleteController::class, 'destroy'])->name('pbx-server.tenant.complete');
    //Route::get('/pbx-server-tenants/{tenant}/mail', [V2\CorePbx\PbxServerTenantIncompleteController::class, 'destroy'])->name('pbx-server.tenant.index');

    Route::get('/pbx-server-tenants/{tenant}/mail', function (PbxServerTenant $tenant) {
        //return (new \Crater\Notifications\PbxServerTenantPendingActivationNotification($tenant))->toMail(User::first());
        return new \Crater\Mail\TenantPendingActivationMail($tenant);
    })->name('ting');


    //    Route::get('/tenants/incomplete', V2\CorePbx\PbxServerTenantStoreController::class)->name('pbx-server.tenant.store');
    //    Route::post('/tenants/complete', V2\CorePbx\PbxServerTenantStoreController::class)->name('pbx-server.tenant.store');


});
