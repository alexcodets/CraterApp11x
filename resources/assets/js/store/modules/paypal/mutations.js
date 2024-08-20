import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_PAYPAL](state, paypals) {
    state.paypals = paypals
  },

  [types.SET_TOTAL_PAYPAL](state, totalPaypals) {
    state.totalPaypals = totalPaypals
  },

  [types.ADD_PAYPALS](state, data) {
    state.paypals.push(data.paypal)
  },

  [types.UPDATE_PAYPAL](state, data) {
    let pos = state.paypals.findIndex(
      (paypal) => paypal.id === data.paypal.id
    )

    state.paypal[pos] = data.paypal 
  },

  [types.DELETE_PAYPAL](state, id) {
    let index = state.paypals.findIndex((paypal) => paypal.id === id)
    state.paypals.splice(index, 1)
  },

  /*[types.SET_SELECTED_PAYPALS](state, data) {
    state.selectedPaypal = data
  },

  [types.RESET_SELECTED_PAYPAL](state, data) {
    state.selectedPaypal = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.ADD_PAYPAL](state, data) {
    state.paypals.push(data.authorization)
  },

  [types.SAVE_PAYPAL](state, data) {
    state.paypals.push(data.paypal)
  },

  [types.VOID_PAYPAL](state, data) {
    state.paypals.push(data.paypal)
  },

  [types.REFUNDED_PAYPAL](state, data) {
    state.paypals.push(data.paypal)
  },*/

  [types.UPDATE_STATUS_PAYPAL](state, data) {
    state.paypals.push(data.paypal)
  },

  /*[types.ADD_PAYPAL_ACH](state, data) {
    state.paypals.push(data.paypal)
  },

  [types.SAVE_PAYPAL_ACH](state, data) {
    state.paypals.push(data.paypal)
  },*/
  
}