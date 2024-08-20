//import * as types from './mutation-types'

/*
export const fetchItemGroups = ({commit, dispatch, state}, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/item-groups`, {params})
      .then((response) => {
        commit(types.BOOTSTRAP_ITEM_GROUPS, response.data.itemGroups.data)
        commit(types.SET_TOTAL_ITEM_GROUPS, response.data.itemGroupsTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
*/

export const addMultiplePayment = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/payments/multiple/create', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchMultiplePayment = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
      window.axios
          .get(`/api/v1/payments/multiple/show/${id}`)
          .then((response) => {
              resolve(response)
          })
          .catch((err) => {
              reject(err)
          })
  })
}

export const getPaymentMethods = ({ commit, dispatch }, data) => {
  return new Promise((resolve, reject) => {
      window.axios
          .post(`/api/v1/payments/multiple/get-payment-methods`, data)
          .then((response) => {
              resolve(response)
          })
          .catch((err) => {
              reject(err)
          })
  })
}