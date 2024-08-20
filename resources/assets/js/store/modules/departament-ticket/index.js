import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    departaments: [],
    totaldepartaments: 0,
    selectedViewDepartament: {}
}

export default {
    namespaced: true,

    state: initialState,

    getters: getters,

    actions: actions,

    mutations: mutations,
}