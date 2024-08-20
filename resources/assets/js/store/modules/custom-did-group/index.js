import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    customDidGroups: [],
    totalCustomDidGroups: 0,
    selectAllField: false,
    selectedCustomDidGroups: [],
    selectedViewCustomDidGroup: {},
    clonedDidGroup: null,
}

export default {
    namespaced: true,
    state: initialState,
    getters: getters,
    actions: actions,
    mutations: mutations,
}