export const paypals = (state) => state.paypals
export const selectAllField = (state) => state.selectAllField
export const selectedPaypals = (state) => state.selectedPaypals
export const totalPaypals = (state) => state.totalPaypals
export const getPaypal = (state) => (id) => {
  let CstId = parseInt(id)
  return state.paypals.find((paypal) => paypal.id === CstId)
}
export const selectedViewPaypal = (state) => state.selectedViewPaypal