import * as types from './mutation-types'

export const fetchReportCdr = ({ commit }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/custom-search/reports-cdr?` + params)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const indexPbxTenants = ({ commit }) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/custom-search/pbx-tenant`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


export const indexPbxTenantservice = ({ commit }) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/custom-search/pbx-tenant-service`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchCustomSearch = ({ commit }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/custom-search`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPbxTenant = ({ commit }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/pbx/tenant`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}



export const showCustomSearch = ({ commit }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/custom-search/${id}`, )
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}


// indexExtensions
export const indexExtensions = ({ commit }, tenant) => {
    //console.log(tenant, "tenant")
    return new Promise((resolve, reject) => {
        window.axios.post(`/api/v1/custom-search/extension`, tenant)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// createCustomSearch
export const createCustomSearch = ({ commit }, customSearch) => {
    return new Promise((resolve, reject) => {
        window.axios.post(`/api/v1/custom-search`, customSearch)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// updateCustomSearch
export const updateCustomSearch = ({ commit }, customSearch) => {
    return new Promise((resolve, reject) => {
        window.axios.put(`/api/v1/custom-search/${customSearch.id}`, customSearch)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// deleteCustomSearch
export const deleteCustomSearch = ({ commit }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.delete(`/api/v1/custom-search/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}