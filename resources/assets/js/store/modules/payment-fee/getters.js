export const paymentFees = (state) => state.paymentFees
export const selectAllField = (state) => state.selectAllField
export const selectedPaymentFees = (state) => state.selectedPaymentFees
export const totalPaymentFees = (state) => state.totalPaymentFees
export const getPaymentFees = (state) => (id) => {
    let CstId = parseInt(id)
    return state.payment_fee.find((group) => payment_fee.id === CstId)
}
export const selectedViewPaymentFee = (state) => state.selectedViewPaymentFee