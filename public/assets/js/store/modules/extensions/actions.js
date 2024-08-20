import * as types from './mutation-types'

export const fetchExtensions = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/extensions`, {
                params
            })
            .then((response) => {
                commit(types.BOOTSTRAP_EXTENSIONS, response.data.profileExtensions.data)
                commit(types.SET_TOTAL_EXTENSIONS, response.data.profileExtensions.total)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchExtensionsnp = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/extensions/np`, {
                params
            })
            .then((response) => {
                commit(types.BOOTSTRAP_EXTENSIONS, response.data.profileExtensions)
                commit(types.SET_TOTAL_EXTENSIONS, response.data.profileExtensions.length)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchOneExtension = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/extensions/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateExtension = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/profile/extensions/update/${data.id}`, data)
            .then((response) => {

                if (response.data.success) {
                    commit(types.UPDATE_EXTENSIONS, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                console.log('error', err)
                reject(err)
            })
    })
}

export const addExtension = ({
    commit,
    dispatch,
    state
}, data) => {

    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/profile/extensions/insert', data)
            .then((response) => {
                //commit(types.ADD_EXTENSIONS, response.data)

                resolve(response)
            })
            .catch((err) => {
                console.log("DATOS INSERTADOS ERROR", err)
                reject(err)
            })
    })
}

export const deleteExtension = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {


        window.axios
            .post(`/api/v1/profile/extensions/delete/${id}`, )
            .then((response) => {
                if (response.data.error) {
                    resolve(response)
                } else {
                    //commit(types.DELETE_EXTENSIONS, id)
                    resolve(response)
                }
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchItems = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/item-groups/delete`, {
                ids: state.selectedItemGroups
            })
            .then((response) => {
                commit(types.DELETE_MULTIPLE_ITEM_GROUPS, state.selectedItemGroups)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setSelectAllState = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllItem = ({
    commit,
    dispatch,
    state
}) => {
    if (state.selectedItemGroups.length === state.itemGroups.length) {
        commit(types.SET_SELECTED_ITEM_GROUPS, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allItemGroupsIds = state.itemGroups.map((itGr) => itGr.id)
        commit(types.SET_SELECTED_ITEM_GROUPS, allItemGroupsIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const selectExtension = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECTED_EXTENSIONS, data)
    if (state.selectedExtensions.length === state.selectedExtensions.length) {
        commit(types.SET_SELECT_ALL_STATE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE, false)
    }
}

export const resetSelectedItem = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.RESET_SELECTED_ITEM_GROUP)
}

export const fetchItemUnits = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/units`, {
                params
            })
            .then((response) => {
                commit(types.SET_ITEM_UNITS, response.data.units.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCharges = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/aditional-charges`, {
                params
            })
            .then((response) => {
                // commit(types.BOOTSTRAP_ITEMS, response.data.items.data)
                // commit(types.SET_TOTAL_ITEMS, response.data.itemTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const setExtension = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.RESET_EXTENSIONS)
    commit(types.SET_EXTENSIONS, data)
}