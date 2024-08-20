import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  taxGroups: [],
  totalTaxGroups: 0,
  selectAllField: false,
  selectedTaxGroups: [],
  selectedViewCustomer: {},
  taxes: []
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}