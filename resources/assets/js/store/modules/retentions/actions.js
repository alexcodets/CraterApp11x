import * as types from './mutation-types'

export const fetchRetentions = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/retentions`, { params })
        .then((response) => {
          commit(types.BOOTSTRAP_RETENTIONS, response.data.retentions.data)
          //console.log('total: ', response.data.retentions.total);
          commit(types.SET_TOTAL_RETENTIONS, response.data.retentions.total)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

 export const fetchRetention = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/api/v1/retention/${id}`)
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
 /* 
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
  }*/
  
  export const addRetention = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post('/api/v1/retentions', data)
        .then((response) => {
          // commit(types.ADD_TAX_GROUP, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const updateRetention = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .put(`/api/v1/retentions/update`, data)
        .then((response) => {
          if (response.data.success) {
            // commit(types.UPDATE_TAX_GROUP, response.data)
          }
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  
  export const deleteRetention = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post(`/api/v1/retentions/delete/${id}`, id)
        .then((response) => {
          // commit(types.DELETE_TAX_GROUP, id)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
  /*
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
  }*/