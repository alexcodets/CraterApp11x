import * as types from './mutation-types'

export const fetchBandwidths = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/bandwidth`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_BANDWIDTHS, response.data.bandwidths.data)
                commit(types.SET_TOTAL_BANDWIDTHS, response.data.bandwidthTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchBandwidth = ({ commit, dispatch }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/bandwidth/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addBandwidth = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/bandwidth', data)
            .then((response) => {
                commit(types.ADD_BANDWIDTH, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateBandwidth = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/bandwidth/${data.id}`, data)
            .then((response) => {
                commit(types.UPDATE_BANDWIDTH, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteBandwidth = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/bandwidth/delete`, id)
            .then((response) => {
                commit(types.DELETE_BANDWIDTH, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateDefaultBandwidth = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/bandwidth/update-default`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}