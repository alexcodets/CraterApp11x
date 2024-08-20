import * as types from './mutation-types'

export default {

  [types.ADD_AVALARA_CONFIG](state, data) {
    state.avalara.configs.push(data)
  },

  [types.SET_AVALARA_CONFIGS](state, data) {
    state.avalara.configs = data
  },

  [types.SET_AVALARA_CONFIGS_GROUP](state, data) {
    state.groups = data
  },

  [types.SET_TOTAL_AVALARA_CONFIGS](state, totalAvalaraConfigs) {
    state.totalAvalaraConfigs = totalAvalaraConfigs
  },

  [types.DELETE_AVALARA_CONFIG](state, data) {
    let index = state.avalara.configs.findIndex((item) => item.id === data.id)
    state.avalara.configs.splice(index, 1)
  },

  [types.SET_SELECTED_AVALARA_CONFIGS](state, data) {
    state.selectedAvalaraConfigs = data
  },

  [types.UPDATE_AVALARA_CONFIG](state, data) {
    let pos = state.avalara.configs.findIndex(
      (item) => item.id === data.item.id
    )

    state.avalara.configs[pos] = data.avalara.configs
  },

  [types.UPDATE_AVALARA_CONFIG_STATUS](state, data) {
    let pos = state.avalara.configs.findIndex((item) => item.id === data.id)

    if (state.avalara.configs[pos]) {
      state.avalara.configs[pos].status = data.status
    }
  },

  [types.RESET_SELECTED_AVALARA_CONFIGS](state, data) {
    state.selectedAvalaraConfigs = []
    state.selectAllField = false
  },

  [types.DELETE_MULTIPLE_AVALARA_CONFIGS](state, selectedAvalaraConfigs) {
    selectedAvalaraConfigs.forEach((item) => {
      let index = state.avalara.configs.findIndex((_inv) => _inv.id === item.id)
      state.avalara.configs.splice(index, 1)
    })
    state.selectedAvalaraConfigs = []
  },

  [types.SET_TEMPLATE_ID](state, templateId) {
    state.avalaraConfigsTemplateId = templateId
  },

  [types.SELECT_CUSTOMER](state, data) {
    state.selectedCustomer = data
  },

  [types.RESET_SELECTED_CUSTOMER](state, data) {
    state.selectedCustomer = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.RESET_SELECTED_NOTE](state, data) {
    state.selectedNote = null
  },

  [types.SET_SELECTED_NOTE](state, data) {
    state.selectedNote = data
  },

  [types.RESET_ITEM](state, data) {
    state.selectedItem = null
  },

  [types.SET_ITEM](state, data) {
    state.selectedItem = data
  },

  [types.CREATE_AVALARA_CONFIG_GROUP](state, name) {
    state.avalaraConfigGroupName = name
  },

  [types.SET_AVALARA_ITEMS](state, data) {
    state.avalaraItems = data
  },
}
