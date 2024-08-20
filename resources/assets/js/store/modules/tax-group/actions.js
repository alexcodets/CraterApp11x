import * as types from './mutation-types'

export const fetchTaxGroups = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/tax-groups`, { params })
        .then((response) => {
          commit(types.BOOTSTRAP_TAX_GROUPS, response.data.tax_groups.data)
          commit(types.SET_TOTAL_TAX_GROUPS, response.data.taxGroupTotalCount)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  export const fetchTaxGroup = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/tax-groups/${params}`)
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const fetchViewTaxGroup = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/tax-groups/${params}`, { params })
        .then((response) => {
          commit(types.SET_SELECTED_VIEW_TAX_GROUP, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const addTaxGroup = ({ commit, dispatch, state }, data) => {    
    return new Promise((resolve, reject) => {
      window.axios
        .post('/api/v1/tax-groups', data)
        .then((response) => {         
          commit(types.ADD_TAX_GROUP, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const updateTaxGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .put(`/api/v1/tax-groups/${data.id}`, data)
        .then((response) => {
          if (response.data.success) {
            commit(types.UPDATE_TAX_GROUP, response.data)
          }
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const deleteTaxGroup = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post(`/api/v1/tax-groups/delete`, id)
        .then((response) => {
          commit(types.DELETE_TAX_GROUP, id)
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
    
  export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_TAX_GROUP)
  }

  export const fetchTaxMembership = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
      .get(`/api/v1/tax-groups/taxes`, { params })
        .then((response) => {          
          commit(types.SET_GROUP_TAXES, response.data.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }