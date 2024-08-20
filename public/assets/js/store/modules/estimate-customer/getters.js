export const estimatesCustomer = (state) => state.estimatesCustomer
export const selectAllField = (state) => state.selectAllField
export const getTemplateId = (state) => state.estimateCustomerTemplateId
export const selectedCustomerEstimates = (state) => state.selectedCustomerEstimates
export const totalCustomerEstimates = (state) => state.totalCustomerEstimates
export const selectedCustomer = (state) => state.selectedCustomerEst
export const selectedNote = (state) => state.selectedNote
export const getEstimate = (state) => (id) => {
  let invId = parseInt(id)
  return state.estimatesCustomer.find((estimate) => estimate.id === invId)
}
