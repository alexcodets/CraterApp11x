import * as types from './mutation-types'

export const fetchDIDs = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/did`, {
                params
            })
            .then((response) => {


                commit(types.BOOTSTRAP_DID, response.data.profileDID)
                commit(types.SET_TOTAL_DID, response.data.profileDID.length)
                resolve(response)
            })
            .catch((err) => {
                console.log("CATCH ERROR DE PROFILE DID:", err)
                reject(err)
            })
    })
}

export const fetchOneDID = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/did/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })

    })
}

export const addDID = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/profile/did/insert', data)
            .then((response) => {
                commit(types.ADD_DID, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateDID = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/profile/did/update/${data.id}`, data)
            .then((response) => {

                if (response.data.success) {
                    commit(types.UPDATE_DID, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteDID = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/profile/did/delete/${id}`, )
            .then((response) => {
                if (response.data.error) {
                    resolve(response)
                } else {
                    commit(types.DELETE_DID, id)
                    resolve(response)
                }
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteMultipleDID = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/items/delete`, {
                ids: state.selectedDID
            })
            .then((response) => {
                commit(types.DELETE_MULTIPLE_DID, state.selectedDID)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectDID = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECTED_DID, data)
    if (state.selectedDID.length === state.did.length) {
        commit(types.SET_SELECT_ALL_STATE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE, false)
    }
}

export const selectAllDID = ({
    commit,
    dispatch,
    state
}) => {
    if (state.selectedDID.length === state.did.length) {
        commit(types.SET_SELECTED_DID, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allDIDIds = state.did.map((dd) => dd.id)
        commit(types.SET_SELECTED_DID, allDIDIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const setSelectAllState = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const resetSelectedDID = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.RESET_SELECTED_DID)
}