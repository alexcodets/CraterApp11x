import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_STRIPE](state, stripes) {
    state.stripes = stripes
  },

  [types.SET_TOTAL_STRIPE](state, totalStripes) {
    state.totalStripes = totalStripes
  },

  [types.ADD_STRIPE](state, data) {
    state.stripes.push(data.stripe)
  },

  [types.UPDATE_STRIPE](state, data) {
    let pos = state.stripes.findIndex(
      (stripe) => stripe.id === data.stripe.id
    )

    state.stripe[pos] = data.stripe 
  },

  [types.DELETE_STRIPE](state, id) {
    let index = state.stripes.findIndex((stripe) => stripe.id === id)
    state.stripes.splice(index, 1)
  },

  [types.UPDATE_STATUS_STRIPE](state, data) {
    state.stripes.push(data.stripe)
  },
  
}