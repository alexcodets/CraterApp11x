import * as types from './mutation-types'

export const fetchProviders = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/providers`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_PROVIDERS, response.data.providers.data)
                commit(types.SET_TOTAL_PROVIDERS, response.data.providerTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchProvider = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/providers/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewProvider = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/providers/${params}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_PROVIDER, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addProvider = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/providers', data)
            .then((response) => {
                commit(types.ADD_PROVIDER, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateProvider = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/providers/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_PROVIDER, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteProvider = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/providers/delete`, id)
            .then((response) => {
                commit(types.DELETE_PROVIDER, id)
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

export const resetSelectedProvider = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_PROVIDER)
}

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_PROVIDER)
}


export const setPrefixpro = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.post(`/api/v1/providers/set-prefix`, params)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}