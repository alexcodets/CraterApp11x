import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'

/*
 |--------------------------------------------------------------------------
 | Views
 |--------------------------------------------------------------------------|
 */


//  Avalara
import AvalaraIndex from './views/avalara/Index.vue'
import AvalaraConfigCreate from './views/avalara/Create.vue'
import AvalaraLogs from './views/avalara/Logs/index.vue'
import AvalaraGlossary from './views/avalara/Glossary.vue'
//  Packages
import PackageIndex from './views/packages/Index.vue'
import PackageCreate from './views/packages/Create.vue'
import PackageView from './views/packages/View.vue'

// Services
import SrvicesIndex from './views/services/Index.vue'

// Layouts
import LayoutBasic from './views/layouts/LayoutBasic.vue'
import LayoutCustomer from './views/layouts/LayoutCustomer.vue'
import LayoutLogin from './views/layouts/LayoutLogin.vue'
import LayoutWizard from './views/layouts/LayoutWizard.vue'

// Auth
import Login from './views/auth/Login.vue'
import LoginCustomer from './views/auth/LoginCustomer.vue'
import ForgotPassword from './views/auth/ForgotPassword.vue'
import ResetPassword from './views/auth/ResetPassword.vue'
import Register from './views/auth/Register.vue'

import NotFoundPage from './views/errors/404.vue'

// Dashboard
import Dashboard from './views/dashboard/Dashboard.vue'
import DashboardCustomer from './views/dashboard/DashboardCustomer.vue'

// Customers
import CustomerIndex from './views/customers/Index.vue'
import CustomerDisableList from './views/customers/customerDisableList.vue'
import CustomerCreate from './views/customers/Create.vue'
import CustomerView from './views/customers/View.vue'
import CustomerAddress from './views/customers/Address.vue'
/* C:\xampp\htdocs\CorebillCrater\resources\assets\js\components\base\modal\CustomerAddressModal.vue*/
import CustomerAddAddress from './views/customers/CreateAddress.vue'
import CustomerContacts from './views/customers/Contacts.vue'
import CustomerAddContacts from './views/customers/CreateContacts.vue'
import CustomerCredit from './views/customers/CustomerCredit.vue';
import CustomerAddPackage from './views/customers/CreatePackage.vue'
import CustomerNote from './views/customers/Note.vue'
import CustomerViewNote from "./views/customers/ViewNote";
import CustomerAddNote from './views/customers/CreateNote.vue'
import Customerticket from './views/customers/Ticket.vue'
import CustomerAddticket from './views/customers/CreateTicket.vue'
import CustomerPackageView from './views/customers/PackageView.vue'
import CustomerConfig from './views/customers/CustomerConfig.vue'
import CustomerAddCorepbxServices from './views/customers/CreateCorePbxServices.vue'
import CustomerPbxPackageView from './views/customers/PbxPackageView.vue'
import PbxServiceView from "./views/customers/PbxServiceView.vue";
import PbxServiceViewCallHistory from "./views/customers/SearchCallHistoryPbxServices/index"
// tickets
import TicketsCustomer from './views/tickets-customer/Index.vue';
import TicketsCustomerCreateEdit from './views/tickets-customer/CreateEdit.vue';
import TicketsCustomerView from './views/tickets-customer/View.vue';
import TicketsCustomerViewNote from './views/tickets-customer/ViewNote.vue';

import PackageViewCustomer from './views/customer-customer/PackageViewCustomer.vue'
import CustomerPbxServiceView from './views/customer-customer/PbxServiceView.vue'

// Items
import ItemsIndex from './views/items/Index.vue'
import ItemCreate from './views/items/Create.vue'

// Invoices
import InvoiceIndex from './views/invoices/Index.vue'
import InvoiceCreate from './views/invoices/Create.vue'
import InvoiceView from './views/invoices/View.vue'

import InvoiceV2Index from './views/invoices/v2/Index.vue'
import InvoiceV2IndexCustomer from './views/invoices/v2/IndexCustomer.vue'
import InvoiceV2ViewCustomer from './views/invoices/v2/ViewCustomer.vue'

// Payments
import PaymentsIndex from './views/payments/Index.vue'
import PaymentCreate from './views/payments/Create.vue'
import PaymentView from './views/payments/View.vue'

// Payments Multiple
import PaymentMultipleCreate from './views/payments/multiple/Create.vue'

// Payment Failed
import PaymentsFailedIndex from './views/payments/PaymentsFailedIndex.vue'

import PaymentCustomerIndex from './views/payment-customer/Index.vue'
import PaymentCustomerView from './views/payment-customer/View.vue'
import PaymentCustomerCreate from './views/payment-customer/Create.vue'
import PaymentCustomerAddCredit from './views/payment-customer/Addcredit.vue'

import InvoicePaymentWithoutLogin from './views/payment-customer/InvoicePaymentWithoutLogin.vue'

// Payment accounts customer
import PaymentAccountCustomerIndex from './views/payment-account-customer/Index.vue'
import PaymentAccountCustomerView from './views/payment-account-customer/View.vue'
import PaymentAccountCustomerCreate from './views/payment-account-customer/Create.vue'

// Estimates
import EstimateIndex from './views/estimates/Index.vue'
import EstimateCreate from './views/estimates/Create.vue'
import EstimateView from './views/estimates/View.vue'
import ApproveLink from './views/estimates/ApproveLink'

import EstimateCustomerIndex from './views/estimate-customer/Index.vue'
import EstimateCustomerView from './views/estimate-customer/View.vue'

// Expenses
import ExpensesIndex from './views/expenses/Index.vue'
import ExpenseCreate from './views/expenses/Create.vue'
import ExpenseView from './views/expenses/View.vue'
import ExpenseDocs from './views/expenses/Docs.vue'


// Expenses Template
import ExpensesTemplateIndex from './views/expenses/expense-template/Index.vue'
import ExpenseTemplateCreate from './views/expenses/expense-template/Create.vue'

//PBX
import CorePBX from './views/corePbx/Index.vue'
import PBXJobsLogs from './views/settings/modules/Logs/index.vue'

//POS
import CorePOS from './views/corePos/index.vue'
import CorePOSDashboard from './views/corePos/dashboard/index.vue'
//
import CashRegister from './views/corePos/Menu/CashRegister.vue'
import CashRegisterMain from './views/corePos/Menu/CashRegisterMain.vue'
import CreateCashRegister from './views/corePos/Menu/CreateCashRegister.vue'
import InformationCashRegister from './views/corePos/Menu/cash-register/CashRegisterInformation.vue'
import CashRegisterReport from './views/corePos/Menu/cash-register/CashRegisterReport.vue'
//
import Stores from './views/corePos/Menu/Stores.vue'
import StoreCreate from './views/corePos/store/Create.vue'
import StoreEdit from './views/corePos/store/Create.vue'
import Money from './views/corePos/Menu/Money.vue'
import Tables from './views/corePos/Menu/Tables.vue'
import TableCreate from './views/corePos/Menu/CreateTable.vue'

