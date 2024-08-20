import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_PREFIX_GROUPS](state, prefixGroups) {
        state.prefixGroups = prefixGroups
    },

    [types.SET_TOTAL_PREFIX_GROUPS](state, totalPrefixGroups) {
        state.totalPrefixGroups = totalPrefixGroups
    },

    [types.ADD_PREFIX_GROUP](state, data) {
        state.prefixGroups.push(data.prefixGroup)
    },

    [types.UPDATE_PREFIX_GROUP](state, data) {
        let pos = state.prefixGroups.findIndex(
            (prefixGroup) => prefixGroup.id === data.prefixGroup.id
        )
        state.prefixGroups[pos] = data.prefixGroup
    },

    [types.DELETE_PREFIX_GROUP](state, id) {
        let index = state.prefixGroups.findIndex((prefixGroup) => prefixGroup.id === id)
        state.prefixGroups.splice(index, 1)
    },

    [types.DELETE_MULTIPLE_PREFIX_GROUPS](state, selectedPrefixGroups) {
        selectedPrefixGroups.forEach((prefixGroup) => {
            let index = state.prefixGroups.findIndex((_pfxGr) => _pfxGr.id === prefixGroup.id)
            state.prefixGroups.splice(index, 1)
        })
        state.selectedPrefixGroups = []
    },

    [types.SET_SELECTED_PREFIX_GROUPS](state, data) {
        state.selectedPrefixGroups = data
    },

    [types.RESET_SELECTED_PREFIX_GROUP](state, data) {
        state.selectedPrefixGroups = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.SET_SELECTED_VIEW_PREFIX_GROUP](state, selectedViewPrefixGroup) {
        state.selectedViewPrefixGroup = selectedViewPrefixGroup
    },
    
}