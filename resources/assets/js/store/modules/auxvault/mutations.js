import * as types from './mutation-types'

export default {

    [types.BOOTSTRAP_AUTHORIZATIONS](state, authorizations) {
        state.authorizations = authorizations
    },

    [types.SET_TOTAL_AUTHORIZATIONS](state, totalAuthorizations) {
        state.totalAuthorizations = totalAuthorizations
    },

    [types.ADD_AUTHORIZATION](state, data) {

        // state.authorizations.push(data.authorization)
    },

    [types.UPDATE_AUTHORIZATION](state, data) {
        let pos = state.authorizations.findIndex(
            (authorization) => authorization.id === data.authorization.id
        )

        state.authorizations[pos] = data.authorization
    },

    [types.DELETE_AUTHORIZATION](state, id) {

    },

    [types.SET_SELECTED_AUTHORIZATIONS](state, data) {
        //  state.selectedAuthorization = data
    },

    [types.RESET_SELECTED_AUTHORIZATION](state, data) {
        state.selectedAuthorization = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.ADD_AUTHORIZE](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.SAVE_AUTHORIZE](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.VOID_AUTHORIZE](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.REFUNDED_AUTHORIZE](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.UPDATE_STATUS_AUTHORIZE](state, data) {
        // state.authorizations.push(data.authorization)
    },

    [types.ADD_AUTHORIZE_ACH](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.SAVE_AUTHORIZE_ACH](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.ADD_AUTHORIZE_PAYPAL](state, data) {
        state.authorizations.push(data.authorization)
    },

    [types.SAVE_PAYPAL](state, data) {
        state.authorizations.push(data.authorization)
    },


}