import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_MODULES](state, modules) {
    state.modules = modules
  },
  [types.ADD_MODULE](state, data) {
    state.modules.push(data)
  },
  [types.UPDATE_MODULE](state, data) {
    let pos = state.modules.findIndex(
        (module) => module.id === data.pbxServers.id
    )
    state.modules[pos] = data.module
  },
  [types.DELETE_MODULE](state, id) {
    let index = state.modules.findIndex((note) => module.id === id)
    state.modules.splice(index, 1)
  },
}
