import * as types from './mutation-types'

export const fetchModuleLogs = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/module-logs`, {params})
            .then((response) => {
                commit(types.BOOTSTRAP_MODULE_LOGS, response.data.moduleLogs.data)
                commit(types.SET_TOTAL_MODULE_LOGS, response.data.moduleLogsTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchSearchLists = ({commit, dispatch, state}, {params}) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get('/api/v1/module-logs/search-lists', {params})
            .then((response) => {
                commit(types.LIST_MODULES, response.data.listModules.data)
                commit(types.LIST_TASKS, response.data.listTasks.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err);
            })
    })
}

//-------------------- Email ----------------------

export const fetchEmailLogs = ({commit, dispatch, state}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/email-logs`, {params})
            .then((response) => {
                commit(types.BOOTSTRAP_EMAIL_LOGS, response.data.emailLogs.data)
                commit(types.SET_TOTAL_EMAIL_LOGS, response.data.emailLogsTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
