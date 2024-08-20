import * as types from './mutation-types'

export const fetchRoles = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/roles`, { params })
      .then((response) => {
        if(!params.list){
          commit(types.SET_ROLES, response.data.roles.data)
          commit(types.SET_TOTAL_ROLES, response.data.roles.length)
          resolve(response)
        } else {
          resolve(response.data)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}
export const fetchPermissions = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/permissions`, { params })
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const showRoleWithPermissions = ({ commit, dispatch, state }, roleId) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/roles/${roleId}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addRole = ({ commit, dispatch, state }, formData) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/v1/roles`, formData)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteRole = ({ commit, dispatch, state }, roleId) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/v1/roles/${roleId}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchRole = ({ commit, dispatch, state }, roleId) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/roles/${roleId}`)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateRole = ({ commit }, payload) => {
  return new Promise((resolve, reject) => {
    window.axios.put(`/api/v1/roles/${payload.id}`, payload)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchUsers = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/users`, { params })
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const assignRole = ({ commit, dispatch, state }, formData) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/v1/assign-role`, formData)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
