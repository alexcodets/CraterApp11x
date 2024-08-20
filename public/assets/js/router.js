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
//  Packages
import PackageIndex from './views/packages/Index.vue'
import PackageCreate from './views/packages/Create.vue'
import PackageView from './views/packages/View.vue'

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
import CustomerAddPackage from './views/customers/CreatePackage.vue'
import CustomerNote from './views/customers/Note.vue'
import CustomerAddNote from './views/customers/CreateNote.vue'
import Customerticket from './views/customers/Ticket.vue'
import CustomerAddticket from './views/customers/CreateTicket.vue'
import CustomerPackageView from './views/customers/PackageView.vue'
import CustomerConfig from './views/customers/CustomerConfig.vue'
import CustomerAddCorepbxServices from './views/customers/CreateCorePbxServices.vue'
import CustomerPbxPackageView from './views/customers/PbxPackageView.vue'
import PbxServiceView from "./views/customers/PbxServiceView";
import PbxServiceViewCallHistory from "./views/customers/SearchCallHistoryPbxServices/index"
    // tickets
import TicketsCustomer from './views/tickets-customer/Index.vue'; 
import TicketsCustomerCreateEdit from './views/tickets-customer/CreateEdit.vue'; 
import TicketsCustomerView from './views/tickets-customer/View.vue'; 

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

import PaymentCustomerIndex from './views/payment-customer/Index.vue'
import PaymentCustomerView from './views/payment-customer/View.vue'
import PaymentCustomerCreate from './views/payment-customer/Create.vue'

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
import ExpensesIndex from './views/expenses/Index'
import ExpenseCreate from './views/expenses/Create.vue'

//PBX
import CorePBX from './views/corePbx/Index.vue'
import PBXJobsLogs from './views/settings/modules/Logs/index.vue'

//PackagesPBX
import PackagesPBX from './views/corePbx/packages/Packages.vue'
import PackagesCreatePBX from './views/corePbx/packages/Create.vue'
import PackagesViewPBX from './views/corePbx/packages/View.vue'
//ExtensionPBX
import ExtensionsPBX from './views/corePbx/billing-templates/extensions/Extensions.vue'
import ExtensionsCreatePBX from './views/corePbx/billing-templates/extensions/Create.vue'
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

// Email Templates
import EmailTicket from './views/tickets/email/Principal.vue'

// Customization
import CustomizationPBX from './views/corePbx/config/Customization.vue'

//User
import UserIndex from './views/users/Index.vue'
import UserCreate from './views/users/Create.vue'

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
import TaxTypes from './views/settings/TaxTypesSetting.vue'
import Mobile from './views/settings/MobileSetting.vue'
import moduleMobileIndex from './views/settings/modules/Mobile/Principal.vue'
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
import PaymentGateway from './views/settings/PaymentGatewaySetting.vue'

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
            // Module Mobile
            {
                path: 'module/billpay',
                name: 'settingsMobile.index',
                component: moduleMobileIndex,
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
                name: 'invoices.view',
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

            // Expenses
            {
                path: 'expenses',
                component: ExpensesIndex,
            },
            {
                path: 'expenses/create',
                name: 'expenses.create',
                component: ExpenseCreate,
            },
            {
                path: 'expenses/:id/edit',
                name: 'expenses.edit',
                component: ExpenseCreate,
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

                ]
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
                    //Paypal
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
                    }
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
        ],
    },

    //  DEFAULT ROUTE
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
            if (to.matched.some(record => record.meta.isAdmin)) {
                if (user.role === 'super admin') {
                    next()
                } else if(user.role === 'customer') {
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