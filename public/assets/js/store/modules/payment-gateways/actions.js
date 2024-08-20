import * as types from './mutation-types'

export const fetchPaymentGateways = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-gateways`)
      .then((response) => {
        commit(types.BOOTSTRAP_PAYMENT_GATEWAYS, response)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
  
export const updatePaymentGatewaysStatus = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-gateways/change-status/${params}`)
      .then((response) => {
        commit(types.UPDATE_STATUS_PAYMENT_GATEWAY, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
  
export const updatePaymentGatewaysDefault = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/payment-gateways/change-default', data)
      .then((response) => {
        // commit(types.UPDATE_DEFAULT_PAYMENT_GATEWAY, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}