import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_CUSTOMERTICKETS](state, customerTickets) {
    state.customerTickets = customerTickets
  },

  [types.SET_TOTAL_CUSTOMERTICKETS](state, totalCustomerTickets) {
    state.totalCustomerTickets = totalCustomerTickets
  },

  [types.ADD_CUSTOMERTICKET](state, data) {
    state.customerTickets.push(data.customerTicket)
  },

  [types.UPDATE_CUSTOMERTICKET](state, data) {
    let pos = state.customerTickets.findIndex(
      (customerTicket) => customerTicket.id === data.customerTicket.id
    )

    state.customerTickets[pos] = data.customerTicket 
  },

  [types.DELETE_CUSTOMERTICKET](state, id) {
    let index = state.customerTickets.findIndex((customerTicket) => customerTicket.id === id)
    state.customerTickets.splice(index, 1)
  },

  [types.SET_SELECTED_CUSTOMERTICKETS](state, data) {
    state.selectedCustomerTickets = data
  },

  [types.RESET_SELECTED_CUSTOMERTICKET](state, data) {
    state.selectedCustomerTickets = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_SELECTED_VIEW_CUSTOMERTICKET](state, selectedViewTaxGroup) {
    state.selectedViewTaxGroup = selectedViewTaxGroup
  },
  
}