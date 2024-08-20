import * as types from './mutation-types'

export const fetchPaymentAccounts = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-accounts`, { params })
      .then((response) => {
        commit(types.SET_PAYMENT_ACCOUNTS, response.data.payment_accounts.data)
        commit(types.SET_TOTAL_PAYMENT_ACCOUNTS, response.data.paymentAccountTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchPaymentAccount = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-accounts/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addPaymentAccount = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/payment-accounts', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updatePaymentAccount = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/payment-accounts/${data.id}`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deletePaymentAccount = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/payment-accounts/delete`, id)
      .then((response) => {
        commit(types.DELETE_PAYMENT_ACCOUNT, id)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const defaultPayAccount = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/payment-accounts/${id}/default-pay-account`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
 