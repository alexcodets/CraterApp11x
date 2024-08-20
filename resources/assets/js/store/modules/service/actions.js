import * as types from './mutation-types'

export const fetchService = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/services/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewService = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/services/${id}`)
            .then((response) => {
                commit(types.SET_SELECTED_SERVICE, response.data.service)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchInvoicesPerService = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/services/inv/${params.customer_package_id}`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/* export const fetchResponse = ({ commit, dispatch, state }, response) => {
    
    return new Promise((resolve, reject) => {
                commit(types.SET_SELECTED_SERVICE, response)
                resolve(response)
    })
} */

export const updateService = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/services/${data.id}`, data)
            .then((response) => {
                //console.log(response);
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
            .post(`/api/v1/services/delete`, id)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchServiceAll = ({commit}, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/services-all`, {params: params})
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}