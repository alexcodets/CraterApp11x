import * as types from './mutation-types'

export const fetchAuthorizations = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/authorize-settings`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_AUTHORIZATIONS, response.data.authorize.data)
                commit(types.SET_TOTAL_AUTHORIZATIONS, response.data.dataTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAuthorization = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/authorize-settings/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewAuthorization = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/authorize-settings/${params}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_AUTHORIZATION, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addAuthorization = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-settings', data)
            .then((response) => {
                commit(types.ADD_AUTHORIZATION, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateAuthorization = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/authorize-settings/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_AUTHORIZATION, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteAuthorization = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/authorize-settings/delete`, id)
            .then((response) => {
                commit(types.DELETE_AUTHORIZATION, id)
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

export const resetSelectedAuthorization = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_AUTHORIZATION)
}

export const addAuthorize = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-charge', data)
            .then((response) => {
                commit(types.ADD_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const addAuthorizeWithoutLogin = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-charge-without-login', data)
            .then((response) => {
                commit(types.ADD_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const saveAuthorizeDB = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-save', data)
            .then((response) => {
                commit(types.SAVE_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const saveAuthorizeDBWithoutLogin = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-save-without-login', data)
            .then((response) => {
                commit(types.SAVE_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const voidAuthorize = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {

        const headers = {
            'Content-Type': 'application/json'
        }

        window.axios
            .post('/api/v1/authorize-void', data, {
                headers
            })
            .then((response) => {
                commit(types.VOID_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const refundedAuthorize = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {

        const headers = {
            'Content-Type': 'application/json'
        }

        window.axios
            .post('/api/v1/authorize-refunded', data, { headers })
            .then((response) => {
                commit(types.REFUNDED_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setAuthorizeDefault = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/authorize-settings/set-default/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateAuthorizeStatus = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-settings/change-status', data)
            .then((response) => {
                commit(types.UPDATE_STATUS_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addAuthorizeACH = ({ commit, dispatch, state }, data) => {
    console.log('add  authorization ACH')
    return new Promise((resolve, reject) => {
        const headers = {
            'Content-Type': 'application/json'
        }
        window.axios
            .post('/api/v1/authorize-ach', data, {
                headers: headers
            })
            .then((response) => {
                commit(types.ADD_AUTHORIZE_ACH, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const saveAuthorizeACH = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/authorize-save-ach', data)
            .then((response) => {
                commit(types.SAVE_AUTHORIZE_ACH, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addAuthorizePaypal = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        const headers = {
            'Content-Type': 'application/json'
        }
        window.axios
            .post('/api/v1/authorize-paypal', data, {
                headers: headers
            })
            .then((response) => {
                commit(types.ADD_AUTHORIZE_PAYPAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const savePaypalDB = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/paypal-checkout', data)
            .then((response) => {
                commit(types.SAVE_PAYPAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const chargePaypalPro = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/payment-paypalpro', data)
            .then((response) => {
                // commit(types.SAVE_PAYPAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}