import * as types from './mutation-types'

export const fetchCategories = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/categories-toll-free`, { params })
      .then((response) => {
        commit(types.SET_CATEGORIESTOLLFREE, response.data.categories.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchCategory = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/categories-toll-free/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addCategory = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/categories-toll-free', data)
      .then((response) => {
        commit(types.ADD_CATEGORYTOLLFREE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateCategory = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/categories-toll-free/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_CATEGORYTOLLFREE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteCategory = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/categories-toll-free/${id}`)
      .then((response) => {
        if (response.data.success) {
          commit(types.DELETE_CATEGORYTOLLFREE, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
