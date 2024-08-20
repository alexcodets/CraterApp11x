import * as types from './mutation-types'

export default {
  [types.SET_ESTIMATES](state, data) {
    state.estimatesCustomer = data
  },

  [types.SET_TOTAL_ESTIMATES](state, totalCustomerEstimates) {
    state.totalCustomerEstimates = totalCustomerEstimates
  },

  [types.ADD_ESTIMATE](state, data) {
    state.estimatesCustomer = [...state.estimatesCustomer, data]
  },

  [types.DELETE_ESTIMATE](state, id) {
    let index = state.estimatesCustomer.findIndex((estimate) => estimate.id === id)
    state.estimatesCustomer.splice(index, 1)
  },

  [types.SET_SELECTED_ESTIMATES](state, data) {
    state.selectedCustomerEstimates = data
  },

  [types.DELETE_MULTIPLE_ESTIMATES](state, selectedEstimates) {
    selectedEstimates.forEach((estimate) => {
      let index = state.estimatesCustomer.findIndex((_est) => _est.id === estimate.id)
      state.estimatesCustomer.splice(index, 1)
    })

    state.selectedCustomerEstimates = []
  },

  [types.UPDATE_ESTIMATE](state, data) {
    let pos = state.estimatesCustomer.findIndex(
      (estimate) => estimate.id === data.estimate.id
    )

    state.estimatesCustomer[pos] = data.estimate
  },

  [types.UPDATE_ESTIMATE_STATUS](state, data) {
    let pos = state.estimatesCustomer.findIndex((estimate) => estimate.id === data.id)
    if (state.estimatesCustomer[pos]) {
      // state.estimates[pos] = { ...state.estimates[pos], status: data.status }
      state.estimatesCustomer[pos].status = data.status
    }
  },

  [types.RESET_SELECTED_ESTIMATES](state, data) {
    state.selectedCustomerEstimates = []
    state.selectAllField = false
  },

  [types.SET_TEMPLATE_ID](state, templateId) {
    state.estimateCustomerTemplateId = templateId
  },

  [types.SELECT_CUSTOMER](state, data) {
    state.selectedCustomerEst = data
  },

  [types.RESET_SELECTED_CUSTOMER](state, data) {
    state.selectedCustomerEst = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.VIEW_ESTIMATE](state, estimate) {
    state.selectedEstimate = estimate
  },

  [types.SET_SELECTED_NOTE](state, data) {
    state.selectedNote = data
  },

  [types.RESET_SELECTED_NOTE](state, data) {
    state.selectedNote = null
  },
}
