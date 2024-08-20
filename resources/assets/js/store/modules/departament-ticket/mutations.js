import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_DEPARTAMENTS](state, departaments) {
    state.departaments = departaments
  },

  [types.SET_TOTAL_DEPARTAMENTS](state, totaldepartaments) {
    state.totaldepartaments = totaldepartaments
  },

  [types.ADD_DEPARTAMENT](state, data) {
    state.departaments.push(data.departament)
  },

  [types.UPDATE_DEPARTAMENT](state, data) {
    let pos = state.departaments.findIndex(
      (departament) => departament.id === data.departaments.id
    )

    state.departaments[pos] = data.departament 
  },

  [types.DELETE_DEPARTAMENT](state, id) {
    let index = state.departaments.findIndex((departament) => departament.id === id)
    state.departaments.splice(index, 1)
  },

  [types.SET_SELECTED_DEPARTAMENTS](state, data) {
    state.selectedDepartaments = data
  },

  [types.RESET_SELECTED_DEPARTAMENT](state, data) {
    state.selectedDepartaments = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_SELECTED_VIEW_DEPARTAMENT](state, selectedViewDepartament) {
    state.selectedViewDepartament = selectedViewDepartament
  },
  
}