import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_ITEM_GROUPS](state, itemGroups) {
    state.itemGroups = itemGroups
  },

  [types.SET_TOTAL_ITEM_GROUPS](state, totalItemGroups) {
    state.totalItemGroups = totalItemGroups
  },

  [types.ADD_ITEM_GROUP](state, data) {
    state.itemGroups.push(data.itemGroup)
  },

  [types.UPDATE_ITEM_GROUP](state, data) {
    let pos = state.itemGroups.findIndex(
        (itemGroup) => itemGroup.id === data.itemGroup.id
    )
    state.itemGroups[pos] = data.itemGroup
  },

  [types.DELETE_ITEM_GROUP](state, id) {
    let index = state.itemGroups.findIndex((itemGroup) => itemGroup.id === id)
    state.itemGroups.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_ITEM_GROUPS](state, selectedItemGroups) {
    selectedItemGroups.forEach((itemGroup) => {
      let index = state.itemGroups.findIndex((_itGr) => _itGr.id === itemGroup.id)
      state.itemGroups.splice(index, 1)
    })
    state.selectedItemGroups = []
  },

  [types.SET_SELECTED_ITEM_GROUPS](state, data) {
    state.selectedItemGroups = data
  },

  [types.RESET_SELECTED_ITEM_GROUP](state, data) {
    state.selectedItemGroups = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_SELECTED_VIEW_ITEM_GROUP](state, selectedViewItemGroup) {
    state.selectedViewItemGroup = selectedViewItemGroup
  },
}
