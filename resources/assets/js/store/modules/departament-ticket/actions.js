import * as types from './mutation-types'

export const fetchDepartaments = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/ticket/departaments`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_DEPARTAMENTS, response.data.departaments.data)
                commit(types.SET_TOTAL_DEPARTAMENTS, response.data.departamentTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchDepartament = ({ commit, dispatch, state }, params) => {

    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/ticket/departaments/${params}`)
            .then((response) => {

                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewDepartament = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/ticket/departaments/${params.id}`, { params })
            .then((response) => {

                commit(types.SET_SELECTED_VIEW_DEPARTAMENT, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addDepartament = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/ticket/departaments', data)
            .then((response) => {
                commit(types.ADD_DEPARTAMENT, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateDepartament = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/ticket/departaments/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_DEPARTAMENT, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteDepartament = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/ticket/departaments/delete/${id}`)
            .then((response) => {
                commit(types.DELETE_DEPARTAMENT, id)
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

export const resetSelectedDepartament = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_DEPARTAMENT)
}

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_DEPARTAMENT)
}

export const getUsers = ({ commit, dispatch, state }) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/ticket/departaments/list-users`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getUsersByDepartment = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/ticket/departaments/${params.id}/users`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getTicketsByDepartment = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/ticket/departaments/${params.id}/tickets`, {params})
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}