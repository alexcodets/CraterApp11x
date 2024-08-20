import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_DID_TOLL_FREE](state, tollfree) {
    state.tollfree = tollfree
  },

  [types.SET_TOTAL_DID_TOLL_FREE](state, totalDIDTOLLFREE) {
    state.totalDIDTOLLFREE = totalDIDTOLLFREE
  },

  [types.ADD_DID_TOLL_FREE](state, data) {
    state.tollfree.push(data.ProfileDidTollFree);
  },

  [types.UPDATE_DID_TOLL_FREE](state, data) {
    let pos = state.tollfree.findIndex(
      (tollfree) => tollfree.id === data.tollfree.id
    )
    state.didtollfree[pos] = data.didtollfree
  },

  [types.DELETE_DID_TOLL_FREE](state, id) {
   let index = state.tollfree.findIndex((tollfree) => tollfree.id === id)
    state.tollfree.splice(index, 1)
  },

  [types.SET_SELECTED_DID_TOLL_FREE](state, data) {
    state.selectedDIDTOLLFREE = data
  },

  [types.RESET_SELECTED_DID_TOLL_FREE](state, data) {
    state.selectedDIDTOLLFREE = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },
}
