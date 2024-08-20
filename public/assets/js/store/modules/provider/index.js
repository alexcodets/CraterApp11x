import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  providers: [],
  totalProviders: 0,
  selectedViewCustomer: {}
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}