//PackagesPBX
import PackagesPBX from './views/corePbx/packages/Packages.vue'
import PackagesCreatePBX from './views/corePbx/packages/Create.vue'
import PackagesViewPBX from './views/corePbx/packages/View.vue'
//ExtensionPBX
import ExtensionsPBX from './views/corePbx/billing-templates/extensions/Extensions.vue'
import ExtensionsCreatePBX from './views/corePbx/billing-templates/extensions/Create.vue'
// CUSTOM APP RATE
import CustomAppRate from './views/corePbx/billing-templates/CustomAppRate'
import CustomAppRateCreateEdit from './views/corePbx/billing-templates/CustomAppRate/CreateEdit.vue'

// DIDPBX
import DIDsPBX from './views/corePbx/billing-templates/did/DIDs.vue'
import DIDsCreatePBX from './views/corePbx/billing-templates/did/Create.vue'
// DIDFREEPBX
import DIDFREEsPBX from './views/corePbx/billing-templates/did-free/DidTollFree.vue'
import DIDFREEsCreatePBX from './views/corePbx/billing-templates/did-free/Create.vue'

// Custom DID Groups
import CustomDidGroupIndexPbx from './views/corePbx/billing-templates/custom-did-group/Index'
import CustomDidGroupCreatePbx from './views/corePbx/billing-templates/custom-did-group/Create'
import CustomDidGroupViewPbx from './views/corePbx/billing-templates/custom-did-group/View.vue'

// INTERNACIONALRATEPBX
import InternacionalRatePBX from './views/corePbx/billing-templates/internacional-rate/InternacionalRate.vue'
import InternacionalRateCreatePBX from './views/corePbx/billing-templates/internacional-rate/Create.vue'
// DashBoard
import PBXwareDashboard from './views/corePbx/pbxware-dashboard/PbxwareDashboard.vue'

// Pbx Prefix Groups
import PrefixGroupsIndexPbx from './views/corePbx/billing-templates/prefix-groups/Index.vue'
import PrefixGroupCreatePbx from './views/corePbx/billing-templates/prefix-groups/Create.vue'
import PrefixGroupViewPbx from './views/corePbx/billing-templates/prefix-groups/View.vue'

// Tickets
import Ticket from './views/tickets/Index.vue'
//Departaments
import DepartamentsTicket from './views/tickets/departaments/Departaments.vue'
import DepartamentsCreateTicket from './views/tickets/departaments/Create.vue'
import DepartamentsTicketView from './views/tickets/departaments/View.vue'

// Tickets Principal
import PrincipalTicket from './views/tickets/tickets/Principal.vue'
import PrincipalAddticket from './views/tickets/tickets/CreateTickets.vue'
import PrincipalViewticket from './views/tickets/tickets/ViewTicket.vue'
import CreateNoteTicket from './views/tickets/tickets/CreateNote.vue'
import ViewNoteTicket from './views/tickets/tickets/ViewNote.vue'

// Email Templates
import EmailTicket from './views/tickets/email/Principal.vue'

// Bandwidth
import BandwidthModuleLayout from './views/bandwidth/Index.vue'
import BandwidthDesconnectModule from './views/bandwidth/disconnect/index.vue'
import BandwidthOrderingModule from './views/bandwidth/ordering/index.vue'
import BandwidthE911ManagementModule from './views/bandwidth/e911Management/index.vue'

// Customization
import CustomizationPBX from './views/corePbx/config/Customization.vue'

// TenantPBX
import TenantPBX from './views/corePbx/tenants/index.vue'

// TenantListPBX
import TenantListPBX from './views/corePbx/tenants-list/index.vue'
import TenantListCreatePBX from './views/corePbx/tenants-list/Create.vue'
import TenantListViewPBX from './views/corePbx/tenants-list/View.vue'

//Reports PBX
import ReportsPbxIndex from './views/corePbx/reports/Index.vue'
import DepartamentsPbx from './views/corePbx/reports/departaments/Departaments.vue'
import DepartamentsPbxAdd from './views/corePbx/reports/departaments/CreateCpDepatram.vue'
import ReportsPbx from './views/corePbx/reports/reportsPbx/Principal.vue'
import ReportsTenantsPbx from './views/corePbx/reports/reportsPbx/Tenants.vue'

//Reports PBX CUSTOMER
import ReportsPbxIndexCustomer from './views/customer-customer/reports/Index.vue'
import DepartamentsPbxCustomer from './views/customer-customer/reports/departaments/Departaments.vue'
import DepartamentsPbxAddCustomer from './views/customer-customer/reports/departaments/CreateCpDepatram.vue'
import ReportsPbxCustomer from './views/customer-customer/reports/reportsPbx/Principal.vue'

//Search
import SearchIndex from './views/customer-lead/Index.vue'
//Lead
import LeadIndex from './views/leads/Index.vue'
import LeadCreate from './views/leads/Create.vue'
import LeadView from './views/leads/View.vue'

//lead notes
import LeadNoteCreate from './views/lead-note/CreateNoteLead.vue'
import LeadNoteView from './views/lead-note/ViewLeadNote.vue'


//User
import UserIndex from './views/users/Index.vue'
import UserCreate from './views/users/Create.vue'
import UserView from './views/users/View/Index.vue'
import UserPermissions from './views/users/View/Permissions.vue'

// Report
import SalesReports from './views/reports/SalesReports'
import ExpensesReport from './views/reports/ExpensesReport'
import ProfitLossReport from './views/reports/ProfitLossReport'
import TaxReport from './views/reports/TaxReport.vue'
import ReportLayout from './views/reports/layout/Index.vue'

// Settings
import SettingsLayout from './views/settings/SettingsIndex.vue'
import CompanyInfo from './views/settings/CompanyInfoSetting.vue'
import Customization from './views/settings/CustomizationSetting.vue'
import Notifications from './views/settings/NotificationsSetting.vue'
import Preferences from './views/settings/PreferencesSetting.vue'
import UserProfile from './views/settings/UserProfileSetting.vue'
import TaxAgency from './views/settings/TaxAgencySetting.vue'
import TaxCategories from './views/settings/TaxCategoriesSetting.vue'
import TaxTypes from './views/settings/TaxTypesSetting.vue'
import Mobile from './views/settings/MobileSetting.vue'
import moduleMobileIndex from './views/settings/modules/Mobile/index.vue'
import moduleCorePosIndex from './views/settings/modules/Pos/index.vue'
import moduleMobileConfiguration from './views/settings/modules/Mobile/Menu/Configuration.vue'
import moduleCorePosConfiguration from './views/settings/modules/Pos/Menu/Configuration.vue'
import moduleMobileMessaging from './views/settings/modules/Mobile/Menu/Messaging.vue'
import moduleMobileLogin from './views/settings/modules/Mobile/Menu/Login.vue'
import moduleNotificationsLogs from './views/settings/modules/Mobile/Menu/NotificationsLogs.vue'
import NotesSetting from './views/settings/NotesSetting.vue'
import ExpenseCategory from './views/settings/ExpenseCategorySetting.vue'
import MailConfig from './views/settings/MailConfigSetting.vue'
import UpdateApp from './views/settings/UpdateAppSetting.vue'
import Backup from './views/settings/BackupSetting.vue'
import FileDisk from './views/settings/FileDiskSetting.vue'
import CustomFieldsIndex from './views/settings/CustomFieldsSetting.vue'
import PaymentMode from './views/settings/PaymentsModeSetting.vue'
import Wizard from './views/wizard/Wizard.vue'
import Logs from './views/settings/Logs.vue'
import Modules from './views/settings/ModuleSetting.vue'
import AddOns from './views/settings/AddOnsSetting.vue'
import PaymentGateway from './views/settings/PaymentGatewaySetting.vue'
// Settings retentions
import retentionsIndex from './views/settings/retentions/Index.vue'
import retentionsCreate from './views/settings/retentions/Create.vue'
import retentionsView from './views/settings/retentions/View.vue'

