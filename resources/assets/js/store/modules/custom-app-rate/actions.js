import * as types from './mutation-types'

export const fetchCustomAppRate = ({commit}, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/profile/custom-app-rate`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const showCustomAppRate = ({commit}, id) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/profile/custom-app-rate/${id}`)
        .then((response) => {
            resolve(response)
        })
        .catch((err) => {
            reject(err)
        })
    })
}

export const updateCustomAppRate = ({ commit }, data) => {
    return new Promise((resolve, reject) => {
        window.axios.put(`/api/v1/profile/custom-app-rate/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                console.log('error', err)
                reject(err)
            })
    })
}

export const addCustomAppRate  = ({commit}, data) => {
    return new Promise((resolve, reject) => {
        window.axios.post('/api/v1/profile/custom-app-rate', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomAppRate  = ({commit}, id) => {
    return new Promise((resolve, reject) => {
        window.axios.delete(`/api/v1/profile/custom-app-rate/${id}`, )
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// packageAssociateCustomAppRate
export const packageAssociateCustomAppRate = ({commit}, data) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/profile/package-associate-custom-app-rate`, { params: data })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}