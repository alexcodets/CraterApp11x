import * as types from './mutation-types'
import {
    PACKAGES_LIST
} from "./mutation-types";

export default {
    [types.BOOTSTRAP_CUSTOMERS](state, customers) {
        state.customers = customers
    },

    [types.SET_TOTAL_CUSTOMERS](state, totalCustomers) {
        state.totalCustomers = totalCustomers
    },

    [types.SET_PBX_SERVICES_DAYS_RENEWAL](state, daysToRenewal){
        state.daysToRenewal = daysToRenewal
    },

    [types.ADD_CUSTOMER](state, data) {
        state.customers.push(data.customer)
    },

    [types.UPDATE_CUSTOMER](state, data) {
        let pos = state.customers.findIndex(
            (customer) => customer.id === data.customer.id
        )
        state.customers[pos] = data.customer
    },

    [types.DELETE_CUSTOMER](state, id) {
        let index = state.customers.findIndex((customer) => customer.id === id)
        state.customers.splice(index, 1)
    },

    [types.DELETE_MULTIPLE_CUSTOMERS](state, selectedCustomers) {
        selectedCustomers.forEach((customer) => {
            let index = state.customers.findIndex((_cust) => _cust.id === customer.id)
            state.customers.splice(index, 1)
        })
        state.selectedCustomers = []
    },

    [types.SET_SELECTED_CUSTOMERS](state, data) {
        state.selectedCustomers = data
    },

    [types.RESET_SELECTED_CUSTOMER](state, data) {
        state.selectedCustomer = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.SET_SELECTED_VIEW_CUSTOMER](state, selectedViewCustomer) {
        state.selectedViewCustomer = selectedViewCustomer
    },

    [types.PACKAGES_LIST](state, packagesList) {
        state.packagesList = packagesList
    },

    [types.SET_PACKAGE_PARAMETERS](state, packageParameters) {
        state.packageParameters = packageParameters
    },

    [types.SET_CORE_PBX_SERVICES_PARAMETERS](state, corePbxServicesParameters) {
        state.corePbxServicesParameters = corePbxServicesParameters
    },

    [types.SET_CORE_PBX_SERVICES_INCLUDED_DATA](state, corePbxServicesIncludedData) {
        state.corePbxServicesIncludedData = corePbxServicesIncludedData
    },

    [types.PBX_SERVICE_SAVED](state, pbxServiceSaved) {
        state.pbxServiceSaved = pbxServiceSaved
    },

    [types.PBX_TENANT_SAVED](state, pbxTenantSaved) {
        state.pbxTenantSaved = pbxTenantSaved
    },

    [types.PBX_DID_SAVED](state, pbxDIDSaved) {
        state.pbxDIDSaved = pbxDIDSaved
    },

    [types.PBX_EXT_SAVED](state, pbxEXTSaved) {
        state.pbxEXTSaved = pbxEXTSaved
    },

    [types.INVOICES_LIST](state, invoicesList) {
        state.invoicesList = invoicesList
    },

    [types.ESTIMATES_LIST](state, estimatesList) {
        state.estimatesList = estimatesList
    },

    /*extension*/
    [types.BOOTSTRAP_EXTENSION](state, extensions) {
        state.extensions = extensions
    },
    [types.SET_TOTAL_EXTENSION](state, totalExtensions) {
        state.totalExtensions = totalExtensions
    },
    [types.BOOTSTRAP_EXTENSION_INCLUDE](state, extensions) {
        state.extensionsInclude = extensions
    },
    [types.SET_TOTAL_EXTENSION_INCLUDE](state, totalExtensions) {
        state.totalExtensionsInclude = totalExtensions
    },
    [types.SET_SELECTED_EXTENSION](state, data) {
        state.selectedPbxExtensions = data
    },
    [types.SET_SELECTED_EXTENSION_INCLUDE](state, data) {
        state.selectedPbxExtensionsToInclude = data
    },
    [types.SET_SELECT_ALL_STATE_EXTENSION](state, data) {
        state.selectAllFieldExtensions = data
    },
    [types.SET_SELECT_ALL_STATE_EXTENSION_INCLUDE](state, data) {
        state.selectAllFieldExtensionsToInclude = data
    },
    [types.RESET_SELECTED_EXTENSION](state, data) {
        state.selectedPbxExtensions = null
    },

    /*did*/
    [types.BOOTSTRAP_DID](state, did) {
        state.did = did
    },
    [types.SET_TOTAL_DID](state, totalDID) {
        state.totalDID = totalDID
    },
    [types.BOOTSTRAP_DID_INCLUDE](state, did) {
        state.didInclude = did
    },
    [types.SET_TOTAL_DID_INCLUDE](state, totalDID) {
        state.totalDIDInclude = totalDID
    },
    [types.SET_SELECT_ALL_STATE_DID](state, data) {
        state.selectAllFieldDID = data
    },
    /* [types.SET_SELECT_ALL_STATE_DID_INCLUDE](state, data) {
        state.selectAllFieldDIDInclude = data
        console.log("Estado ban",state.selectAllFieldDIDInclude)
    }, */
    [types.SET_SELECT_ALL_STATE_DID_INCLUDE](state, data) {
        state.selectAllFieldDIDToInclude = data
    },
    [types.SET_SELECTED_DID](state, data) {
        state.selectedPbxDID = data
    },
    [types.SET_SELECTED_DID_INCLUDE](state, data) {
        state.selectedPbxDIDToInclude = data
    },
    [types.RESET_SELECTED_DID](state, data) {
        state.selectedPbxDID = null
    },
}