//Modules
import PBXEdit from './views/settings/modules/PbxEdit.vue'
import PBX from './views/settings/modules/PBX.vue'
import PbxAddServer from './views/settings/modules/PbxAddServer.vue'

import LogsIndex from './views/logs/index.vue'

// Option Groups
import ItemGroupsIndex from './views/item-groups/Index.vue'
import ItemGroupsCreate from './views/item-groups/Create.vue'
import ItemGroupsView from './views/item-groups/View.vue'

//Package groups
import PackageGroupIndex from './views/package-groups/Index.vue'
import PackageGroupCreate from './views/package-groups/Create.vue'
import PackageGroupView from './views/package-groups/View.vue'

//Tax groups
import taxGroupIndex from './views/tax-groups/Index.vue'
import taxGroupCreate from './views/tax-groups/Create.vue'
import taxGroupView from './views/tax-groups/View.vue'

//Providers
import providersIndex from './views/providers/Index.vue'
import providersCreate from './views/providers/Create.vue'
import providersView from './views/providers/View.vue'
//  Roles
import RolesIndex from './views/roles/Index.vue'
import RolesCreate from './views/roles/Create.vue'

// Payment account
import PaymentAccountIndex from './views/payment-account/Index.vue'
import PaymentAccountCreate from './views/payment-account/Create.vue'
import PaymentAccountView from './views/payment-account/View.vue'

//Authorize
import authorizeIndex from './views/authorize/Index.vue'
import authorizeCreate from './views/authorize/Create.vue'
import authorizeView from './views/authorize/View.vue'

// prefixCustomer
import prefixCustomer from './views/settings/prefixCustomer.vue'

//Paypal
import paypalIndex from './views/paypal/Index.vue'
import paypalCreate from './views/paypal/Create.vue'
import paypalView from './views/paypal/View.vue'

//Paypal
import stripeIndex from './views/stripe/Index.vue'
import stripeCreate from './views/stripe/Create.vue'
import stripeView from './views/stripe/View.vue'

//Auxpay
import auxpayIndex from './views/auxpax/Index.vue'
import auxpayCreate from './views/auxpax/Create.vue'
import auxpayView from './views/auxpax/View.vue'

// Bandwidth
import Bandwidth from "./views/settings/modules/Bandwidth/Bandwidth"
import BandwidthCreate from "./views/settings/modules/Bandwidth/Create"

//coreposhistories
import coreposhistoryIndex from './views/corePos/history/Index.vue'

//Customer Service
import CustomerServices from './views/customer-customer/services/Index.vue'

Vue.use(VueRouter)

