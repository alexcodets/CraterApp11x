import * as types from './mutation-types'

export const fetchInternacionals = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_INTERNACIONALS, response.data.internacionals.data ? response.data.internacionals.data : response.data.internacionals)
                commit(types.SET_TOTAL_INTERNACIONALS, response.data.internacionalTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchInternacional = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewInternacional = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate/${params}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_INTERNACIONAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addInternacional = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/profile/international-rate', data)
            .then((response) => {
                commit(types.ADD_INTERNACIONAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateInternacional = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/profile/international-rate/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {

                    commit(types.UPDATE_INTERNACIONAL, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteInternacional = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/profile/international-rate/delete`, { id })
            .then((response) => {
                commit(types.DELETE_INTERNACIONAL, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const CargarCustomDestination = ({ commit, dispatch, state }) => {

    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate/prefix-rate`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const ImportarCsvDestination = ({ commit, dispatch, state }, formData) => {
    return new Promise((resolve, reject) => {
        window.axios
        .post( '/api/v1/profile/international-rate/import-excel',
            formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        ).then((response) => {
            resolve(response)
        })
        .catch((err) => {
            reject(err)
        });
        })
}



export const setSelectAllState = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const resetSelectedInternacional = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_INTERNACIONAL)
}

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_INTERNACIONAL)
}

/* 
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
} */