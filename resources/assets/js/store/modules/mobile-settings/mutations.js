import * as types from './mutation-types'

export default {

  [types.BOOTSTRAP_CUSTOMERS_MESSAGING](state, customers) {
    state.customersMessaging = customers
  },

  [types.SET_TOTAL_CUSTOMERS_MESSAGING](state, totalCustomers) {
      state.totalCustomersMessaging = totalCustomers
  },
  
  [types.SET_SELECTED_CUSTOMERS_MESSAGING](state, data) {
    state.selectedCustomersMessaging = data
  },

  [types.SET_SELECT_ALL_STATE_MESSAGING](state, data) {
    state.selectAllField = data
  },

  [types.RESET_CURRENT_USER](state, user) {
    state.currentUser = null
  },

  [types.BOOTSTRAP_CURRENT_USER](state, user) {
    state.currentUser = user
  },

  [types.UPDATE_CURRENT_USER](state, user) {
    state.currentUser = user
  },

  [types.UPDATE_USER_AVATAR](state, data) {
    if (state.currentUser) {
      state.currentUser.avatar = data.avatar
    }
  },

  [types.SET_DEFAULT_LANGUAGE](state, data) {
    window.i18n.locale = data
  },

  [types.SET_COMPANY_LOGO](state, user) {
    state.companyLogo = user
  },
}