const routes = [
    /*
       |--------------------------------------------------------------------------
       | Auth & Registration
       |--------------------------------------------------------------------------|
       */

    {
        path: '/',
        component: LayoutLogin,
        meta: {
            redirectIfAuthenticated: true
        },
        children: [{
                path: '/',
                component: Login,
            },
            {
                path: 'login',
                component: Login,
                name: 'login',
            },
            {
                path: 'account',
                component: LoginCustomer,
                name: 'login-customer',
            },
            {
                path: '/forgot-password',
                component: ForgotPassword,
                name: 'forgot-password',
            },
            {
                path: '/reset-password/:token',
                component: ResetPassword,
                name: 'reset-password',
            },
            {
                path: 'register',
                component: Register,
                name: 'register',
            },
        ],
    },

    /*
       |--------------------------------------------------------------------------
       | Onboarding
       |--------------------------------------------------------------------------|
       */
    {
        path: '/on-boarding',
        component: LayoutWizard,
        children: [{
            path: '/',
            component: Wizard,
            name: 'wizard',
        }, ],
    },

    /*
       |--------------------------------------------------------------------------
       | Estimates
       |--------------------------------------------------------------------------|
       */
    {
        path: '/approve-estimates/:unique_hash',
        component: ApproveLink,
        name: 'ApproveLink',
        meta: {
            redirectIfAuthenticated: false,
            requiresAuth: false,
        },
    },

    /*
       |--------------------------------------------------------------------------
       | Invoices payment without login
       |--------------------------------------------------------------------------|
       */
    {
        path: '/payments/invoices/:unique_hash/create',
        component: InvoicePaymentWithoutLogin,
        name: 'InvoicePaymentWithoutLogin',
        meta: {
            redirectIfAuthenticated: false,
            requiresAuth: false,
        },
    },

    /*
       |--------------------------------------------------------------------------
       | download PDF
       |--------------------------------------------------------------------------|
       */



    /*
       |--------------------------------------------------------------------------
       | Admin
       |--------------------------------------------------------------------------|
       */
    {
        path: '/admin',
        component: LayoutBasic,
        meta: {
            requiresAuth: true,
            isAdmin: true
        },
        children: [

            {
                path: 'pbx/packages/:id/view/detail',
                name: 'pbx.packages.view.detail',
                component: PackagesViewPBX,
            },

            // Dashboard
            {
                path: '/',
                component: Dashboard,
                name: 'dashboard',
            },
            {
                path: 'dashboard',
                component: Dashboard,
            },

            // Avalara
            {
                path: 'avalara/configs',
                name: 'avalara.index',
                component: AvalaraIndex,
            },
            {
                path: 'avalara/config/create',
                name: 'avalara.create',
                component: AvalaraConfigCreate,
            },
            {
                path: 'avalara/config/:id/edit',
                name: 'avalara.edit',
                component: AvalaraConfigCreate,
            },
            {
                path: 'avalara/config/logs',
                name: 'avalara.logs',
                component: AvalaraLogs,
            },
            {
                path: 'avalara/config/glossary',
                name: 'avalara.glossary',
                component: AvalaraGlossary,
            },
            // Module Mobile
            {
                path: 'module/billpay',
                component: moduleMobileIndex,
                children: [{
                        path: 'configuration',
                        name: 'mobile.configuration',
                        component: moduleMobileConfiguration,
                    },
                    {
                        path: 'messaging',
                        name: 'mobile.messaging',
                        component: moduleMobileMessaging,
                    },
                    {
                        path: 'logins',
                        name: 'mobile.login',
                        component: moduleMobileLogin,
                    },
                    {
                        path: 'notifications_logs',
                        name: 'notifications.logs',
                        component: moduleNotificationsLogs,
                    },
                ]
            },
            {
                path: 'corePOS/dashboard',
                name: 'corePOS.dashboard',
                component: CorePOSDashboard,
            },
            {
                path: 'module/corePOS',
                component: moduleCorePosIndex,
                children: [{
                        path: 'configuration',
                        name: 'corePOS.configuration',
                        component: moduleCorePosConfiguration,
                    },

                    // {
                    //     path: 'messaging',
                    //     name: 'mobile.messaging',
                    //     component: moduleMobileMessaging,
                    // },
                    // {
                    //     path: 'logins',
                    //     name: 'mobile.login',
                    //     component: moduleMobileLogin,
                    // },
                    // {
                    //     path: 'notifications_logs',
                    //     name: 'notifications.logs',
                    //     component: moduleNotificationsLogs,
                    // },
                ]
            },

            // Packages
            {
                path: 'packages',
                name: 'packages.index',
                component: PackageIndex,
            },
            {
                path: 'packages/create',
                name: 'packages.create',
                component: PackageCreate,
            },
            {
                path: 'packages/:id/view',
                name: 'packages.view',
                component: PackageView,
            },
            {
                path: 'packages/:id/edit',
                name: 'packages.edit',
                component: PackageCreate,
            },
            {
                path: 'packages/:id/copy',
                name: 'packages.copy',
                component: PackageCreate,
            },
            // services
            {
                path: 'services',
                name: 'services.index',
                component: SrvicesIndex,
            },

            // Customers
            {
                path: 'customers',
                component: CustomerIndex,
            },
            {
                path: 'customers/disable-list',
                component: CustomerDisableList,
            },
            {
                path: 'customers/create',
                name: 'customers.create',
                component: CustomerCreate,
            },
            {
                path: 'customers/:id/edit',
                name: 'customers.edit',
                component: CustomerCreate,
            },
            {
                path: 'customers/:id/view',
                name: 'customers.view',
                component: CustomerView,
            },
            {
                path: 'customers/:id/address',
                name: 'customers.address',
                component: CustomerAddress,
            },
            {
                path: 'customers/:id/add-address',
                name: 'customers.add-address',
                component: CustomerAddAddress,
            },
            {
                path: 'customers/:id/:idAddress/edit-address',
                name: 'customers.edit-address',
                component: CustomerAddAddress,
            },
            {
                path: 'customers/:id/contacts',
                name: 'customers.contacts',
                component: CustomerContacts,
            },
            {
                path: 'customers/:id/add-contact',
                name: 'customers.add-contact',
                component: CustomerAddContacts,
            },
            {
                path: 'customers/:id/edit-contact/:idContact',
                name: 'customers.edit-contact',
                component: CustomerAddContacts,
            },
            {
                path: 'customers/:id/credit',
                name: 'customers.credit',
                component: CustomerCredit,
            },
            {
                path: 'customers/:id/add-package',
                name: 'customers.add-package',
                component: CustomerAddPackage,
            },
            {
                path: 'customers/:id/add-corepbx-services',
                name: 'customers.add-corepbx-services',
                component: CustomerAddCorepbxServices,
            },
            {
                path: 'customers/:id/pbx-service/:customer_pbx_service_id/edit',
                name: 'pbxServices.edit',
                component: CustomerAddCorepbxServices,
            },
            /* {
                path: 'customers/:id/pbx-service/:customer_pbx_package_id/view',
                name: 'customers.pbx-package-view',
                component: CustomerPbxPackageView,
            }, */
            {
                path: 'customers/:id/note',
                name: 'customers.note',
                component: CustomerNote,
            },
            {
                path: 'customers/:id/add-note',
                name: 'customers.add-note',
                component: CustomerAddNote,
            },
            {
                path: 'customers/:id/:id1/edit-note',
                name: 'customers.edit-note',
                component: CustomerAddNote,
            },
            {
                path: 'customers/:id/:note_id/view-note',
                name: 'customers.view-note',
                component: CustomerViewNote,
            },
            {
                path: 'customers/:id/ticket',
                name: 'customers.ticket',
                component: Customerticket,
            },
            {
                path: 'customers/:id/add-ticket',
                name: 'customers.add-ticket',
                component: CustomerAddticket,
            },
            {
                path: 'customers/:id/:id1/edit-ticket',
                name: 'customers.edit-ticket',
                component: CustomerAddticket,
            },
            {
                path: 'customers/:id/service/:customer_package_id/view',
                name: 'customers.package-view',
                component: CustomerPackageView,
            },
            {
                path: 'customers/:id/pbx-service/:pbx_service_id/view',
                name: 'customers.pbx-service-view',
                component: PbxServiceView,
            },
            {
                path: 'customers/:id/service/:customer_package_id/edit',
                name: 'services.edit',
                component: CustomerAddPackage,
            },
            {
                path: 'customers/:idCustomer/pbx-service/:idPbxService/callHistory',
                name: 'customers.pbx-service-view-call-history',
                component: PbxServiceViewCallHistory,
            },
            {
                path: 'customers/:id/options',
                name: 'customers.options',
                component: CustomerConfig,
            },
            {
                path: 'customers/:id/payment-accounts',
                name: 'customers.payment-account',
                component: PaymentAccountIndex,
            },
            {
                path: 'customers/:id/payment-accounts/create-ACH',
                name: 'customers.payment-account.create.ACH',
                component: PaymentAccountCreate,
            },
            {
                path: 'customers/:id/payment-accounts/create-CC',
                name: 'customers.payment-account.create.CC',
                component: PaymentAccountCreate,
            },
            {
                path: 'customers/:id/payment-accounts/:payment_account_id/edit-ACH',
                name: 'customers.payment-account.edit.ACH',
                component: PaymentAccountCreate,
            },
            {
                path: 'customers/:id/payment-accounts/:payment_account_id/edit-CC',
                name: 'customers.payment-account.edit.CC',
                component: PaymentAccountCreate,
            },
            {
                path: 'customers/:id/payment-accounts/:payment_account_id/view-CC',
                name: 'customers.payment-account.view.CC',
                component: PaymentAccountView,
            },
            {
                path: 'customers/:id/payment-accounts/:payment_account_id/view-ACH',
                name: 'customers.payment-account.view.ACH',
                component: PaymentAccountView,
            },

            // Items
            {
                path: 'items',
                component: ItemsIndex,
            },
            {
                path: 'items/create',
                name: 'items.create',
                component: ItemCreate,
            },
            {
                path: 'items/:id/edit',
                name: 'items.edit',
                component: ItemCreate,
            },

            // Estimates
            {
                path: 'estimates',
                name: 'estimates.index',
                component: EstimateIndex,
            },
            {
                path: 'estimates/create',
                name: 'estimates.create',
                component: EstimateCreate,
            },
            {
                path: 'estimates/:id/view',
                name: 'estimates.view',
                component: EstimateView,
            },
            {
                path: 'estimates/:id/edit',
                name: 'estimates.edit',
                component: EstimateCreate,
            },

            // Invoices
            /* {
                path: 'v2/invoices',
                name: 'v2invoices.index',
                component: InvoiceV2Index,
            }, */
            {
                path: 'v2/invoices',
                name: 'v2invoices.index',
                component: InvoiceV2Index,
            },
            {
                path: 'invoices',
                name: 'invoices.index',
                component: InvoiceV2Index,
            },
            {
                path: 'invoices/create',
                name: 'invoices.create',
                component: InvoiceCreate,
                props: true
            },
            {
                path: 'invoices/:id/view/:deleted_at',
                name: 'invoices.deleted',
                component: InvoiceView,
            },
            {
                path: 'invoices/:id/view',
                name: 'invoices.view',
                component: InvoiceView,
            },
            {
                path: 'invoices/:id/edit',
                name: 'invoices.edit',
                component: InvoiceCreate,
            },

            // Package groups
            {
                path: 'groups',
                component: PackageGroupIndex,
            },
            {
                path: 'groups/create',
                name: 'groups.create',
                component: PackageGroupCreate,
            },
            {
                path: 'groups/:id/edit',
                name: 'groups.edit',
                component: PackageGroupCreate,
            },
            {
                path: 'groups/:id/view',
                name: 'groups.view',
                component: PackageGroupView,
            },

            // Payments
            {
                path: 'payments',
                name: 'payments.index',
                component: PaymentsIndex,
            },
            {
                path: 'payments-failed',
                name: 'payments-failed.index',
                component: PaymentsFailedIndex,
            },
            {
                path: 'payments/create',
                name: 'payments.create',
                component: PaymentCreate,
            },
            {
                path: 'payments/:id/create',
                name: 'invoice.payments.create',
                component: PaymentCreate,
            },
            {
                path: 'payments/:id/edit',
                name: 'payments.edit',
                component: PaymentCreate,
            },
            {
                path: 'payments/:id/view',
                name: 'payments.view',
                component: PaymentView,
            },
            // multiple
            {
                path: 'payments/multiple/customer/:customer_id/invoice/:invoice_id/create',
                name: 'payments.multiple.create',
                component: PaymentMultipleCreate,
            },
            // multiple
            {
                path: 'payments/multiple/:id/edit',
                name: 'payments.multiple.edit',
                component: PaymentMultipleCreate,
            },

            // Expenses
            {
                path: 'expenses',
                component: ExpensesIndex,
            },
            {
                path: 'expenses/create',
                name: 'expense.create',
                component: ExpenseCreate,
            },
            {
                path: 'expenses/:id/edit',
                name: 'expense.edit',
                component: ExpenseCreate,
            },
            {
                path: 'expenses/:id/view',
                name: 'expenses.view',
                component: ExpenseView,
            },
            {
                path: 'expenses/:id/docs',
                name: 'expenses.docs',
                component: ExpenseDocs,
            },
            {
                path: 'expenses-template',
                component: ExpensesTemplateIndex,
            },
            {
                path: 'expenses-template/create',
                name: 'expenses.create',
                component: ExpenseTemplateCreate,
            },
            {
                path: 'expenses-template/:id/edit',
                name: 'expenses.edit',
                component: ExpenseTemplateCreate,
            },

            // CorePBX
            {
                path: 'corePBX',
                component: CorePBX,
                children: [
                    //  Packages
                    {
                        path: 'packages',
                        name: 'corepbx.billingtemplates',
                        component: PackagesPBX,
                    },
                    {
                        path: 'packages/create',
                        name: 'corepbx.billingtemplates.create',
                        component: PackagesCreatePBX,
                    },
                    {
                        path: 'packages/:id/view',
                        name: 'corepbx.billingtemplates.view',
                        component: PackagesViewPBX,
                    },
                    {
                        path: 'packages/:id/edit',
                        name: 'corepbx.billingtemplates.edit',
                        component: PackagesCreatePBX,
                    },
                    {
                        path: 'packages/:id/copy',
                        name: 'corepbx.billingtemplates.copy',
                        component: PackagesCreatePBX,
                    },
                    // Billing Templates
                    // //Extension
                    {
                        path: 'billing-templates/extensions',
                        name: 'corepbx.extensions',
                        component: ExtensionsPBX,
                    },
                    {
                        path: 'billing-templates/extensions/create',
                        name: 'corepbx.extensions.create',
                        component: ExtensionsCreatePBX,
                    },
                    {
                        path: 'billing-templates/extensions/:id/edit',
                        name: 'corepbx.extensions.edit',
                        component: ExtensionsCreatePBX,
                    },
                    {
                        path: 'billing-templates/extensions/:id/copy',
                        name: 'corepbx.extensions.copy',
                        component: ExtensionsCreatePBX,
                    },
                    // CUSTOM APP RATE
                    {
                        path: 'billing-templates/custom-app-rate',
                        name: 'corepbx.custom-app-rate',
                        component: CustomAppRate,
                    },
                    {
                        path: 'billing-templates/custom-app-rate/create',
                        name: 'corepbx.custom-app-rate.create',
                        component: CustomAppRateCreateEdit,
                    },
                    {
                        path: 'billing-templates/custom-app-rate/:id/view',
                        name: 'corepbx.custom-app-rate.view',
                        component: CustomAppRateCreateEdit,
                    },
                    {
                        path: 'billing-templates/custom-app-rate/:id/edit',
                        name: 'corepbx.custom-app-rate.edit',
                        component: CustomAppRateCreateEdit,
                    },
                    // // DID
                    {
                        path: 'billing-templates/did',
                        name: 'corepbx.did',
                        component: DIDsPBX,
                    },
                    {
                        path: 'billing-templates/did/create',
                        name: 'corepbx.did.create',
                        component: DIDsCreatePBX,
                    },
                    {
                        path: 'billing-templates/did/:id/edit',
                        name: 'corepbx.did.edit',
                        component: DIDsCreatePBX,
                    },
                    {
                        path: 'billing-templates/did/:id/copy',
                        name: 'corepbx.did.copy',
                        component: DIDsCreatePBX,
                    },
                    // // DID-FREE  path: 'billing-templates/did-free',
                    {
                        path: 'billing-templates/toll-free',
                        name: 'corepbx.didFree',
                        component: DIDFREEsPBX,
                    },
                    {
                        path: 'billing-templates/toll-free/create',
                        name: 'corepbx.didFree.create',
                        component: DIDFREEsCreatePBX,
                    },
                    {
                        path: 'billing-templates/toll-free/:id/edit',
                        name: 'corepbx.didFree.edit',
                        component: DIDFREEsCreatePBX,
                    },
                    // Custom did groups CustomDidGroupIndexPbx
                    {
                        path: 'billing-templates/custom-did-groups',
                        name: 'corepbx.custom-did-groups',
                        component: CustomDidGroupIndexPbx,
                    },
                    {
                        path: 'billing-templates/custom-did-groups/create',
                        name: 'corepbx.custom-did-groups.create',
                        component: CustomDidGroupCreatePbx,
                    },
                    {
                        path: 'billing-templates/custom-did-groups/:id/edit',
                        name: 'corepbx.custom-did-groups.edit',
                        component: CustomDidGroupCreatePbx,
                    },
                    {
                        path: 'billing-templates/custom-did-groups/:id/view',
                        name: 'corepbx.custom-did-groups.view',
                        component: CustomDidGroupViewPbx,
                    },
                    // // International rate
                    {
                        path: 'billing-templates/international-rate',
                        name: 'corepbx.internationalRate',
                        component: InternacionalRatePBX,
                    },
                    {
                        path: 'billing-templates/international-rate/create',
                        name: 'corepbx.internationalRate.create',
                        component: InternacionalRateCreatePBX,
                    },
                    {
                        path: 'billing-templates/international-rate/:id/edit',
                        name: 'corepbx.internationalRate.edit',
                        component: InternacionalRateCreatePBX,
                    },

                    // Prefix Groups
                    {
                        path: 'billing-templates/prefix-groups',
                        name: 'corepbx.prefix-groups',
                        component: PrefixGroupsIndexPbx,
                    },
                    {
                        path: 'billing-templates/prefix-groups/create',
                        name: 'corepbx.prefix-group.create',
                        component: PrefixGroupCreatePbx,
                    },
                    {
                        path: 'billing-templates/prefix-groups/:id/edit',
                        name: 'corepbx.prefix-group.edit',
                        component: PrefixGroupCreatePbx,
                    },
                    {
                        path: 'billing-templates/prefix-groups/:id/copy',
                        name: 'corepbx.prefix-group.copy',
                        component: PrefixGroupCreatePbx,
                    },
                    {
                        path: 'billing-templates/prefix-groups/:id/view',
                        name: 'corepbx.prefix-group.view',
                        component: PrefixGroupViewPbx,
                    },

                    //PBXware Dashboard
                    {
                        path: 'PBXwareDashboard',
                        name: 'corepbx.pbxwaredashboard',
                        component: PBXwareDashboard,
                    },

                    // Config
                    // // Customization
                    {
                        path: 'config/customization',
                        name: 'corepbx.customization',
                        component: CustomizationPBX,
                    },
                    {
                        path: 'tenant/tenants-list',
                        name: 'corepbx.tenants_list',
                        component: TenantListPBX,
                    },
                    {
                        path: 'tenant/tenants-list/create',
                        name: 'corepbx.tenants_list.create',
                        component: TenantListCreatePBX,
                    },
                    {
                        path: 'tenant/tenants-list/:id/view',
                        name: 'corepbx.tenants_list.view',
                        component: TenantListViewPBX,
                    },
                    {
                        path: 'tenants',
                        name: 'corepbx.tenants',
                        component: TenantPBX,
                    },
                ]
            },
            {
                path: 'corePOS',
                component: CorePOS,
                children: [
                    //  Core Pos
                    {
                        path: 'main',
                        name: 'corepos.main',
                        component: CashRegisterMain,

                    },
                    {
                        path: 'cash-register',
                        name: 'corepos.cashregister',
                        component: CashRegister,

                    },
                    {
                        path: 'cash-register/:id/view',
                        name: 'corepos.cashregister.view',
                        component: InformationCashRegister,
                    },
                    {
                        path: 'cash-register/:id/report',
                        name: 'corepos.cashregister.report',
                        component: CashRegisterReport,
                    },
                    {
                        path: 'create-cash-register',
                        name: 'corepos.cashregister.create',
                        component: CreateCashRegister,
                    },
                    {
                        path: 'create-cash-register/:id/edit',
                        name: 'corepos.cashregister.edit',
                        component: CreateCashRegister,
                    },
                    {
                        path: 'stores',
                        name: 'corepos.stores',
                        component: Stores,
                    },
                    {
                        path: 'tables',
                        name: 'corepos.tables',
                        component: Tables,
                    },
                    {
                        path: 'money',
                        name: 'corepos.money',
                        component: Money,
                    },
                    {
                        path: 'store/create',
                        name: 'store.create',
                        component: StoreCreate,
                    },
                    {
                        path: 'create-table',
                        name: 'corepos.tables.create',
                        component: TableCreate,
                    },
                    {
                        path: 'table/:id/edit',
                        name: 'tables.edit',
                        component: TableCreate,
                    },
                    {
                        path: 'store/edit/:id',
                        name: 'store.edit',
                        component: StoreEdit,
                    },
                    {
                        path: 'corepos-history',
                        name: 'corepos.history',
                        component: coreposhistoryIndex,

                    },
                ]
            },

            // Reports PBX
            {
                path: 'corePBX/reports',
                component: ReportsPbxIndex,
                children: [
                    //  Departaments
                    {
                        path: 'departaments',
                        name: 'corepbx.departaments',
                        component: DepartamentsPbx,
                    },
                    {
                        path: 'reports-pbx',
                        name: 'corepbx.reportsPbx',
                        component: ReportsPbx,
                    },
                    {
                        path: 'tenants',
                        name: 'corepbx.resportsTenants',
                        component: ReportsTenantsPbx,
                    },

                ]
            },
            {
                path: 'corePBX/reports/departaments/create',
                name: 'corepbx.DepartamentsPbxAddcp',
                component: DepartamentsPbxAdd,
            },
            {
                path: 'corePBX/reports/departaments/:id/edit',
                name: 'corepbx.DepartamentsPbxEdit',
                component: DepartamentsPbxAdd,
            },
            // Ticket
            {
                path: 'tickets',
                component: Ticket,
                children: [
                    //  Departaments
                    {
                        path: 'departaments',
                        name: 'tickets.departaments',
                        component: DepartamentsTicket,
                    },
                    {
                        path: 'departaments/create',
                        name: 'tickets.departaments.create',
                        component: DepartamentsCreateTicket,
                    },
                    {
                        path: 'departaments/:id/edit',
                        name: 'tickets.departaments.edit',
                        component: DepartamentsCreateTicket,
                    },
                    {
                        path: 'departaments/:id/view',
                        name: 'tickets.departaments.view',
                        component: DepartamentsTicketView,
                    },

                    // Tickets
                    {
                        path: 'main',
                        name: 'tickets.main',
                        component: PrincipalTicket,
                    },
                    {
                        path: 'main/add-ticket',
                        name: 'main.add-ticket',
                        component: PrincipalAddticket,
                    },
                    {
                        path: 'main/:id/:id1/edit-ticket',
                        name: 'main.edit-ticket',
                        component: PrincipalAddticket,
                    },
                    // Email
                    {
                        path: 'email',
                        name: 'tickets.email',
                        component: EmailTicket,
                    },
                ]
            },
            // view tickets
            {
                path: 'tickets/main/:id/:id1/view-ticket',
                name: 'main.view-ticket',
                component: PrincipalViewticket,
            },
            // create note
            {
                path: 'note/create',
                name: 'note.create',
                component: CreateNoteTicket,
            },
            // view note
            {
                path: 'note/:id/view',
                name: 'note.view',
                component: ViewNoteTicket,
            },
            // bandwidth
            {
                path: 'bandwidth',
                component: BandwidthModuleLayout,
                children: [
                    //  desconnect
                    {
                        path: 'desconnect',
                        name: 'bandwidth.desconnect',
                        component: BandwidthDesconnectModule,
                    },
                    {
                        path: 'ordering',
                        name: 'bandwidth.ordering',
                        component: BandwidthOrderingModule,
                    },
                    {
                        path: 'e911management',
                        name: 'bandwidth.e911management',
                        component: BandwidthE911ManagementModule,
                    },

                ]
            },


            // View search
            {
                path: 'search',
                component: SearchIndex,
            },
            // Lead
            {
                path: 'leads',
                component: LeadIndex,
            },
            {
                path: 'leads/create',
                name: 'leads.create',
                component: LeadCreate,
            },
            {
                path: 'leads/:id/edit',
                name: 'leads.edit',
                component: LeadCreate,
            },
            {
                path: 'leads/:id/view',
                name: 'leads.view',
                component: LeadView,
            },

            // Leadnotes

            {
                path: 'leadnotes/:idlead/create',
                name: 'leadnotes.create',
                component: LeadNoteCreate,
            },

            {
                path: 'leadnotes/:idlead/:idlnote/view',
                name: 'leadnotes.view',
                component: LeadNoteView,
            },



            // Leadnotes

            {
                path: 'leadnotes/:idlead/:idlnote/edit',
                name: 'leadnotes.edit',
                component: LeadNoteCreate,
            },

            // User
            {
                path: 'users',
                component: UserIndex,
            },
            {
                path: 'users/create',
                name: 'users.create',
                component: UserCreate,
            },
            {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: UserCreate,
            },
            {
                path: 'users/:id/view',
                name: 'users.view',
                component: UserView,
            },
            {
                path: 'users/:id/permissions',
                name: 'users.permissions',
                component: UserPermissions,
            },

            // Reports
            {
                path: 'reports',
                component: ReportLayout,
                children: [{
                        path: 'sales',
                        component: SalesReports,
                    },
                    {
                        path: 'expenses',
                        component: ExpensesReport,
                    },
                    {
                        path: 'profit-loss',
                        component: ProfitLossReport,
                    },
                    {
                        path: 'taxes',
                        component: TaxReport,
                    },
                ],
            },

            // Settings
            {
                path: 'settings',
                component: SettingsLayout,
                children: [{
                        path: 'company-info',
                        name: 'company.info',
                        component: CompanyInfo,
                    },
                    {
                        path: 'customization',
                        name: 'customization',
                        component: Customization,
                    },
                    {
                        path: 'payment-mode',
                        name: 'payment.mode',
                        component: PaymentMode,
                    },
                    {
                        path: 'custom-fields',
                        name: 'custom.fields',
                        component: CustomFieldsIndex,
                    },
                    {
                        path: 'mobile',
                        name: 'mobile',
                        component: Mobile,
                    },
                    {
                        path: 'user-profile',
                        name: 'user.profile',
                        component: UserProfile,
                    },
                    {
                        path: 'preferences',
                        name: 'preferences',
                        component: Preferences,
                    },
                    {
                        path: 'tax-agency',
                        name: 'tax.agency',
                        component: TaxAgency,
                    },
                    {
                        path: 'tax-categories',
                        name: 'tax.categories',
                        component: TaxCategories,
                    },
                    {
                        path: 'tax-types',
                        name: 'tax.types',
                        component: TaxTypes,
                    },
                    {
                        path: 'notes',
                        name: 'notes',
                        component: NotesSetting,
                    },
                    {
                        path: 'expense-category',
                        name: 'expense.category',
                        component: ExpenseCategory,
                    },
                    {
                        path: 'mail-configuration',
                        name: 'mailconfig',
                        component: MailConfig,
                    },
                    {
                        path: 'notifications',
                        name: 'notifications',
                        component: Notifications,
                    },
                    {
                        path: 'update-app',
                        name: 'updateapp',
                        component: UpdateApp,
                    },
                    {
                        path: 'backup',
                        name: 'backup',
                        component: Backup,
                    },
                    {
                        path: 'file-disk',
                        name: 'file-disk',
                        component: FileDisk,
                    },

                    // Tax groups
                    {
                        path: 'tax-groups',
                        component: taxGroupIndex,
                    },
                    {
                        path: 'tax-groups/create',
                        name: 'tax-groups.create',
                        component: taxGroupCreate,
                    },
                    {
                        path: 'tax-groups/:id/edit',
                        name: 'tax-groups.edit',
                        component: taxGroupCreate,
                    },
                    {
                        path: 'tax-groups/:id/view',
                        name: 'tax-groups.view',
                        component: taxGroupView,
                    },
                    {
                        path: 'logs',
                        name: 'logs',
                        component: Logs,
                    },
                    // Add ons
                    {
                        path: 'add-ons',
                        name: 'addOns',
                        component: AddOns,
                    },
                    // Modules
                    {
                        path: 'modules',
                        name: 'modules',
                        component: Modules,
                    },
                    {
                        path: 'pbx',
                        name: 'pbx',
                        component: PBX,
                    },
                    {
                        path: 'pbx/:id/edit',
                        name: 'pbx.edit',
                        component: PBXEdit,
                    },
                    {
                        path: 'pbx/addrow',
                        name: 'pbx.addrow',
                        component: PbxAddServer,
                    },
                    {
                        path: 'pbx/jobs/logs',
                        name: 'pbxJobs.logs',
                        component: PBXJobsLogs,
                    },
                    // Payments Gateways
                    {
                        path: 'payment-gateways',
                        name: 'payment-gateways',
                        component: PaymentGateway,
                    },
                    // Authorize
                    {
                        path: 'authorize',
                        name: 'authorize',
                        component: authorizeIndex,
                    },
                    {
                        path: 'authorize/create',
                        name: 'authorize.create',
                        component: authorizeCreate,
                    },
                    {
                        path: 'authorize/:id/edit',
                        name: 'authorize.edit',
                        component: authorizeCreate,
                    },
                    {
                        path: 'authorize/:id/view',
                        name: 'authorize.view',
                        component: authorizeView,
                    },
                    {
                        path: '/admin/settings/prefix-customer',
                        name: 'prefix.customer',
                        component: prefixCustomer,
                    },
                    // Paypal
                    {
                        path: 'paypal',
                        name: 'paypal',
                        component: paypalIndex,
                    },
                    {
                        path: 'paypal/create',
                        name: 'paypal.create',
                        component: paypalCreate,
                    },
                    {
                        path: 'paypal/:id/edit',
                        name: 'paypal.edit',
                        component: paypalCreate,
                    },
                    {
                        path: 'paypal/:id/view',
                        name: 'paypal.view',
                        component: paypalView,
                    },
                    // Stripe
                    {
                        path: 'stripe',
                        name: 'stripe',
                        component: stripeIndex,
                    },
                    {
                        path: 'stripe/create',
                        name: 'stripe.create',
                        component: stripeCreate,
                    },
                    {
                        path: 'stripe/:id/edit',
                        name: 'stripe.edit',
                        component: stripeCreate,
                    },
                    {
                        path: 'stripe/:id/view',
                        name: 'stripe.view',
                        component: stripeView,
                    },

                    {
                        path: 'bandwidth',
                        name: 'bandwidth',
                        component: Bandwidth,
                    },
                    {
                        path: 'bandwidth/add-config',
                        name: 'bandwidth.add-config',
                        component: BandwidthCreate,
                    },
                    {
                        path: 'bandwidth/:id/edit-config',
                        name: 'bandwidth.edit-config',
                        component: BandwidthCreate,
                    },
                    //auxpax

                    {
                        path: 'AuxVault',
                        name: 'AuxVault',
                        component: auxpayIndex,
                    },

                    {
                        path: 'AuxVault/create',
                        name: 'AuxVault.create',
                        component: auxpayCreate,
                    },
                    {
                        path: 'AuxVault/:id/edit',
                        name: 'AuxVault.edit',
                        component: auxpayCreate,
                    },

                    {
                        path: 'AuxVault/:id/view',
                        name: 'AuxVault.view',
                        component: auxpayView,
                    },



                    // Retentions
                    {
                        path: 'retentions',
                        component: retentionsIndex,
                    },
                    {
                        path: 'retentions/create',
                        name: 'retentions.create',
                        component: retentionsCreate,
                    },
                    {
                        path: 'retentions/:id/edit',
                        name: 'retentions.edit',
                        component: retentionsCreate,
                    },
                    {
                        path: 'retentions/:id/view',
                        name: 'retentions.view',
                        component: retentionsView,
                    },

                ],
            },

            // Items Groups
            {
                path: 'item-groups',
                component: ItemGroupsIndex,
            },
            {
                path: 'item-groups/create',
                name: 'item-groups.create',
                component: ItemGroupsCreate,
            },
            {
                path: 'item-groups/:id/edit',
                name: 'item-groups.edit',
                component: ItemGroupsCreate,
            },
            {
                path: 'item-groups/:id/view',
                name: 'item-groups.view',
                component: ItemGroupsView,
            },
            // Providers
            {
                path: 'providers',
                component: providersIndex,
            },
            {
                path: 'providers/create',
                name: 'providers.create',
                component: providersCreate,
            },
            {
                path: 'providers/:id/edit',
                name: 'providers.edit',
                component: providersCreate,
            },
            {
                path: 'providers/:id/view',
                name: 'providers.view',
                component: providersView,
            },
            // Roles
            {
                path: 'roles/:id/edit',
                name: 'roles.edit',
                component: RolesCreate,
            },
            {
                path: 'roles/create',
                name: 'roles.create',
                component: RolesCreate,
            },
            {
                path: 'roles/',
                name: 'roles.index',
                component: RolesIndex,
            },
        ],
    },
    /*
       |--------------------------------------------------------------------------
       | Customer
       |--------------------------------------------------------------------------|
       */
    {
        path: '/customer',
        component: LayoutCustomer,
        meta: {
            requiresAuth: true,
            isCustomer: true
        },
        children: [
            // Dashboard
            {
                path: '/',
                component: DashboardCustomer,
                name: 'dashboardCustomer',
            },
            {
                path: 'dashboard',
                component: DashboardCustomer,
            },
            // Dashboard Customer View Services
            {
                path: 'service/:customer_package_id/view',
                name: 'customer.package-view',
                component: PackageViewCustomer,
            },
            // Dashboard Customer View Services Pbx
            {
                path: 'pbx-service/:pbx_service_id/view',
                name: 'customer.pbx-service-view',
                component: CustomerPbxServiceView,
            },
            // Dashboard Estimates
            {
                path: '/estimates',
                component: EstimateCustomerIndex,
                name: 'principalEstimates',
            },
            {
                path: 'estimates',
                name: 'estimatesCustomer.index',
                component: EstimateCustomerIndex,
            },
            {
                path: 'estimates/:id/view',
                name: 'estimatesCustomerinvoice.view',
                component: EstimateCustomerView,
            },

            // Dashboard Invoices
            {
                path: 'invoices',
                component: InvoiceV2IndexCustomer,
                name: 'invoicesCustomer.index',
            },
            {
                path: 'invoice/:id/view',
                name: 'invoice.view',
                component: InvoiceV2ViewCustomer,
            },
            // Dashboard Payments
            {
                path: '/payments',
                component: PaymentCustomerIndex,
                name: 'principalPayment',
            },
            {
                path: 'payments',
                name: 'paymentsCustomer.index',
                component: PaymentCustomerIndex,
            },
            {
                path: 'payments/:id/view',
                name: 'paymentsCustomer.view',
                component: PaymentCustomerView,
            },
            {
                path: 'payments/:id/create',
                name: 'paymentsCustomer.createInvoice',
                component: PaymentCustomerCreate,
            },
            {
                path: 'payments/create',
                name: 'paymentsCustomer.create',
                component: PaymentCustomerCreate,
            },

            {
                path: 'payments/addcredit',
                name: 'paymentsCustomer.addcredit',
                component: PaymentCustomerAddCredit,
            },

            {
                path: 'payment-accounts',
                name: 'paymentAccountCustomer.index',
                component: PaymentAccountCustomerIndex,
            },
            {
                path: 'payment-accounts/:payment_account_id/view-ACH',
                name: 'paymentAccountCustomer.view.ACH',
                component: PaymentAccountCustomerView,
            },
            {
                path: 'payment-accounts/:payment_account_id/view-CC',
                name: 'paymentAccountCustomer.view.CC',
                component: PaymentAccountCustomerView,
            },
            {
                path: 'payment-accounts/create-ACH',
                name: 'paymentAccountCustomer.create.ACH',
                component: PaymentAccountCustomerCreate,
            },
            {
                path: 'payment-accounts/create-CC',
                name: 'paymentAccountCustomer.create.CC',
                component: PaymentAccountCustomerCreate,
            },
            {
                path: 'payment-accounts/:payment_account_id/edit-ACH',
                name: 'paymentAccountCustomer.edit.ACH',
                component: PaymentAccountCustomerCreate,
            },
            {
                path: 'payment-accounts/:payment_account_id/edit-CC',
                name: 'paymentAccountCustomer.edit.CC',
                component: PaymentAccountCustomerCreate,
            },
           // Services
        {
          path: 'services',
          name: 'services.customer',
          component: CustomerServices,
      },
            // Tickets
            {
                path: 'tickets',
                name: 'tickets.customer',
                component: TicketsCustomer,
            },
            {
                path: 'tickets/add',
                name: 'tickets.customer.add',
                component: TicketsCustomerCreateEdit,
            },
            {
                path: 'tickets/:id/:id1/edit',
                name: 'tickets.customer.edit',
                component: TicketsCustomerCreateEdit,
            },
            {
                path: 'tickets/:id/:id1/view',
                name: 'tickets.customer.view',
                component: TicketsCustomerView,
            },

            {
                path: 'ticketsnote/:id/:id1/view',
                name: 'tickets.customernote.view',
                component: TicketsCustomerViewNote,
            },
            {
                path: 'reports/',
                component: ReportsPbxIndexCustomer,
                children: [{
                        path: 'departaments',
                        name: 'corepbx.customer.departaments',
                        component: DepartamentsPbxCustomer,
                    },
                    {
                        path: 'reports-pbx',
                        name: 'corepbx.customer.reportsPbx',
                        component: ReportsPbxCustomer,
                    },
                ]
            },
            {
                path: 'reports/departaments/create',
                name: 'corepbx.customer.DepartamentsPbxAddcp',
                component: DepartamentsPbxAddCustomer,
            },
            {
                path: 'reports/departaments/:id/edit',
                name: 'corepbx.customer.DepartamentsPbxEdit',
                component: DepartamentsPbxAddCustomer,
            },
        ],

    },

    // DEFAULT ROUTE
    {
        path: '*',
        component: NotFoundPage
    },
]

const router = new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active',
})

async function fetchUser() {
    let user = store.getters['user/currentUser']
    if (!user) {
        await store.dispatch('user/fetchCurrentUser')
    }
}

router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.requiresAuth)) {
        fetchUser().then(() => {
            let user = store.getters['user/currentUser']

            if (user.role === 'customer' && !user.authentication) {
                store.dispatch('auth/logout')
            }

            if (to.matched.some(record => record.meta.isAdmin)) {
                if (user.role === 'super admin') {
                    next()
                } else if (user.role === 'customer') {
                    next({ name: 'dashboardCustomer' })
                } else {
                    next({ name: 'dashboard' })
                }
            } else if (to.matched.some(record => record.meta.isCustomer)) {
                if (user.role === 'customer') {
                    next()
                } else {
                    next({ name: 'dashboard' })
                }
            } else {
                next()
            }
        })
    } else {
        next()
    }
})

export default router
