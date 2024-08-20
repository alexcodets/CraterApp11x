import Vue from 'vue'
import Vuex from 'vuex'

import * as getters from './getters'
import mutations from './mutations'
import actions from './actions'

import auth from './modules/auth'
import user from './modules/user'
import category from './modules/category'
import customer from './modules/customer'
import company from './modules/company'
import dashboard from './modules/dashboard'
import estimate from './modules/estimate'
import estimateCust from './modules/estimate-customer'
import expense from './modules/expense'
import invoice from './modules/invoice'
import invoiceCustomer from './modules/invoice-customer'
import payment from './modules/payment'
import paymentCust from './modules/payment-customer'
import item from './modules/item'
import modal from './modules/modal'
import customFields from './modules/custom-field'
import taxType from './modules/tax-type'
import taxCategories from './modules/tax-categories'
import users from './modules/users'
import backup from './modules/backup'
import disks from './modules/disk'
import estimateTemplate from './modules/estimate-template'
import invoiceTemplate from './modules/invoice-template'
import search from './modules/search'
import notes from './modules/notes'
import log from './modules/Logs'
import itemGroups from './modules/item-groups'
import group from './modules/package-group'
import pack from './modules/package'
import taxGroups from './modules/tax-group'
import providers from './modules/provider'
import authorizations from './modules/authorize'
import modules from './modules/modules'
import mobileSettings from './modules/mobile-settings'
import paymentGateways from './modules/payment-gateways'
import pbx from './modules/package-pbx'
import avalara from './modules/avalara'
import roles from './modules/roles'
import extensions from './modules/extensions'
import did from './modules/did'
import customerNote from './modules/customer-note'
import customerTicket from './modules/customer-ticket'
import service from './modules/service'
import servicePbx from './modules/service-pbx'
import didtollfree from './modules/did-free'
import internacionalrate from './modules/internacional-rate'
import paymentAccounts from './modules/payment-accounts'
import paymentAccountsCustomer from './modules/payment-accounts-customer'
import failedPaymentHistory from './modules/failed-payment-history'
import ticketDepartament from './modules/departament-ticket'
import categoriesTollF from './modules/category-toll-free'
import pbxService from './modules/pbx-service'
import paypal from './modules/paypal'
import prefixGroup from './modules/prefix-group'
import customDidGroup from './modules/custom-did-group'
import customerProfile from './modules/customer-profile'
import leadNote from './modules/lead-note'


Vue.use(Vuex)

const initialState = {
    languages: [],

    timeZones: [],

    dateFormats: [],

    fiscalYears: [],

    currencies: [],

    countries: [],

    isAppLoaded: false,

    isSidebarOpen: false,
}

export default new Vuex.Store({
    strict: true,
    state: initialState,
    getters,
    mutations,
    actions,

    modules: {
        auth,
        user,
        category,
        company,
        customer,
        dashboard,
        estimate,
        item,
        invoice,
        invoiceCustomer,
        expense,
        modal,
        customFields,
        payment,
        taxType,
        taxCategories,
        users,
        backup,
        disks,
        estimateCust,
        paymentCust,
        estimateTemplate,
        invoiceTemplate,
        search,
        notes,
        itemGroups,
        group,
        pack,
        taxGroups,
        log,
        authorizations,
        modules,
        mobileSettings,
        pbx,
        providers,
        avalara,
        roles,
        paymentGateways,
        extensions,
        did,
        customerNote,
        customerTicket,
        service,
        servicePbx,
        didtollfree,
        internacionalrate,
        paymentAccounts,
        paymentAccountsCustomer,
        failedPaymentHistory,
        ticketDepartament,
        categoriesTollF,
        pbxService,
        paypal,
        prefixGroup,
        customDidGroup,
        customerProfile,
        leadNote
    },
})