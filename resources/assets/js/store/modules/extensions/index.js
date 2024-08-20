import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  extensions: [],
  itemGroups: [],
  totalExtensions: 0,
  selectAllField: false,
  selectedItemGroups: [],
  selectedExtensions: [],
  selectedViewItemGroup: {},
}

export default {
  namespaced: true,
  state: initialState,
  getters: getters,
  actions: actions,
  mutations: mutations,
}