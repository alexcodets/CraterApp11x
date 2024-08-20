import * as types from './mutation-types'

export const fetchCustomerContacts = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer/${params.customer_id}/contacts`, { params })
            .then((response) => {
                /* commit(types.CUSTOMER_CONTACT, response.data.customerAddress.data)*/ 
                commit(types.SET_TOTAL_CUSTOMER_CONTACTS, response.data.totalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomerContact = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/contacts/${id}`)
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

export const addCustomerContact = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
        .post('/api/v1/contacts', data)
        .then((response) => {
            // commit(types.ADD_CUSTOMER_TICKET, response.data)
            resolve(response)
        })
        .catch((err) => {
            reject(err)
        })
    })
}

export const updateCustomerContact = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/contacts/update`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_CUSTOMER_CONTACT, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomerContact = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/contacts/delete`, data)
            .then((response) => {
                commit(types.DELETE_CUSTOMER_CONTACT, data.id)
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