import * as types from './mutation-types'

export default {

  [types.SET_ROLES](state, data) {
    state.roles.push(data)
  },

  [types.SET_TOTAL_ROLES](state, total) {
    state.totalRoles = total
  },
}
