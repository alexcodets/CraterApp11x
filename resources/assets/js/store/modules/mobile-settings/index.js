import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  mobileSettings: [],
  /*COSTUMER*/
  customersMessaging: [],
  totalCustomersMessaging: 0,
  selectAllField: false,
  selectedCustomersMessaging: [],
  selectedViewCustomer: {},
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}
