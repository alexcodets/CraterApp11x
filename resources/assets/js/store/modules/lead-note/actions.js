import * as types from './mutation-types'
import * as searchTypes from '../search/mutation-types'



export const fetchLeadNotes = ({ commit, dispatch, state }, params) => {

    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/lead-notes`, { params })
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
            .post('/api/v1/lead-notes', data)
            .then((response) => {

                resolve(response)
            })
            .catch((err) => {

                reject(err)
            })
    })
}

export const fetchLeadNote = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/lead-notes/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateLeadNote = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/lead-notes/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteLeadNote = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/lead-notes/delete`, id)
            .then((response) => {
                // commit(types.DELETE_CUSTOMER_NOTE, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchLeadSingleNote = ({ commit, dispatch }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/lead-notes/${id}`)
            .then((response) => {

                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}