import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_CUSTOMER_NOTE](state, CustomerNote) {
        state.CustomerNote = CustomerNote
    },

    [types.SET_TOTAL_CUSTOMER_NOTE](state, totalCustomerNote) {
        state.totalCustomerNote = totalCustomerNote
    },

    [types.ADD_CUSTOMER_NOTE](state, data) {
        state.CustomerNote.push(data.CustomerNote)
    },

    [types.UPDATE_CUSTOMER_NOTE](state, data) {

        let pos = state.CustomerNote.findIndex(
            (CustomerNote) => CustomerNote.id === data.CustomerNote.id
        )

        state.CustomerNote[pos] = data.CustomerNote
    },

    [types.DELETE_CUSTOMER_NOTE](state, id) {
        let index = state.CustomerNote.findIndex((CustomerNote) => CustomerNote.id === id)
        state.CustomerNote.splice(index, 1)
    },

    [types.SET_SELECTED_CUSTOMER_NOTE](state, data) {
        state.selectedCustomerNote = data
    },

    [types.RESET_SELECTED_CUSTOMER_NOTE](state, data) {
        state.selectedCustomerNote = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.SET_SELECTED_VIEW_CUSTOMER_NOTE](state, selectedViewTaxGroup) {
        state.selectedViewTaxGroup = selectedViewTaxGroup
    },

}