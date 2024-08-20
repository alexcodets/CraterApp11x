import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  payments: [],
  totalPayments: 0,
  selectAllField: false,
  selectedPayments: [],
  paymentModes: [],
  //
  paymentModesWithSettings: [],
  existAuthorizeSetting: 0,
  existPaypalSetting: 0,
  existAuxVaultSetting: 0,
  //
  selectedNote: null,
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}
