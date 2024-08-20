import * as types from './mutation-types'
import * as typesCustomer from '../customer/mutation-types'

export const fetchPbxService = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/service/${id}`)
            .then((response) => {
                commit(types.SET_SELECTED_PBX_SERVICE, response.data.response.pbx_service)
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

export const fetchDIDs = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/service-detail/did/${params.pbx_service_id}`, { params })
            .then((response) => {
                console.log("C")
                let didPbx = []
                if(response.data.response.service_did.data.length > 2){
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
                        })
                    }
                }
                // console.log('did pbx: ', didPbx);
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