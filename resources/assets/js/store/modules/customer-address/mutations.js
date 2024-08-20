import * as types from './mutation-types'

export default {
  [types.CUSTOMER_ADDRESS](state, customerAddress) {
    state.customerAddress = customerAddress
  },

  [types.SET_TOTAL_CUSTOMER_ADDRESS](state, totalCustomerAddress) {
    state.totalCustomerAddress = totalCustomerAddress
  },

  /* [types.ADD_CUSTOMERTICKET](state, data) {
    state.customerTickets.push(data.customerTicket)
  },
*/
  [types.UPDATE_CUSTOMER_ADDRESS](state, data) {
    let pos = state.customerAddress.findIndex(
      (customerAddress) => customerAddress.id === data.customerAddress.id
    )

    state.customerAddress[pos] = data.customerAddress 
  },
/*
  [types.DELETE_CUSTOMERTICKET](state, id) {
    let index = state.customerTickets.findIndex((customerTicket) => customerTicket.id === id)
    state.customerTickets.splice(index, 1)
  },

  [types.SET_SELECTED_CUSTOMER_ADDRESS](state, data) {
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
  }, */
  
}