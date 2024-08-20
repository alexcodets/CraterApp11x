import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  internationals: [],
  ids: [],
  rate: [],
  totalInternacionals: 0,
  selectAllField: false,
  selectedInternacionals: [],
  selectedViewCustomer: {}
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}