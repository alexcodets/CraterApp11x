import * as types from './mutation-types'

export const fetchInternacionals = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate`, { params })
            .then((response) => {
                //
                commit(types.SET_INTERNATIONALS, response.data.internacionals.data
                                                 ? response.data.internacionals.data
                                                 : [])
                //   
                commit(types.BOOTSTRAP_INTERNACIONALS, response.data.internacionals.data ? response.data.internacionals.data : response.data.internacionals)
                commit(types.SET_TOTAL_INTERNACIONALS, response.data.internacionalTotalCount)
                resolve(response)
                //
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchInternacional = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchViewInternacional = ({ commit, dispatch }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate/${params}`, { params })
            .then((response) => {
                commit(types.SET_SELECTED_VIEW_INTERNACIONAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const addInternacional = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/profile/international-rate', data)
            .then((response) => {
                commit(types.ADD_INTERNACIONAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateInternacional = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/profile/international-rate/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_INTERNACIONAL, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

// /corePBX/billing-templates/prefix-groups/ID/edit

export const fetchPrefixInternational = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/prefix-groups-new`, {
                params
            })
            .then((response) => {
                commit(types.SET_INTERNATIONALS, response.data.international_rate.data)                            
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePrefixInternational = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/profile/update-prefix-international`, data)
            .then((response) => {                
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deletePrefixInternational = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/profile/delete-prefix-international`, data)
            .then((response) => {                
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}
export const modifySelected = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post(`/api/v1/profile/modify-selected-prefix-international`, 
        {   
            data: data,
            internationalsSelectedIds: state.selectedInternacionals
        })
        .then((response) => {
          if(response.data.success)
          {
            commit(types.SET_SELECT_ALL_STATE, false)  
            commit(types.SET_INTERNATIONALS, [])
            commit(types.SET_SELECTED_INTERNACIONALS, [])
          }    
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
}

export const modifyAll = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
      window.axios
        .post(`/api/v1/profile/modify-all-prefix-international`, 
        {   
            data: data,
            internationals: state.internationals
        })
        .then((response) => {  
          if(response.data.success)
          {
            commit(types.SET_SELECT_ALL_STATE, false)            
            commit(types.SET_INTERNATIONALS, [])
            commit(types.SET_SELECTED_INTERNACIONALS, [])
          }      
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
}

//

export const deleteInternacional = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/profile/international-rate/delete`, { id })
            .then((response) => {
                commit(types.DELETE_INTERNACIONAL, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const CargarCustomDestination = ({ commit, dispatch, state }) => {

    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/international-rate/prefix-rate`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const ImportarCsvDestination = ({ commit, dispatch, state }, formData) => {
    return new Promise((resolve, reject) => {
        window.axios
        .post( '/api/v1/profile/international-rate/import-excel',
            formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        ).then((response) => {
            resolve(response)
        })
        .catch((err) => {
            reject(err)
        });
        })
}

// Select one or Select all

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
    commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectInternational = ({
    commit,
    dispatch,
    state
}, data) => {
    commit(types.SET_SELECTED_INTERNACIONALS, data) 
    if (state.selectedInternacionals.length === state.internationals.length) {
        commit(types.SET_SELECT_ALL_STATE, true)
    } else {       
        commit(types.SET_SELECT_ALL_STATE, false)
    }
}

export const selectAllInternacionals = ({
    commit,
    dispatch,
    state
}) => {
    
    if (state.selectedInternacionals.length === state.internationals.length) {       
        commit(types.SET_SELECTED_INTERNACIONALS, [])
        commit(types.SET_SELECT_ALL_STATE, false)
    } else {     
        let allInternationalsIds = state.internationals.map((inv) => inv.id)      
        commit(types.SET_SELECTED_INTERNACIONALS, allInternationalsIds)
        commit(types.SET_SELECT_ALL_STATE, true)
    }
}

export const resetSelectedInternacional = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_INTERNACIONAL)
}

export const resetSelectedGroup = ({ commit, dispatch, state }, data) => {
    commit(types.RESET_SELECTED_INTERNACIONAL)
}

//
