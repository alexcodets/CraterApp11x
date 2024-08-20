import * as types from './mutation-types'

export const fetchCustomerNotes = ({ commit, dispatch, state }, params) => {

    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-note`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_CUSTOMER_NOTE, response.data.customerNote.data)
                commit(types.SET_TOTAL_CUSTOMER_NOTE, response.data.customerNoteTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomerNote = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-note/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// export const fetchCustomerNote = ({ commit, dispatch }, params) => {
//   return new Promise((resolve, reject) => {
//     window.axios
//       .get(`/api/v1/customer-note/${params}`, { params })
//       .then((response) => {
//         commit(types.SET_SELECTED_VIEW_CUSTOMER_NOTE, response.data)
//         resolve(response)
//       })
//       .catch((err) => {
//         reject(err)
//       })
//   })
// }

export const addCustomerNote = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/customer-note', data)
            .then((response) => {
                commit(types.ADD_CUSTOMER_NOTE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateCustomerNote = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/customer-note/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomerNote = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customer-note/delete`, id)
            .then((response) => {
                // commit(types.DELETE_CUSTOMER_NOTE, id)
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