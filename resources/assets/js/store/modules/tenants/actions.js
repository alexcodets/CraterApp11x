import * as types from './mutation-types'

export const fetchPbxTenantsList = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/`, { params })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const fetchPbxServices = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/servers`, { params })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxServerPackages = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server/${id}/tenant-packages`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxServerCountry = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server/${id}/routes`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addTenants = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v2/pbx-server/${data.pbxServerId}/tenants`, data)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const statusCompletedTenants = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v2/pbx-server-tenants/${id}/complete`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxTenantsShow = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${params.id}`, { params })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const tablePbxServer = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${data.id}/pbx-services`, {
                params: data,
            })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const tablePbxExtensions = ({ commit, dispatch, state }, { id, page, sort }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${id}/pbx-extensions`, {
                params: { page, sort },
            })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const tablePbxDids = ({ commit, dispatch, state }, { id, page, sort }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${id}/pbx-did`, {
                params: { page, sort },
            })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const tablePbxApp = ({ commit, dispatch, state }, { id, page, sort }) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${id}/pbx-app-rates`, {
                params: { page, sort },
            })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateExtensions = ({ commit, dispatch, state }, payload) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(
                `/api/v2/pbx-server-tenants/${payload.pbx_server_id}/pbx-extensions/${payload.pbx_ext_id}`,
                payload.data
            )
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const createExtensions = ({ commit, dispatch, state }, payload) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(
                `/api/v2/pbx-services/${payload.pbx_service_id}/pbx-extensions`,
                payload.data
            )
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateDids = ({ commit, dispatch, state }, payload) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(
                `/api/v2/pbx-server-tenants/${payload.pbx_server_id}/pbx-did/${payload.pbx_did_id}`,
                payload.data
            )
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectGroupDid = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server/${id}/did-groups`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectExtensionDid = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(
                `/api/v2/pbx-server-tenants/${id}/pbx-did/destination-types/extension`
            )
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const selectRingGroupsDid = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(
                `/api/v2/pbx-server-tenants/${id}/pbx-did/destination-types/ring-groups`
            )
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectIvrDid = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${id}/pbx-did/destination-types/ivr`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectQueuesDid = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${id}/pbx-did/destination-types/queues`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectUserAgentDevicesExt = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-server-tenants/${id}/user-agent-devices`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })


}

export const selectTrunksDids = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v2/pbx-services/${id}/pbx-trunks`)
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })


}

export const createDids = ({ commit, dispatch, state }, payload) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(
                `/api/v2/pbx-services/${payload.pbx_service_id}/pbx-did`,
                payload.data
            )
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}