import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  paymentsCustomers: [],
  totalPaymentsCustomers: 0,
  selectAllField: false,
  selectedPaymentsCustomers: [],
  paymentModes: [],
  selectedNote: null,
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}
