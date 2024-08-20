import * as types from './mutation-types'

export default {
  [types.SET_TAX_AGENCIES](state, taxAgency) {
    state.taxAgency = taxAgency
  },

  [types.ADD_TAX_AGENCY](state, data) {
    state.taxAgency.push(data.taxAgency)
  },

  [types.UPDATE_TAX_AGENCY](state, data) {
    let pos = state.taxAgency.findIndex(
      (taxAgency) => taxAgency.id === data.taxAgency.id
    )
    Object.assign(state.taxAgency[pos], { ...data.taxCategory })
  },

  [types.RESET_SELECTED_TAX_AGENCIES](state, data) {
    state.selectedTaxAgencies = []
    state.selectAllField = false
  },

  [types.DELETE_TAX_AGENCY](state, id) {
    let pos = state.taxAgency.findIndex((taxAgency) => taxAgency.id === id)
    state.taxAgency.splice(pos, 1)
  },
}
