import * as types from './mutation-types'

export const fetchItemGroups = ({commit, dispatch, state}, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/item-groups`, {params})
      .then((response) => {
        commit(types.BOOTSTRAP_ITEM_GROUPS, response.data.itemGroups.data)
        commit(types.SET_TOTAL_ITEM_GROUPS, response.data.itemGroupsTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchItemGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/item-groups/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewItemGroup = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/item-groups/${params.id}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_ITEM_GROUP, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addItemGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/item-groups', data)
            .then((response) => {
                commit(types.ADD_ITEM_GROUP, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateItemGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/item-groups/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_ITEM_GROUP, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteItemGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/item-groups/delete`, id)
            .then((response) => {
                commit(types.DELETE_ITEM_GROUP, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteMultipleItemGroups = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/item-groups/delete`, { ids: state.selectedItemGroups })
            .then((response) => {
                commit(types.DELETE_MULTIPLE_ITEM_GROUPS, state.selectedItemGroups)
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

export const selectAllItemGroups = ({ commit, dispatch, state }) => {
    if (state.selectedItemGroups.length === state.itemGroups.length) {
        commit(types.SET_SELECTED_ITEM_GROUPS, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allItemGroupsIds = state.itemGroups.map((itGr) => itGr.id)
        commit(types.SET_SELECTED_ITEM_GROUPS, allItemGroupsIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const selectItemGroup = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECTED_ITEM_GROUPS, data)
    if (state.selectedItemGroups.length === state.itemGroups.length) {
        commit(types.SET_SELECT_ALL_STATE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE, false)
    }
}

export const resetSelectedItemGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_ITEM_GROUP)
}

export const uploadPicture = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/item-groups/upload-picture', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
