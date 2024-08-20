import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_PAYMENT_FEES](state, paymentFees) {
        state.paymentFees = paymentFees
    },

    [types.SET_TOTAL_PAYMENT_FEES](state, totalPaymentFees) {
        state.totalPaymentFees = totalPaymentFees
    },

    [types.ADD_PAYMENT_FEE](state, data) {
        //state.paymentFees.push(data.paymentFee)
    },

    [types.UPDATE_PAYMENT_FEE](state, data) {

    },

    [types.DELETE_PAYMENT_FEE](state, id) {

    },

    [types.DELETE_MULTIPLE_PAYMENT_FEES](state, selectedPaymentFees) {
        selectedPaymentFees.forEach((paymentFee) => {
            let index = state.paymentFees.findIndex((_cust) => _cust.id === paymentFee.id)
            state.paymentFees.splice(index, 1)
        })

        state.selectedPaymentFees = []
    },

    [types.SET_SELECTED_PAYMENT_FEES](state, data) {
        state.selectedPaymentFees = data
    },

    [types.RESET_SELECTED_PAYMENT_FEE](state, data) {
        state.selectedPaymentFees = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.SET_SELECTED_VIEW_PAYMENT_FEE](state, selectedViewPaymentFee) {
        state.selectedViewPaymentFee = selectedViewPaymentFee
    },


}