import * as types from './mutation-types'
import * as searchTypes from '../search/mutation-types'

export const fetchLeads = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/leads`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchLead = ({ commit, dispatch }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/leads/${id}`)
            .then((response) => {

                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addLead = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/leads', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateLead = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/leads/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomersLeads = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/customer-lead`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const sendEmaillead = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/lead-email', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}