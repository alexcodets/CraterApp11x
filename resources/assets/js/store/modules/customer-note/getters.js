export const CustomerNote = (state) => state.CustomerNote
export const selectAllField = (state) => state.selectAllField
export const selectedCustomerNote = (state) => state.selectedCustomerNote
export const totalCustomerNote = (state) => state.totalCustomerNote
export const getCustomerNote = (state) => (id) => {
  let CstId = parseInt(id)
  return state.CustomerNote.find((CustomerNote) => CustomerNote.id === CstId)
}
export const selectedViewCustomerNote = (state) => state.selectedViewCustomerNote