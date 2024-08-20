export const customerTickets = (state) => state.customerTickets
export const selectAllField = (state) => state.selectAllField
export const selectedCustomerTickets = (state) => state.selectedCustomerTickets
export const totalCustomerTickets = (state) => state.totalCustomerTickets
export const getCustomerTicket = (state) => (id) => {
  let CstId = parseInt(id)
  return state.customerTickets.find((customerTicket) => customerTicket.id === CstId)
}
export const selectedViewcustomerTicket = (state) => state.selectedViewcustomerTicket