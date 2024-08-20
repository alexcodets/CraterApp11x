import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_EXTENSIONS](state, extensions) {
    state.extensions = extensions
  },

  [types.ADD_EXTENSIONS](state, data) {
    state.extensions.push(data.extensions)
  },

  [types.UPDATE_EXTENSIONS](state, data) {
    let pos = state.extensions.findIndex(
        (extension) => extension.id === data.extension.id
    )
    state.extensions[pos] = data.extension
  },

  [types.DELETE_EXTENSIONS](state, id) {
    let index = state.extensions.findIndex((extension) => extension.id === id)
    state.extensions.splice(index, 1)
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


  [types.SET_SELECTED_EXTENSIONS](state, data) {
    state.selectedExtensions = data
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

  [types.SET_ITEM_UNITS](state, itemUnits) {
    state.itemUnits = itemUnits
  },
  
  [types.RESET_EXTENSIONS](state, data) {
    state.selectedExtension = null
  },

  [types.SET_EXTENSIONS](state, data) {
    state.selectedExtension = data
  },
  [types.SET_TOTAL_EXTENSIONS](state, totalExtensions) {
    state.totalExtensions = totalExtensions
  },
}
