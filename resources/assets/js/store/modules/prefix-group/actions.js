import * as types from './mutation-types'

export const fetchPrefixGroups = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/prefix-groups`, {params})
            .then((response) => {
                commit(types.BOOTSTRAP_PREFIX_GROUPS, response.data.prefixGroups.data)
                commit(types.SET_TOTAL_PREFIX_GROUPS, response.data.prefixGroupsTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addPrefixGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/prefix-groups', data)
            .then((response) => {
                commit(types.ADD_PREFIX_GROUP, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPrefixGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/prefix-groups/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewPrefixGroup = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/prefix-groups/${params.id}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_PREFIX_GROUP, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// View (/corePBX/billing-templates/prefix-groups/id/view)
export const fetchViewPrefixGroupNew = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/prefix-groups-new`, {
                params
            })
            .then((response) => {
                //commit(types.SET_INVOICES, response.data.invoices.data)
                //commit(types.SET_TOTAL_INVOICES, response.data.invoiceTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
//

export const updatePrefixGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/prefix-groups/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_PREFIX_GROUP, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deletePrefixGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/prefix-groups/delete`, id)
            .then((response) => {
                commit(types.DELETE_PREFIX_GROUP, id)
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

export const selectAllPrefixGroups = ({ commit, dispatch, state }) => {
    if (state.selectedPrefixGroups.length === state.prefixGroups.length) {
        commit(types.SET_SELECTED_PREFIX_GROUPS, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allPrefixGroupsIds = state.prefixGroups.map((pfxGr) => pfxGr.id)
        commit(types.SET_SELECTED_PREFIX_GROUPS, allPrefixGroupsIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}