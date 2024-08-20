import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  avalara: {
    configs: []
  },
  avalaraConfigsTemplateId: 1,
  selectedAvalaraConfigs: [],
  selectAllField: false,
  totalAvalaraConfigs: 0,
  selectedCustomer: null,
  selectedNote: null,
  selectedItem: null,
  avalaraConfigGroupName: null,
  group: [],
  AvailableGroups: [],
  avalaraItems: [],
  avalaraLocationToSave: []
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}
