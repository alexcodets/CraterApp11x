import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_INTERNACIONALS](state, internacionals) {
        state.rate = internacionals
    },

    [types.SET_TOTAL_INTERNACIONALS](state, totalInternacionals) {
        state.totalInternacionals = totalInternacionals
    },  

    [types.ADD_INTERNACIONAL](state, data) {       
        state.rate.push(data.internacional)       
    },

    [types.UPDATE_INTERNACIONAL](state, data) {       
        let pos = state.rate.findIndex(
                (internacional) => internacional.id === data.internacional.id
            )          
        state.rate[pos] = data.internacional
    },

    [types.DELETE_INTERNACIONAL](state, id) {
        let index = state.rate.findIndex((internacional) => internacional.id === id)         
        state.rate.splice(index, 1)
    },

    [types.SET_INTERNATIONALS](state, internationals) {             
        state.internationals = internationals
      },

    [types.SET_SELECTED_INTERNACIONALS](state, data) {    
        state.selectedInternacionals = data
    },   

    [types.RESET_SELECTED_INTERNACIONAL](state, data) {
        state.selectedInternacionals = null
        state.selectAllField = false
    },   

    [types.SET_SELECT_ALL_STATE](state, data) {        
        state.selectAllField = data
    },   

    [types.SET_SELECTED_VIEW_INTERNACIONAL](state, selectedViewTaxGroup) {
        state.selectedViewTaxGroup = selectedViewTaxGroup
    },

}