import * as types from './mutation-types'

export const fetchStripes = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/stripe-settings`, { params })
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchStripe = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/stripe-settings/${id}`)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const getStripeSessionId = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/stripe/request-ids` )
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

// getSettingStripeDefault
export const getSettingStripeDefault = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/stripe-settings/default`)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addStripe = ({ commit, dispatch, state }, payload) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/stripe-settings`, payload)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateStripe = ({ commit, dispatch, state }, payload) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/stripe-settings/${payload.id}`, payload)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteStripe = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
      window.axios
        .delete(`/api/v1/stripe-settings/${id}`)
        .then((response) => {
          resolve(response.data)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }
