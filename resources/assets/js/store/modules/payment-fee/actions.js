import * as types from './mutation-types'

export const fetchPaymentFees = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/payment-fees`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_PAYMENT_FEES, response.data.response.PaymentGatewaysFee)
                commit(types.SET_TOTAL_PAYMENT_FEES, response.data.totalPaymentFees)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPaymentFee = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/payment-fees/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewPaymentFee = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/payment-fees/${params}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_PAYMENT_FEE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addPaymentFee = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/payment-fees', data)
            .then((response) => {
                commit(types.ADD_PAYMENT_FEE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePaymentFee = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/payment-fees/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_PAYMENT_FEE, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deletePaymentFee = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/payment-fees/delete`, id)
            .then((response) => {
                commit(types.DELETE_PAYMENT_FEE, id)
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

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_PAYMENT_FEE)
}