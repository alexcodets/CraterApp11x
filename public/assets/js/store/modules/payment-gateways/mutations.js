import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_PAYMENT_GATEWAYS](state, payment_gateways) {
    state.payment_gateways = payment_gateways
  }, 

  [types.UPDATE_STATUS_PAYMENT_GATEWAY](state, payment_gateways) {
    state.payment_gateways = payment_gateways
  },

  [types.UPDATE_DEFAULT_PAYMENT_GATEWAY](state, data) {
    state.paymentGateways.push(data.paymentGateway)
  },
}