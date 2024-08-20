export const internacionals = (state) => state.rate
export const selectAllField = (state) => state.selectAllField
export const selectedInternacionals = (state) => state.selectedInternacionals
export const totalInternacionals = (state) => state.totalInternacionals
export const getInternacional = (state) => (id) => {
  let CstId = parseInt(id)
  return state.rate.find((internacional) => internacional.id === CstId)
}
export const selectedViewInternacional = (state) => state.selectedViewInternacional