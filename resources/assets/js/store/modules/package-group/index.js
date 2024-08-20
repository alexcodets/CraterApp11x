import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  groups: [],
  totalGroups: 0,
  selectAllField: false,
  selectedGroups: [],
  selectedViewCustomer: {},
  package: []
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}