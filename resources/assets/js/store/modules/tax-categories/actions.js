import * as types from './mutation-types'

export const fetchTaxCategories = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-categories`, { params })
      .then((response) => {
        commit(types.SET_TAX_CATEGORIES, response.data.taxCategories.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
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

export const addTaxCategory = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/tax-categories', data)
      .then((response) => {
        commit(types.ADD_TAX_CATEGORY, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchTaxCategory = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-categories/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateTaxCategory = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/tax-categories/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_TAX_CATEGORY, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
/*
export const deleteTaxType = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/tax-types/${id}`)
      .then((response) => {
        if (response.data.success) {
          commit(types.DELETE_TAX_TYPE, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
 */