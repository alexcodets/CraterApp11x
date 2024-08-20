import * as types from './mutation-types'

export default {
    [types.SET_SELECTED_PBX_SERVICE](state, selectedPbxService) {
        state.selectedPbxService = selectedPbxService
    },
}