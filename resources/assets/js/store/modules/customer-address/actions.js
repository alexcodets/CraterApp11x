import * as types from './mutation-types'

export const fetchCustomerAddresses = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-address`, { params })
            .then((response) => {
                commit(types.CUSTOMER_ADDRESS, response.data.customerAddress.data)
                commit(types.SET_TOTAL_CUSTOMER_ADDRESS, response.data.customerAddressTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomerAddress = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-address/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
/*
export const fetchViewCustomerTicket = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-ticket/${params}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_CUSTOMERTICKET, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}*/

export const addAddress = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
        .post('/api/v1/customer-address', data)
        .then((response) => {
            // commit(types.ADD_CUSTOMER_TICKET, response.data)
            resolve(response)
        })
        .catch((err) => {
            reject(err)
        })
    })
}

export const updateCustomerAddress = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/customer-address/update`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_CUSTOMER_ADDRESS, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomerAddress = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customer-address/delete`, data)
            .then((response) => {
                commit(types.DELETE_CUSTOMER_ADDRESS, data.id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
/*
export const setSelectAllState = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const resetSelectedCustomerTicket = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_CUSTOMERTICKET)
}

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_CUSTOMERTICKET)
}

export const getListUsers = ({ commit, dispatch, state }) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/customer-ticket-list-users/list-users`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getListUsersCustomers = ({ commit, dispatch, state }) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/customer-ticket-list-custom/list-users-customers`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getServicesByCustomer = ({ commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-ticket/${params.customer_id}/services`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getPbxServicesByCustomer = ({ commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-ticket/${params.customer_id}/pbx-services`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
} */