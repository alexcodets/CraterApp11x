import * as types from './mutation-types'
import * as typesCustomer from '../customer/mutation-types'

export const fetchPbxService = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/pbx/service-detail/service/${id}`)
            .then((response) => {
                commit(types.SET_SELECTED_PBX_SERVICE, response.data.response.pbx_service)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAvalaraTaxesItems = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/pbx/service-detail/avalara-taxes-items/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAvalaraTaxes = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/pbx/service-detail/avalara-taxes/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchExtensions = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/ext/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePriceExtension = ({ commit, dispatch, state }, payload) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/pbx/service-detail/update-price-extension`, payload)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const updatePriceDid = ({ commit, dispatch, state }, payload) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/pbx/service-detail/update-price-did`, payload)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchDIDs = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/did/${params.pbx_service_id}`, { params })
            .then((response) => {
                let didPbx = []
                if (response.data.response.service_did.data.length > 2) {
                    for (const property in response.data.response.service_did.data) {

                        didPbx.push({
                            id: response.data.response.service_did.data[property].id,
                            api_id: response.data.response.service_did.data[property].api_id,
                            e164: response.data.response.service_did.data[property].e164,
                            e164_2: response.data.response.service_did.data[property].e164_2,
                            ext: response.data.response.service_did.data[property].ext,
                            number: response.data.response.service_did.data[property].number,
                            number2: response.data.response.service_did.data[property].number2,
                            server: response.data.response.service_did.data[property].server,
                            status: response.data.response.service_did.data[property].status,
                            type: response.data.response.service_did.data[property].type,
                            trunk: response.data.response.service_did.data[property].trunk,
                            price: response.data.response.service_did.data[property].price,

                            pbx_service_did_id: response.data.response.service_did.data[property].pbx_service_did_id,
                        })
                    }
                }
                commit('customer/' + typesCustomer.BOOTSTRAP_DID, didPbx, { root: true })
                commit('customer/' + typesCustomer.SET_TOTAL_DID, didPbx.length, { root: true })
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchItemsPbxService = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/item/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchTaxType = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/services/taxtype/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAdditionalCharges = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/charges/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCallHistory = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/cdr/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchInvoicesPerServicePbx = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/inv/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomAppRate = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/custom-app-rate/${params.pbx_service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomAppRateForPbxService = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/custom-app-rate-pbx-service/${data.pbx_service_id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// fetchCallHistorySearch
export const fetchCallHistorySearch = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service/${params.pbx_service_id}/cdr`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateServiceStatus = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/pbx/service-detail/update-status/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteService = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/pbx/service-detail/delete`, id)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCommandos = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/cdr/${id}/commandos`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const downloadCalls = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/pbx/service-detail/cdr/download`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPrepaidCharges = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service/${params.pbx_service_id}/callhistoryindi`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateExtension = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/pbx/service/${params.pbx_service_id}/ext/${params.pbx_ext_id}`, params.data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchTenantApps = ({ commit, dispatch, state }, { tenantId }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/tenant/${tenantId}/apps`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const fetchTenantAll = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/pbx/tenantall`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const disableTenant = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.put(`/api/v1/cdr-tenant/${id}/disable`)
            .then((response) => {
                resolve(response)
            }).catch((err) => {
                reject(err)
            })
    })
}
export const enableTenant = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.put(`/api/v1/cdr-tenant/${id}/enable`)
            .then((response) => {
                resolve(response)
            }).catch((err) => {
                reject(err)
            })
    })
}

export const tenantImportCdr = ({ commit, dispatch, state }, { id, days }) => {
    return new Promise((resolve, reject) => {
        window.axios.post(`/api/v1/command/pbx/tenant-import-cdr/${id}`, { days })
            .then((response) => {
                resolve(response)
            }).catch((err) => {
                reject(err)
            })
    })
}


export const jobInfoPBXService = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service/${data.pbx_service}/jobs/import-cdr`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const softDeletePBXService = ({ commit, dispatch, state }, pbxService) => {
    return new Promise((resolve, reject) => {
        window.axios
            .delete(`/api/v1/pbx/service/${pbxService.id}/jobs/import-cdr/delete`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const hardDeletePBXService = ({ commit, dispatch, state }, pbxService) => {
    return new Promise((resolve, reject) => {
        window.axios
            .delete(`/api/v1/pbx/service/${pbxService.id}/jobs/import-cdr/delete`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAdditionalChargesService = ({ commit, dispatch, state }, params) => {
    //console.log(params)
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/get-additional-charges/${params.service_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addPbxServicesItem = ({ commit, dispatch, state }, payload) => {


    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/pbx/services/additem`, payload)
            .then((response) => {
                //  console.log(response)
                resolve(response)
            })
            .catch((err) => {
                // console.log(err)
                reject(err)
            })
    })
}