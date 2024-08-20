import * as types from './mutation-types'

export const addServer = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/pbx/servers/insert', data)
            .then((response) => {
                commit(types.ADD_MODULE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const updateaAddOnStatus = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/add-ons/update/${id}`)
            .then((response) => {
                // commit(types.ADD_MODULE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchAddOns = ({
    commit,
    dispatch
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/add-ons`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchModules = ({
    commit,
    dispatch
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/modules`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchServers = ({
    commit,
    dispatch
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/servers/`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_MODULES, response.data.pbxServers.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxServer = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/servers/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePbxServer = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/pbx/servers/update/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_MODULE, response.data)
                    resolve(response)
                }

            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deletePbxServer = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .delete(`/api/v1/pbx/servers/delete/${id}`)
            .then((response) => {
                commit(types.DELETE_MODULE, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getContienents = ({ commit }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/timezones/contienents`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getTimeZone = ({ commit }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/timezones/fulltimezone`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const getZoneByContinent = ({ commit }, continent) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/timezones/zonebycontinent/${continent}`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/**
 * fetch pbx jobs logs
 */
export const fetchPbxJobsLogs = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/pbx/jobs/logs`, { params })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/**
 * test conection
 */
export const testServerConection = ({}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/server/${id}/check-connection`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/**
 * get module
 */
export const getModule = ({}, param) => {
        return new Promise((resolve, reject) => {
            window.axios
                .get(`/api/v1/modules/${param.name}/check`)
                .then((response) => {
                    resolve(response.data)
                })
                .catch((err) => {
                    reject(err)
                })
        })
    }
    /**
     * get module
     */
export const getModules = ({ commit }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/modules/check-modules`, { params })
            .then((response) => {

                commit(types.ADD_MODULES, response.data)
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
