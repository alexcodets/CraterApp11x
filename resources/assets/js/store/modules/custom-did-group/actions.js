import * as types from './mutation-types'

export const fetchCustomDidGroups = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/custom-did-groups`, {params})
            .then((response) => {
                commit(types.BOOTSTRAP_CUSTOM_DID_GROUPS, response.data.customDidGroups.data)
                commit(types.SET_TOTAL_CUSTOM_DID_GROUPS, response.data.customDidGroupsTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addCustomDidGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/custom-did-groups', data)
            .then((response) => {
                commit(types.ADD_CUSTOM_DID_GROUP, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomDidGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/custom-did-groups/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewCustomDidGroup = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/custom-did-groups/${params.id}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_CUSTOM_DID_GROUP, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateCustomDidGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/custom-did-groups/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_CUSTOM_DID_GROUP, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteCustomDidGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/custom-did-groups/delete`, id)
            .then((response) => {
                commit(types.DELETE_CUSTOM_DID_GROUP, id)
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

export const selectAllCustomDidGroups = ({ commit, dispatch, state }) => {
    if (state.selectedCustomDidGroups.length === state.customDidGroups.length) {
        commit(types.SET_SELECTED_CUSTOM_DID_GROUPS, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allCustomDidGroupsIds = state.customDidGroups.map((ctmGr) => ctmGr.id)
        commit(types.SET_SELECTED_CUSTOM_DID_GROUPS, allCustomDidGroupsIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const setClonedDidGroup = ({ commit, dispatch, state }, data) => {
    commit(types.SET_CLONED_DID_GROUP, data)
}

export const resetClonedData = ({ commit, dispatch, state }) => {
    commit(types.RESET_CLONED_DID_GROUP)
}

export const importParse = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/custom-did-groups/import-parse', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const importProcess = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/custom-did-groups/import-process', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}