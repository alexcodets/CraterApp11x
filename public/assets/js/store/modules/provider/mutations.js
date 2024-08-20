import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_PROVIDERS](state, providers) {
    state.providers = providers
  },

  [types.SET_TOTAL_PROVIDERS](state, totalProviders) {
    state.totalProviders = totalProviders
  },

  [types.ADD_PROVIDER](state, data) {
    state.providers.push(data.provider)
  },

  [types.UPDATE_PROVIDER](state, data) {
    let pos = state.providers.findIndex(
      (provider) => provider.id === data.provider.id
    )

    state.providers[pos] = data.provider 
  },

  [types.DELETE_PROVIDER](state, id) {
    let index = state.providers.findIndex((provider) => provider.id === id)
    state.providers.splice(index, 1)
  },

  [types.SET_SELECTED_PROVIDERS](state, data) {
    state.selectedProviders = data
  },

  [types.RESET_SELECTED_PROVIDER](state, data) {
    state.selectedProviders = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_SELECTED_VIEW_PROVIDER](state, selectedViewTaxGroup) {
    state.selectedViewTaxGroup = selectedViewTaxGroup
  },
  
}