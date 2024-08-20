import * as types from './mutation-types'

export default {
  [types.SET_PAYMENTS](state, payments) {
    state.paymentsCustomers = payments
  },

  [types.SET_TOTAL_PAYMENTS](state, totalPayments) {
    state.totalPaymentsCustomers = totalPayments
  },

  [types.ADD_PAYMENT](state, data) {
    state.paymentsCustomers.push(data)
  },

  [types.DELETE_PAYMENT](state, id) {
    let index = state.paymentsCustomers.findIndex((payment) => payment.id === id)
    state.paymentsCustomers.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_PAYMENTS](state, selectedPayments) {
    selectedPaymentsCustomers.forEach((payment) => {
      let index = state.paymentsCustomers.findIndex((_inv) => _inv.id === payment.id)
      state.paymentsCustomers.splice(index, 1)
    })

    state.selectedPaymentsCustomers = []
  },

  [types.SET_SELECTED_PAYMENTS](state, data) {
    state.selectedPaymentsCustomers = data
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_PAYMENT_MODES](state, data) {
    state.paymentModes = data
  },

  [types.ADD_PAYMENT_MODE](state, data) {
    state.paymentModes = [data.paymentMethod, ...state.paymentModes]
  },

  [types.DELETE_PAYMENT_MODE](state, id) {
    let index = state.paymentModes.findIndex(
      (paymentMethod) => paymentMethod.id === id
    )
    state.paymentModes.splice(index, 1)
  },

  [types.UPDATE_PAYMENT_MODE](state, data) {
    let pos = state.paymentModes.findIndex(
      (paymentMethod) => paymentMethod.id === data.paymentMethod.id
    )
    state.paymentModes[pos] = data.paymentMethod
  },

  [types.RESET_SELECTED_NOTE](state, data) {
    state.selectedNote = null
  },

  [types.SET_SELECTED_NOTE](state, data) {
    state.selectedNote = data
  },
}
