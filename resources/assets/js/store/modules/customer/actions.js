import * as types from './mutation-types'
import * as searchTypes from '../search/mutation-types'

export const fetchCustomers = ({ commit }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/customers`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_CUSTOMERS, response.data.customers.data)
                commit(types.SET_TOTAL_CUSTOMERS, response.data.customerTotalCount)
                commit('search/' + searchTypes.SET_CUSTOMER_LISTS, response.data.customers.data, { root: true })
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getCustomerNumber = ({ commit, dispatch, state }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/next-number?key=customer`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomersDisabled = ({ commit }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/customers-disabled`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_CUSTOMERS, response.data.customers.data)
                commit(types.SET_TOTAL_CUSTOMERS, response.data.customerTotalCount)
                commit('search/' + searchTypes.SET_CUSTOMER_LISTS, response.data.customers.data, { root: true })
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const customerRestore = ({ commit }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.put(`/api/v1/customers-restore/${id}`, )
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomer = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${params.id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getCustomers = ({ commit, dispatch }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/items/get-customers`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomerWithoutLogin = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers-without-login/${params.id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewCustomer = ({
    commit,
    dispatch
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${params.id}/stats`, {
                params
            })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_CUSTOMER, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addCustomer = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/customers', data)
            .then((response) => {
                commit(types.ADD_CUSTOMER, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateCustomer = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/customers/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_CUSTOMER, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomer = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customers/delete`, id)
            .then((response) => {
                commit(types.DELETE_CUSTOMER, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteMultipleCustomers = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customers/delete`, {
                ids: state.selectedCustomers
            })
            .then((response) => {
                commit(types.DELETE_MULTIPLE_CUSTOMERS, state.selectedCustomers)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setPrefix = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios.post(`/api/v1/customers/set-prefix`, params)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPrefix = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios.post(`/api/v1/customers/fetch-prefix`, {
                params
            })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setSelectAllState = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllCustomers = ({
    commit,
    dispatch,
    state
}) => {
    if (state.selectedCustomers.length === state.customers.length) {
        commit(types.SET_SELECTED_CUSTOMERS, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allCustomerIds = state.customers.map((cust) => cust.id)
        commit(types.SET_SELECTED_CUSTOMERS, allCustomerIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const selectCustomer = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECTED_CUSTOMERS, data)
    if (state.selectedCustomers.length === state.customers.length) {
        commit(types.SET_SELECT_ALL_STATE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE, false)
    }
}

export const resetSelectedCustomer = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.RESET_SELECTED_CUSTOMER)
}

export const addPackage = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {

        window.axios
            .post('/api/v1/customers/package/save', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPackages = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${params.customer_id}/packages`, {
                params
            })
            .then((response) => {
                commit(types.PACKAGES_LIST, response.data.packagesList.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setPackageParameters = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_PACKAGE_PARAMETERS, data)
}

export const setCorePbxServicesParameters = ({
    commit,
    dispatch,
    state
}, data) => {

    commit(types.SET_CORE_PBX_SERVICES_PARAMETERS, data)
}

export const getDaysToRenewal = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/pbx/services/daysToRenewal`, {
                params
            })
            .then((response) => {
                // console.log('data renewal action: ', response.data);
                commit(types.SET_PBX_SERVICES_DAYS_RENEWAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setPbxServicesIncludedData = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_CORE_PBX_SERVICES_INCLUDED_DATA, data)
}

export const setCorePBXServices = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/services/insert', data)
            .then((response) => {

                commit(types.PBX_SERVICE_SAVED, response.data.pbxService)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePBXServices = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/services/update', data)
            .then((response) => {

                // commit(types.PBX_SERVICE_SAVED, response.data.pbxService)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setPbxTenant = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.PBX_TENANT_SAVED, data);
}

// Endpoint to save tenant (api) into database
export const addPbxTenant = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/tenant/insert', data)
            .then((response) => {
                commit(types.PBX_TENANT_SAVED, response.data.pbxTenant)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// Endpoint to get tenant from database
export const getPbxTenant = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/tenant/${params.tenant_id}`)
            .then((response) => {
                // commit(types.PACKAGES_LIST, response.data.packagesList.data)


                resolve(response.data.pbxTenant)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// Endpoint to save did (api) into database
export const addPbxDid = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/did/insert', data)
            .then((response) => {
                commit(types.PBX_DID_SAVED, response.data.pbxDID)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// endpoint to get pbx_ext from database
export const fetchPbxDidBD = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/did/${data.did_number}`)
            .then((response) => {

                /* let ExtPbx = []
                for (const property in response.data.ExtensionByTenantList) {
                    ExtPbx.push({
                        id: property,
                        name: response.data.ExtensionByTenantList[property].name,
                        email: response.data.ExtensionByTenantList[property].email,
                        ext_id: response.data.ExtensionByTenantList[property].ext_id,
                        ext: response.data.ExtensionByTenantList[property].ext,
                        location: response.data.ExtensionByTenantList[property].location,
                        ua_id: response.data.ExtensionByTenantList[property].ua_id,
                        ua_fullname: response.data.ExtensionByTenantList[property].ua_fullname,
                        ua_name: response.data.ExtensionByTenantList[property].ua_name,
                        macaddress: response.data.ExtensionByTenantList[property].macaddress,
                        status: response.data.ExtensionByTenantList[property].status,
                        linenum: response.data.ExtensionByTenantList[property].linenum,
                        protocol: response.data.ExtensionByTenantList[property].protocol,
                    })
                } */

                /* commit(types.BOOTSTRAP_EXTENSION, ExtPbx)
                commit(types.SET_TOTAL_EXTENSION, ExtPbx.length) */
                resolve(response.data.pbxDID);
            })
            .catch((err) => {
                reject(err)
            })
    })
}


// Endpoint to save pbx_services_did 
export const addPbxServiceDid = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/services/did/insert', data)
            .then((response) => {
                // commit(types.PBX_DID_SAVED, response.data.pbxDID)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// Endpoint to save extensions (api) into database
export const addPbxExt = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/ext/insert', data)
            .then((response) => {
                commit(types.PBX_EXT_SAVED, response.data.pbxExtension)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// set extensions states
export const setDataExtensionState = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.BOOTSTRAP_EXTENSION, data.extensionsIncluded)
    commit(types.SET_TOTAL_EXTENSION, data.extensionsIncluded.length)

    commit(types.BOOTSTRAP_EXTENSION_INCLUDE, data.extensionsExcluded)
    commit(types.SET_TOTAL_EXTENSION_INCLUDE, data.extensionsExcluded.length)
}

// set did states
export const setDataDidState = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.BOOTSTRAP_DID, data.didIncluded)
    commit(types.SET_TOTAL_DID, data.didIncluded.length)

    commit(types.BOOTSTRAP_DID_INCLUDE, data.didExcluded)
    commit(types.SET_TOTAL_DID_INCLUDE, data.didExcluded.length)
}

// endpoint to get pbx_ext from database
export const fetchPbxExtensionBD = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/ext/${data.ext_name}`)
            .then((response) => {

                /* let ExtPbx = []
                for (const property in response.data.ExtensionByTenantList) {
                    ExtPbx.push({
                        id: property,
                        name: response.data.ExtensionByTenantList[property].name,
                        email: response.data.ExtensionByTenantList[property].email,
                        ext_id: response.data.ExtensionByTenantList[property].ext_id,
                        ext: response.data.ExtensionByTenantList[property].ext,
                        location: response.data.ExtensionByTenantList[property].location,
                        ua_id: response.data.ExtensionByTenantList[property].ua_id,
                        ua_fullname: response.data.ExtensionByTenantList[property].ua_fullname,
                        ua_name: response.data.ExtensionByTenantList[property].ua_name,
                        macaddress: response.data.ExtensionByTenantList[property].macaddress,
                        status: response.data.ExtensionByTenantList[property].status,
                        linenum: response.data.ExtensionByTenantList[property].linenum,
                        protocol: response.data.ExtensionByTenantList[property].protocol,
                    })
                } */

                /* commit(types.BOOTSTRAP_EXTENSION, ExtPbx)
                commit(types.SET_TOTAL_EXTENSION, ExtPbx.length) */
                resolve(response.data.pbxEXT);
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// Endpoint to save pbx_services_ext 
export const addPbxServiceExt = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/services/ext/insert', data)
            .then((response) => {
                // commit(types.PBX_DID_SAVED, response.data.pbxDID)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchExtensionPBX = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/ware/ext/`, { params: data })
            .then((response) => {
                let ExtPbx = []
                for (const property in response.data.ExtensionByTenantList) {
                    ExtPbx.push({
                        id: property,
                        api_id: property,
                        only_api: response.data.ExtensionByTenantList[property].only_api,
                        db_available: response.data.ExtensionByTenantList[property].db_available,
                        name: response.data.ExtensionByTenantList[property].name,
                        email: response.data.ExtensionByTenantList[property].email,
                        ext_id: response.data.ExtensionByTenantList[property].ext_id,
                        ext: response.data.ExtensionByTenantList[property].ext,
                        location: response.data.ExtensionByTenantList[property].location,
                        ua_id: response.data.ExtensionByTenantList[property].ua_id,
                        ua_fullname: response.data.ExtensionByTenantList[property].ua_fullname,
                        ua_name: response.data.ExtensionByTenantList[property].ua_name,
                        macaddress: response.data.ExtensionByTenantList[property].macaddress,
                        status: response.data.ExtensionByTenantList[property].status,
                        linenum: response.data.ExtensionByTenantList[property].linenum,
                        protocol: response.data.ExtensionByTenantList[property].protocol,
                    });
                }
                if (!data.isEdit) {
                    commit(types.BOOTSTRAP_EXTENSION, ExtPbx)
                    commit(types.SET_TOTAL_EXTENSION, ExtPbx.length)
                } else {
                    commit(types.BOOTSTRAP_EXTENSION_INCLUDE, ExtPbx)
                    commit(types.SET_TOTAL_EXTENSION_INCLUDE, ExtPbx.length)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
                resolve(err)
            })
    })
}

export const fetchDIDPBX = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/ware/did`, { params: data })
            .then((response) => {
                let didPbx = [];
                //console.log('did tenant list: ', response);
                //console.log('did tenant list: ', response.data.DIDByTenantList);
                // Primero, verifica si response.data tiene la estructura de error
                if (response.data.status === false && response.data.error === true) {
                    // Si es así, maneja el error aquí
                    //  console.error(response.data.message.error);
                } else {
                    // Si no hay error, entonces ejecuta el código original
                    if (response.data.DIDByTenantList && response.data.DIDByTenantList.length > 0) {
                        for (const property in response.data.DIDByTenantList) {
                            didPbx.push({
                                id: property,
                                only_api: response.data.DIDByTenantList[property].only_api,
                                db_available: response.data.DIDByTenantList[property].db_available,
                                e164: response.data.DIDByTenantList[property].e164,
                                e164_2: response.data.DIDByTenantList[property].e164_2,
                                ext: response.data.DIDByTenantList[property].ext,
                                number: response.data.DIDByTenantList[property].number,
                                number2: response.data.DIDByTenantList[property].number2,
                                server: response.data.DIDByTenantList[property].server,
                                status: response.data.DIDByTenantList[property].status,
                                type: response.data.DIDByTenantList[property].type,
                                trunk: response.data.DIDByTenantList[property].trunk,
                            });
                        }
                    }
                }

                if (!data.isEdit) {
                    commit(types.BOOTSTRAP_DID, didPbx)
                    commit(types.SET_TOTAL_DID, didPbx.length)
                } else {
                    commit(types.BOOTSTRAP_DID_INCLUDE, didPbx)
                    commit(types.SET_TOTAL_DID_INCLUDE, didPbx.length)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchDIDPbxService = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/services/did/${data.pbx_service_id}`)
            .then((response) => {
                let didPbxService = []

                for (const property in response.data.DIDListByPbxService) {
                    didPbxService.push({
                        id: response.data.DIDListByPbxService[property].id,
                        did_number: response.data.DIDListByPbxService[property].did_number,
                        did_server: response.data.DIDListByPbxService[property].did_server,
                        did_status: response.data.DIDListByPbxService[property].did_status,
                        did_details: response.data.DIDListByPbxService[property].did_details,
                        package_name: response.data.DIDListByPbxService[property].package_name,
                        package_did_rate: response.data.DIDListByPbxService[property].package_did_rate,
                        tenant_code: response.data.DIDListByPbxService[property].tenant_code,
                        tenant_name: response.data.DIDListByPbxService[property].tenant_name,
                        tenant_details: response.data.DIDListByPbxService[property].tenant_details
                    })
                }
                /* commit(types.BOOTSTRAP_DID, didPbx)
                commit(types.SET_TOTAL_DID, didPbx.length) */
                resolve(didPbxService)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchExtPbxService = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/services/ext/${data.pbx_service_id}`)
            .then((response) => {
                let extPbxService = []
                for (const property in response.data.ExtListByPbxService) {

                    extPbxService.push({
                        id: response.data.ExtListByPbxService[property].id,
                        ext_name: response.data.ExtListByPbxService[property].ext_name,
                        ext_email: response.data.ExtListByPbxService[property].ext_email,
                        ext_status: response.data.ExtListByPbxService[property].ext_status,
                        ext_details: response.data.ExtListByPbxService[property].ext_details,
                        package_name: response.data.ExtListByPbxService[property].package_name,
                        package_rate: response.data.ExtListByPbxService[property].package_rate,
                        tenant_code: response.data.ExtListByPbxService[property].tenant_code,
                        tenant_name: response.data.ExtListByPbxService[property].tenant_name,
                        tenant_details: response.data.ExtListByPbxService[property].tenant_details
                    })
                }
                /* commit(types.BOOTSTRAP_DID, didPbx)
                commit(types.SET_TOTAL_DID, didPbx.length) */
                resolve(extPbxService)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchInvoices = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${params.customer_id}/invoices`, {
                params
            })
            .then((response) => {
                commit(types.INVOICES_LIST, response.data.invoicesList.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchEstimates = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${params.customer_id}/estimates`, {
                params
            })
            .then((response) => {
                commit(types.ESTIMATES_LIST, response.data.estimatesList.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxServices = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${params.customer_id}/pbx-services`, {
                params
            })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setConfig = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customers/set-config`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const sendPassword = ({ commit, dispatch }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customers/${data.userId}/send-password`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getConfig = ({ commit, dispatch }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers/${id}/get-config`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectDID = ({
    commit,
    dispatch,
    state
}, data) => {
    //console.log('data: ', data);
    //console.log('state.selectedDid: ', state.selectedPbxDID);
    //console.log('state did: ', state.did);
    commit(types.SET_SELECTED_DID, data)
    if (state.selectedPbxDID.length === state.did.length) {
        commit(types.SET_SELECT_ALL_STATE_DID, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE_DID, false)
    }
}

export const selectAllDID = ({
    commit,
    dispatch,
    state
}) => {
    /* console.log("selectAllDID",state.selectedPbxDID.length,state.did.length) */
    /* if (state.selectedPbxDID.length === state.did.length) {
        commit(types.SET_SELECTED_DID, [])
        commit(types.SET_SELECT_ALL_STATE_DID, false)
    } else {
        let allDIDIds = state.did.map((dd) => dd.id)
        commit(types.SET_SELECTED_DID, allDIDIds)
        commit(types.SET_SELECT_ALL_STATE_DID, true)
    } */
    if (state.selectedPbxDID.length === state.did.length || state.selectedPbxDID.length > 0) {
        commit(types.SET_SELECTED_DID, [])
        commit(types.SET_SELECT_ALL_STATE_DID, false)
    } else {
        let allDIDIds = state.did.map((dd) => dd.number)
        commit(types.SET_SELECTED_DID, allDIDIds)
        commit(types.SET_SELECT_ALL_STATE_DID, true)
    }
}

export const selectDIDInclude = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECTED_DID_INCLUDE, data)
    if (state.selectedPbxDIDToInclude.length === state.did.length) {
        commit(types.SET_SELECT_ALL_STATE_DID_INCLUDE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE_DID_INCLUDE, false)
    }
}

export const selectAllDIDInclude = ({
    commit,
    dispatch,
    state
}) => {
    /* console.log("Inicio",state.selectedPbxDIDToInclude.length,state.didInclude.length) */
    if (state.selectedPbxDIDToInclude.length === state.didInclude.length || state.didInclude.length == 0 || state.selectedPbxDIDToInclude.length > 0) {
        commit(types.SET_SELECTED_DID_INCLUDE, [])
        commit(types.SET_SELECT_ALL_STATE_DID_INCLUDE, false)
    } else {
        let allDIDIds = state.didInclude.map((dd) => dd.number)
        commit(types.SET_SELECTED_DID_INCLUDE, allDIDIds)
        commit(types.SET_SELECT_ALL_STATE_DID_INCLUDE, true)
    }
}


export const setSelectAllStateDID = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECT_ALL_STATE_DID, data)
}

export const setSelectAllStateDIDInclude = ({
    commit,
    dispatch,
    state
}, data) => {
    // console.log('data state: ', data);
    commit(types.SET_SELECT_ALL_STATE_DID_INCLUDE, data)
}

export const setSelectAllStateExtInclude = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECT_ALL_STATE_EXTENSION_INCLUDE, data)
}

export const resetSelectedDID = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.RESET_SELECTED_DID)
}


/*EXTENSION*/
export const selectExtensions = ({
    commit,
    dispatch,
    state
}, data) => {
    // console.log('ext data', data);
    commit(types.SET_SELECTED_EXTENSION, data)
    if (state.selectedPbxExtensions.length === state.extensions.length) {
        commit(types.SET_SELECT_ALL_STATE_EXTENSION, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE_EXTENSION, false)
    }
}

export const selectExtensionsInclude = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECTED_EXTENSION_INCLUDE, data)
    if (state.selectedPbxExtensionsToInclude.length === state.extensions.length) {
        commit(types.SET_SELECT_ALL_STATE_EXTENSION_INCLUDE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE_EXTENSION_INCLUDE, false)
    }
}

export const selectAllExtensions = ({
    commit,
    dispatch,
    state
}) => {
    // console.log('selected extensions longitud', state.selectedPbxExtensions.length);
    if (state.selectedPbxExtensions.length === state.extensions.length || state.selectedPbxExtensions.length > 0) {
        commit(types.SET_SELECTED_EXTENSION, [])
        commit(types.SET_SELECT_ALL_STATE_EXTENSION, false)
    } else {
        let allExtensionIds = state.extensions.map((ext) => ext.ext)

        commit(types.SET_SELECTED_EXTENSION, allExtensionIds)
        commit(types.SET_SELECT_ALL_STATE_EXTENSION, true)
    }
}

export const selectAllExtensionsInclude = ({
    commit,
    dispatch,
    state
}) => {
    /* console.log("Extensions Include",state.selectedPbxExtensionsToInclude.length,state.extensionsInclude.length) */
    if (state.selectedPbxExtensionsToInclude.length === state.extensionsInclude.length || state.selectedPbxExtensionsToInclude.length > 0 || state.extensionsInclude.length == 0) {
        commit(types.SET_SELECTED_EXTENSION_INCLUDE, [])
        commit(types.SET_SELECT_ALL_STATE_EXTENSION_INCLUDE, false)
    } else {
        let allExtensionIds = state.extensionsInclude.map((ext) => ext.ext)
        commit(types.SET_SELECTED_EXTENSION_INCLUDE, allExtensionIds)
        commit(types.SET_SELECT_ALL_STATE_EXTENSION_INCLUDE, true)
    }
}

export const setSelectAllStateExtensions = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECT_ALL_STATE_EXTENSION, data)
}

export const resetSelectedExtensions = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.RESET_SELECTED_EXTENSION, [])
}

// company
export const fetchCompanySettings = ({ commit, dispatch, state }, settings) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get('/api/v1/company/settings', {
                params: {
                    settings,
                },
            })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// 
export const fetchItemGroups = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/packages/item-groups`, { params })
            .then((response) => {
                // commit(types.SET_TAX_TYPES, response.data.taxTypes.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPrefixrateGroups = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/ware/prefixrate/groups`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const billingValidation = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customers/billing-validation`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const fetchOnlyCustomer = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customers-selector`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}