import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    paymentFees: [],
    totalTaxGroups: 0,
    selectAllField: false,
    selectedPaymentFees: [],
    selectedViewPaymentFee: {},

}

export default {
    namespaced: true,

    state: initialState,

    getters: getters,

    actions: actions,

    mutations: mutations,
}