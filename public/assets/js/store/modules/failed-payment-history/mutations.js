import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_FAILED_PAYMENT_HISTORY](state, failed_payment_history) {
    state.failed_payment_history = failed_payment_history
  },

  [types.SET_TOTAL_FAILED_PAYMENT_HISTORY](state, totalFailedPaymentHistory) {
    state.totalFailedPaymentHistory = totalFailedPaymentHistory
  },

  [types.ADD_FAILED_PAYMENT_HISTORY](state, data) {
    state.failed_payment_history.push(data.failed_payment_histor)
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },
  
}