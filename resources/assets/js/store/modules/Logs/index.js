import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
    Logs: [],
    totalLogs: 0,
    selectAllField: false,
    totalModuleLogs: 0,
    totalEmailLogs: 0,
    listModules: [],
    listTasks: []
}

export default {
    namespaced: true,
    state: initialState,
    getters: getters,
    actions: actions,
    mutations: mutations
}
