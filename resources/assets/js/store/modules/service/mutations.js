import * as types from './mutation-types'

export default {
    [types.SET_SELECTED_SERVICE](state, selectedService) {
        state.selectedService = selectedService
    },
}