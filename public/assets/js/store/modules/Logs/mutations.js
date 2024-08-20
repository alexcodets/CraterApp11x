import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_MODULE_LOGS](state, moduleLogs) {
    state.moduleLogs = moduleLogs
  },

  [types.SET_TOTAL_MODULE_LOGS](state, totalModuleLogs) {
    state.totalModuleLogs = totalModuleLogs
  },

  [types.LIST_MODULES](state, listModules) {
    state.listModules = listModules
  },

  [types.LIST_TASKS](state, listTasks) {
    state.listTasks = listTasks
  },

  //-------------------- Email ----------------------

  [types.BOOTSTRAP_EMAIL_LOGS](state, emailLogs) {
    state.emailLogs = emailLogs
  },

  [types.SET_TOTAL_EMAIL_LOGS](state, totalEmailLogs) {
    state.totalEmailLogs = totalEmailLogs
  },
}
