import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  paymentMultiple: []
}

export default {
  namespaced: true,
  state: initialState,
  getters: getters,
  actions: actions,
  mutations: mutations,
}