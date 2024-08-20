import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_CUSTOM_DID_GROUPS](state, customDidGroups) {
        state.customDidGroups = customDidGroups
    },

    [types.SET_TOTAL_CUSTOM_DID_GROUPS](state, totalCustomDidGroups) {
        state.totalCustomDidGroups = totalCustomDidGroups
    },

    [types.ADD_CUSTOM_DID_GROUP](state, data) {
        state.customDidGroups.push(data.customDidGroup)
    },

    [types.UPDATE_CUSTOM_DID_GROUP](state, data) {
        let pos = state.customDidGroups.findIndex(
            (customDidGroup) => customDidGroup.id === data.customDidGroup.id
        )
        state.customDidGroups[pos] = data.customDidGroup
    },

    [types.DELETE_CUSTOM_DID_GROUP](state, id) {
        let index = state.customDidGroups.findIndex((customDidGroup) => customDidGroup.id === id)
        state.customDidGroups.splice(index, 1)
    },

    [types.DELETE_MULTIPLE_CUSTOM_DID_GROUPS](state, selectedCustomDidGroups) {
        selectedCustomDidGroups.forEach((customDidGroup) => {
            let index = state.customDidGroups.findIndex((_ctmGr) => _ctmGr.id === customDidGroup.id)
            state.customDidGroups.splice(index, 1)
        })
        state.selectedCustomDidGroups = []
    },

    [types.SET_SELECTED_CUSTOM_DID_GROUPS](state, data) {
        state.selectedCustomDidGroups = data
    },

    [types.RESET_SELECTED_CUSTOM_DID_GROUP](state, data) {
        state.selectedCustomDidGroups = null
    },

    [types.SET_SELECT_ALL_STATE](state, data) {
        state.selectAllField = data
    },

    [types.SET_SELECTED_VIEW_CUSTOM_DID_GROUP](state, selectedViewCustomDidGroup) {
        state.selectedViewCustomDidGroup = selectedViewCustomDidGroup
    },

    [types.SET_CLONED_DID_GROUP](state, clonedDidGroup) {
        state.clonedDidGroup = clonedDidGroup
    },

    [types.RESET_CLONED_DID_GROUP](state) {
        state.clonedDidGroup = null
    },

}