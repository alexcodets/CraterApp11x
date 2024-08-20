import * as types from './mutation-types'

export default {
  [types.SET_TAX_CATEGORIES](state, taxCategories) {
    state.taxCategories = taxCategories
  },

  [types.ADD_TAX_CATEGORY](state, data) {
    state.taxCategories.push(data.taxCategories)
  },

  [types.UPDATE_TAX_CATEGORY](state, data) {
    let pos = state.taxCategories.findIndex(
      (taxCategory) => taxCategory.id === data.taxCategory.id
    )
    Object.assign(state.taxCategories[pos], { ...data.taxCategory })
  },
/*
  [types.DELETE_TAX_TYPE](state, id) {
    let pos = state.taxTypes.findIndex((taxType) => taxType.id === id)
    state.taxTypes.splice(pos, 1)
  }, */
}
