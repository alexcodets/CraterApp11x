import * as types from './mutation-types'

export const fetchFailedPaymentHistory = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/failed-payment-history`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_FAILED_PAYMENT_HISTORY, response.data.failed_payment_history.data)
                commit(types.SET_TOTAL_FAILED_PAYMENT_HISTORY, response.data.failedPaymentHistoryTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addFailedPaymentHistory = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/failed-payment-history', data)
            .then((response) => {
                commit(types.ADD_FAILED_PAYMENT_HISTORY, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const SavePayFailedWithoutLogin = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/failed-payment-history-without', data)
            .then((response) => {
                commit(types.ADD_FAILED_PAYMENT_HISTORY, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}