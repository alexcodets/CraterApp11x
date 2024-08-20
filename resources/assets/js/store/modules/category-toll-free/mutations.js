import * as types from './mutation-types'

export default {
  [types.SET_CATEGORIESTOLLFREE](state, categoriesTollFree) {
    state.categoriesTollFree = categoriesTollFree
  },

  [types.ADD_CATEGORYTOLLFREE](state, data) {
    state.categoriesTollFree.push(data.category)
  },

  [types.UPDATE_CATEGORYTOLLFREE](state, data) {
    let pos = state.categoriesTollFree.findIndex(
      (category) => category.id === data.category.id
    )
    Object.assign(state.categoriesTollFree[pos], { ...data.category })
  },

  [types.DELETE_CATEGORYTOLLFREE](state, id) {
    let pos = state.categoriesTollFree.findIndex((category) => category.id === id)
    state.categoriesTollFree.splice(pos, 1)
  },
}
