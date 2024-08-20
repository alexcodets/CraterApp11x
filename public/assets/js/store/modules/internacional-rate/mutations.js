import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_INTERNACIONALS](state, internacionals) {
        state.rate = internacionals
    },

    [types.SET_TOTAL_INTERNACIONALS](state, totalInternacionals) {
        state.totalInternacionals = totalInternacionals
    },

    [types.ADD_INTERNACIONAL](state, data) {
        /* console.log("ADD_INTERNACIONAL",state,data); */
        state.rate.push(data.internacional)
            /* console.log("Guardado ADD_INTERNACIONAL",state,data); */
    },

    [types.UPDATE_INTERNACIONAL](state, data) {
        /* console.log(state.internacionals,data); */
        let pos = state.rate.findIndex(
                (internacional) => internacional.id === data.internacional.id
            )
            /*  console.log("Esto es pos",pos); */
        state.rate[pos] = data.internacional
    },

    [types.DELETE_INTERNACIONAL](state, id) {

        let index = state.rate.findIndex((internacional) => internacional.id === id)
            /* console.log("DELETE_INTERNACIONAL",state.rate,id,index); */
        state.rate.splice(index, 1)
    },

    [types.SET_SELECTED_INTERNACIONALS](state, data) {
        state.selectedInternacionals = data
    },

    [types.RESET_SELECTED_INTERNACIONAL](state, data) {
        state.selectedInternacionals = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.SET_SELECTED_VIEW_INTERNACIONAL](state, selectedViewTaxGroup) {
        state.selectedViewTaxGroup = selectedViewTaxGroup
    },

}