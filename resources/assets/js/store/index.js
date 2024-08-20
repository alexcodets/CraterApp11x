import Vue from 'vue'
import Vuex from 'vuex'

import * as getters from './getters'
import mutations from './mutations'
import actions from './actions'

import auth from './modules/auth'
import user from './modules/user'
import auxvault from './modules/auxvault'
import category from './modules/category'
import customer from './modules/customer'
import lead from './modules/leads'
import customerTicket from './modules/customer-ticket'
import customerAddress from './modules/customer-address'
import customerContacts from './modules/customer-contacts'
import company from './modules/company'
import corePos from './modules/core-pos'
import corePoshistory from './modules/core-pos-history'
import dashboard from './modules/dashboard'
import estimate from './modules/estimate'
import estimateCust from './modules/estimate-customer'
import expense from './modules/expense'
import invoice from './modules/invoice'
import invoiceCustomer from './modules/invoice-customer'
import payment from './modules/payment'

import paymentMultiple from './modules/payment-multiple'

import paymentCust from './modules/payment-customer'
import item from './modules/item'
import modal from './modules/modal'
import customFields from './modules/custom-field'
import taxAgency from './modules/tax-agency'
import taxCategories from './modules/tax-categories'
import taxType from './modules/tax-type'
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
import customAppRate from '../../../../resources/assets/js/store/modules/custom-app-rate'
import reportsModule from './modules/reports'
import retentions from './modules/retentions'
import bandwidth from './modules/bandwidth'
import customSearch from './modules/customSearch'
import tenants from './modules/tenants'
import stripes from './modules/stripe'
import validateIdentification from './modules/validateIdentification'
import leadNote from './modules/lead-note'
import paymentFee from './modules/payment-fee'


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
        auxvault,
        category,
        company,
        corePos,
        corePoshistory,
        customer,
        lead,
        customerAddress,
        customerContacts,
        customerNote,
        customerTicket,
        dashboard,
        estimate,
        item,
        invoice,
        invoiceCustomer,
        expense,
        modal,
        customFields,
        payment,
        paymentMultiple,
        taxAgency,
        taxCategories,
        taxType,
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
        customAppRate,
        reportsModule,
        retentions,
        bandwidth,
        customSearch,
        tenants,
        stripes,
        validateIdentification,
        leadNote,
        paymentFee
    },
})