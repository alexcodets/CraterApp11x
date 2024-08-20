import * as types from './mutation-types'

export const fetchTaxAgencies = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-agency`, { params })
      .then((response) => {
        commit(types.SET_TAX_AGENCIES, response.data.taxAgencies.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const resetSelectedTaxAgency = ({ commit, dispatch, state }) => {
  commit(types.RESET_SELECTED_TAX_AGENCIES)
}

/* export const fetchTaxTypesList = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-types?limit=500`)
      .then((response) => {
        commit(types.SET_TAX_TYPES, response.data.taxTypes)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}*/

export const addTaxAgency = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/tax-agency', data)
      .then((response) => {
        commit(types.ADD_TAX_AGENCY, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchTaxAgency = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-agency/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateTaxAgency = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/tax-agency/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_TAX_AGENCY, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
} 

export const deleteTaxAgency = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/tax-agency/${id}`)
      .then((response) => {
        if (response.data.success) {
          commit(types.DELETE_TAX_AGENCY, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}