import * as types from './mutation-types'

export default {
    [types.SET_SELECTED_VIEW_CUSTOMER](state, loggedInCustomer) {
        state.loggedInCustomer = loggedInCustomer
    },

    [types.SERVICES_LIST](state, packagesList) {
        state.servicesList = packagesList
    },
}