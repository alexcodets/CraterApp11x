import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  items: [],
  itemsPos: [],
  totalItems: 0,
  selectAllField: false,
  selectedItems: [],
  itemUnits: [],
  itemCategories: [],
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations
}
