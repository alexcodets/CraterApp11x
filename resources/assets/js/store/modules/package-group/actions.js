import * as types from './mutation-types'

export const fetchGroups = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/groups`, { params })
        .then((response) => {
          commit(types.BOOTSTRAP_GROUPS, response.data.groups.data)
          commit(types.SET_TOTAL_GROUPS, response.data.groupTotalCount)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  export const fetchGroup = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/groups/${params}`)
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const fetchViewGroup = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/groups/${params}`, { params })
        .then((response) => {
          commit(types.SET_SELECTED_VIEW_GROUP, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const addGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post('/api/v1/groups', data)
        .then((response) => {
          commit(types.ADD_GROUP, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const updateGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .put(`/api/v1/groups/${data.id}`, data)
        .then((response) => {
          if (response.data.success) {
            commit(types.UPDATE_GROUP, response.data)
          }
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const deleteGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post(`/api/v1/groups/delete`, id)
        .then((response) => {
          commit(types.DELETE_GROUP, id)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const deleteMultipleGroups = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
      window.axios
        .delete(`/api/v1/groups`, { ids: state.selectedGroups })
        .then((response) => {
          commit(types.DELETE_MULTIPLE_GROUPS, state.selectedGroups)
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
  
  export const selectAllGroups = ({ commit, dispatch, state }) => {
    if (state.selectedGroups.length === state.groups.length) {
      commit(types.SET_SELECTED_GROUPS, [])
      commit(types.SET_SELECT_ALL_STATE, false)
    } else {
      let allGroupIds = state.groups.map((cust) => cust.id)
      commit(types.SET_SELECTED_GROUPS, allGroupIds)
      commit(types.SET_SELECT_ALL_STATE, true)
    }
  }
  
  export const selectGroup = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECTED_GROUPS, data)
    if (state.selectedGroups.length === state.groups.length) {
      commit(types.SET_SELECT_ALL_STATE, true)
    } else {
      commit(types.SET_SELECT_ALL_STATE, false)
    }
  }
  
  export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_GROUP)
  }

  export const fetchPackageMembership = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
      .get(`/api/v1/groups/packages`, { params })
        .then((response) => {          
          commit(types.SET_GROUP_PACKAGES, response.data.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }