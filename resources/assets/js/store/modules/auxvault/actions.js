import * as types from './mutation-types'


export const fetchAuxVault = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/aux-vault-settings`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_AUTHORIZATIONS, response.data.data)
                commit(types.SET_TOTAL_AUTHORIZATIONS, response.data.dataTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addAuxVault = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/aux-vault-settings', data)
            .then((response) => {
                commit(types.ADD_AUTHORIZATION, response)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAuxvault = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/aux-vault-settings/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const updateAuxvault = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/aux-vault-settings/${data.id}`, data)
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

export const deleteAuxvault = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/aux-vault-settings/delete`, id)
            .then((response) => {
                commit(types.DELETE_AUTHORIZATION, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const updatedefaultAuxvault = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/aux-vault-settings/set-default/${data.id}`, data)
            .then((response) => {

                commit(types.UPDATE_STATUS_AUTHORIZE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}