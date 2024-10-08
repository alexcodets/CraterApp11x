import { reject } from 'lodash'
import * as types from './mutation-types'

export const fetchMobileSetting = ({ commit, dispatch, state }, idCompany = null) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/mobile-settings/${idCompany}`)
      .then((response) => {
        // commit(types.BOOTSTRAP_CURRENT_USER, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const getUserPermission = ({ commit, dispatch, state }, items) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/v1/get-user-permission`, items)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const saveMobileSettings = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/mobile-settings', data)
      .then((response) => {
        // commit(types.UPDATE_USER_AVATAR, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchUserSettings = ({ commit, dispatch, state }, settings) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get('/api/v1/me/settings', {
        params: {
          settings,
        },
      })
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

/* export const updateUserSettings = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put('/api/v1/me/settings', data)
      .then((response) => {
        commit(types.SET_DEFAULT_LANGUAGE, data.settings.language)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchCompanyLogo = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/company-logo`, params)
      .then((response) => {
        commit(types.SET_COMPANY_LOGO, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
} */
