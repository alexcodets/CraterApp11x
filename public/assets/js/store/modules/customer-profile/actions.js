import * as types from './mutation-types'

export const fetchLoggedInCustomer = ({commit, dispatch}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/stats`, {params})
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_CUSTOMER, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchServices = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/services`, {params})
            .then((response) => {
                commit(types.SERVICES_LIST, response.data.packagesList.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchInvoices = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/invoices`, {params})
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchEstimates = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/estimates`, {params})
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchExpenses = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/expenses`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPayments = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/payments`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxServices = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/pbx-services`, {params})
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
          .get(`/api/v1/customer/${id}/get-config/`)
          .then((response) => {
              resolve(response)
          })
          .catch((err) => {
              reject(err)
          })
    })
}