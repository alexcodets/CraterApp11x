import * as types from './mutation-types'

export default {
  [types.SET_PACKAGES](state, packages) {
    state.packages = packages
  },

  [types.SET_PACKAGES_GROUP](state, data) {
    state.groups = data
  },

  [types.SET_TOTAL_PACKAGES](state, totalPackages) {
    state.totalPackages = totalPackages
  },

  [types.ADD_PACKAGE](state, data) {
    state.packages.push(data)
  },

  [types.DELETE_PACKAGE](state, data) {
    let index = state.packages.findIndex((item) => item.id === data.id)
    state.packages.splice(index, 1)
  },

  [types.SET_SELECTED_PACKAGES](state, data) {
    state.selectedPackages = data
  },

  [types.UPDATE_PACKAGE](state, data) {
    let pos = state.packages.findIndex(
      (item) => item.id === data.item.id
    )

    state.packages[pos] = data.package
  },

  [types.UPDATE_PACKAGE_STATUS](state, data) {
    let pos = state.packages.findIndex((item) => item.id === data.id)

    if (state.packages[pos]) {
      state.packages[pos].status = data.status
    }
  },

  [types.RESET_SELECTED_PACKAGES](state, data) {
    state.selectedPackages = []
    state.selectAllField = false
  },

  [types.DELETE_MULTIPLE_PACKAGES](state, selectedPackages) {
    selectedPackages.forEach((item) => {
      let index = state.packages.findIndex((_inv) => _inv.id === item.id)
      state.packages.splice(index, 1)
    })
    state.selectedPackages = []
  },

  [types.SET_TEMPLATE_ID](state, templateId) {
    state.packageTemplateId = templateId
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

  [types.CREATE_PACKAGE_GROUP](state, name) {
    state.packageGroupName = name
  },
}
