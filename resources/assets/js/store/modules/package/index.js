import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    packages: [],
    packageTemplateId: 1,
    selectedPackages: [],
    selectAllField: false,
    totalPackages: 0,
    selectedCustomer: null,
    selectedNote: null,
    selectedItem: null,
    packageGroupName: null,
    group: [],
    AvailableGroups: []
}

export default {
    namespaced: true,

    state: initialState,

    getters: getters,

    actions: actions,

    mutations: mutations,
}