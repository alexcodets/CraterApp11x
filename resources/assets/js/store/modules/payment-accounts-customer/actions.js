import * as types from './mutation-types'

export const fetchPaymentAccounts = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-accounts-customer`, { params })
      .then((response) => {
        commit(types.SET_PAYMENT_ACCOUNTS_CUSTOMER, response.data.payment_accounts.data)
        commit(types.SET_TOTAL_PAYMENT_ACCOUNTS_CUSTOMER, response.data.paymentAccountTotalCount)
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
      .get(`/api/v1/payment-accounts-customer/${id}`)
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
      .post('/api/v1/payment-accounts-customer', data)
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
      .put(`/api/v1/payment-accounts-customer/${data.id}`, data)
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
      .post(`/api/v1/payment-accounts-customer/delete`, id)
      .then((response) => {
        commit(types.DELETE_PAYMENT_ACCOUNT_CUSTOMER, id)
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
      .post(`/api/v1/payment-accounts-customer/${id}/default-pay-account`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
 