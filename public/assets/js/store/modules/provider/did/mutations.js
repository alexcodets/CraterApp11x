import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_DID](state, did) {
    state.did = did
  },

  [types.SET_TOTAL_DID](state, totalDID) {
    state.totalDID = totalDID
  },

  [types.ADD_DID](state, data) {
    state.did.push(data.did)
  },

  [types.UPDATE_DID](state, data) {
    let pos = state.did.findIndex(
      (did) => did.id === data.did.id
    )
    state.did[pos] = data.did
  },

  [types.DELETE_DID](state, id) {
    let index = state.did.findIndex((did) => did.id === id)
    state.did.splice(index, 1)
  },

  [types.SET_SELECTED_DID](state, data) {
    state.selectedDID = data
  },

  [types.RESET_SELECTED_DID](state, data) {
    state.selectedDID = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },
}
