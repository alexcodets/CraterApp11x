import * as types from './mutation-types'

export const fetchCustomerTickets = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-ticket`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_CUSTOMERTICKETS, response.data.customerTicket.data)
                commit(types.SET_TOTAL_CUSTOMERTICKETS, response.data.customerTicketTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomerTicket = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-ticket/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

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
}

export const addCustomerTicket = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/customer-ticket', data)
            .then((response) => {
                commit(types.ADD_CUSTOMERTICKET, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateCustomerTicket = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/customer-ticket/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_CUSTOMERTICKET, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomerTicket = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/customer-ticket/delete`, id)
            .then((response) => {
                commit(types.DELETE_CUSTOMERTICKET, id)
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

export const resetSelectedCustomerTicket = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_CUSTOMERTICKET)
}

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_CUSTOMERTICKET)
}


export const getListUsers = ({ commit, dispatch, state }) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/customer-ticket/list-users`)
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
        window.axios.get(`/api/v1/customer-ticket/list-users-customers`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
