import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_RETENTIONS](state, retentions) {
    state.retentions = retentions
  },

  [types.SET_TOTAL_RETENTIONS](state, totalRetentions) {
    state.totalRetentions = totalRetentions
  },
/*
  [types.ADD_TAX_GROUP](state, data) {
    state.taxGroups.push(data.taxGroup)
  },

  [types.UPDATE_TAX_GROUP](state, data) {
    let pos = state.taxGroups.findIndex(
      (taxGroup) => taxGroup.id === data.taxGroup.id
    )

    state.taxGroups[pos] = data.taxGroup
  },

  [types.DELETE_TAX_GROUP](state, id) {
    let index = state.taxGroups.findIndex((taxGroup) => taxGroup.id === id)
    state.taxGroups.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_TAX_GROUPS](state, selectedTaxGroups) {
    selectedTaxGroups.forEach((taxGroups) => {
      let index = state.taxGroups.findIndex((_cust) => _cust.id === taxGroup.id)
      state.taxGroups.splice(index, 1)
    })

    state.selectedTaxGroups = []
  },

  [types.SET_SELECTED_TAX_GROUPS](state, data) {
    state.selectedTaxGroups = data
  },

  [types.RESET_SELECTED_TAX_GROUP](state, data) {
    state.selectedTaxGroups = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_SELECTED_VIEW_TAX_GROUP](state, selectedViewTaxGroup) {
    state.selectedViewTaxGroup = selectedViewTaxGroup
  },

  [types.SET_GROUP_TAXES](state, data) {
    state.taxes = data
  }, */
  
}