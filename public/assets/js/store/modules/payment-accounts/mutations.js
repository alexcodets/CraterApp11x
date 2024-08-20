import * as types from './mutation-types'

export default {
  [types.SET_PAYMENT_ACCOUNTS](state, paymentAccounts) {
    state.paymentAccounts = paymentAccounts
  },

  [types.SET_TOTAL_PAYMENT_ACCOUNTS](state, totalPaymentAccounts) {
    state.totalPaymentAccounts = totalPaymentAccounts
  },

  [types.ADD_PAYMENT_ACCOUNT](state, data) {
    state.paymentAccounts.push(data)
  },

  [types.DELETE_PAYMENT_ACCOUNT](state, id) {
    let index = state.paymentAccounts.findIndex((payment) => payment.id === id)
    state.paymentAccounts.splice(index, 1)
  },

  [types.SET_SELECTED_PAYMENT_ACCOUNTS](state, data) {
    state.selectedPaymentAccounts = data
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },
}
