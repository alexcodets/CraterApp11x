import * as types from './mutation-types'

export const fetchPackages = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/packages`, { params })
            .then((response) => {
                commit(types.SET_PACKAGES, response.data.packages.data)
                commit(types.SET_TOTAL_PACKAGES, response.data.packageTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchGroupMembership = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/packages/groups`, { params })
            .then((response) => {
                commit(types.SET_PACKAGES_GROUP, response.data.data)
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const saveName = ({ commit, dispatch, state }, name) => {
    return new Promise((resolve, reject) => {
        commit(types.CREATE_PACKAGE_GROUP, name)
        resolve(true)
    })
}

export const fetchItemGroups = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/packages/item-groups`, { params })
            .then((response) => {
                // commit(types.SET_TAX_TYPES, response.data.taxTypes.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchGroupTaxMembership = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/packages/tax-groups`, { params })
            .then((response) => {
                resolve(response.data)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPackage = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/packages/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addPackage = ({ commit, dispatch, state }, data) => {

    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/packages', data)
            .then((response) => {
                commit(types.ADD_PACKAGE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const saveGroup = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/packages/add-groups', data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const destroyItemPackagesGroup = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.delete(`/api/v1/destroy-item-packages-group`, { params })
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deletePackage = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios.delete(`/api/v1/packages/${id}`, )
            .then((response) => {
                if (response.data.error) {
                    resolve(response)
                } else {
                    commit(types.DELETE_PACKAGE, id)
                    resolve(response)
                }
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteMultiplePackages = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/package/delete`, { ids: state.selectedPackages })
            .then((response) => {
                if (response.data.error) {
                    resolve(response)
                } else {
                    commit(types.DELETE_MULTIPLE_PACKAGES, state.selectedPackages)
                    resolve(response)
                }
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePackage = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/packages/${data.id}`, data)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const searchPackage = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/package?${data}`)
            .then((response) => {
                // commit(types.UPDATE_PACKAGE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const selectPackage = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECTED_PACKAGES, data)
    if (state.selectedPackages.length === state.package.length) {
        commit(types.SET_SELECT_ALL_STATE, true)
    } else {
        commit(types.SET_SELECT_ALL_STATE, false)
    }
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllPackages = ({ commit, dispatch, state }) => {
    if (state.selectedPackages.length === state.package.length) {
        commit(types.SET_SELECTED_PACKAGES, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {
        let allPackageIds = state.package.map((inv) => inv.id)
        commit(types.SET_SELECTED_PACKAGES, allPackageIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const resetSelectedPackages = ({ commit, dispatch, state }) => {
    commit(types.RESET_SELECTED_PACKAGES)
}
export const setCustomer = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_CUSTOMER)
    commit(types.SET_CUSTOMER, data)
}

export const resetCustomer = ({ commit, dispatch, state }) => {
    commit(types.RESET_CUSTOMER)
}

export const setTemplate = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        commit(types.SET_TEMPLATE_ID, data)
        resolve({})
    })
}

export const resetSelectedCustomer = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_CUSTOMER)
}

export const setItem = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_ITEM)
    commit(types.SET_ITEM, data)
}

export const resetItem = ({ commit, dispatch, state }) => {
    commit(types.RESET_ITEM)
}

export const selectNote = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_NOTE)
    commit(types.SET_SELECTED_NOTE, data.notes)
}

export const resetSelectedNote = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_NOTE)
}

export const fetchPackagesByGroup = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios.get(`/api/v1/packages-by-group`, { params })
            .then((response) => {
                commit(types.SET_PACKAGES_BY_GROUP, response.data.packagesByGroup)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewPackage = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/packages/${params.id}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_PACKAGE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPackagesServices = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/packages-services/${params.package_id}/services`, {
                params
            })
            .then((response) => {
                commit(types.PACKAGES_LIST, response.data.packagesList.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}