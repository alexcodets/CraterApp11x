import * as types from './mutation-types'

export const fetchDIDTOLLFREEs = ({
    commit,
    dispatch,
    state
}, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/did-toll-free`, {
                params
            })
            .then((response) => {


                commit(types.BOOTSTRAP_DID_TOLL_FREE, response.data.profileDIDTOLLFREE.data)
                commit(types.SET_TOTAL_DID_TOLL_FREE, response.data.profileDIDTOLLFREE.total)
                resolve(response)
            })
            .catch((err) => {
                //CATCH ERROR DE PROFILE DIDTOLLFREE:", err)
                reject(err)
            })
    })
}

export const fetchOneDIDTOLLFREE = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/profile/did-toll-free/${id}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })

    })
}

export const addDIDTOLLFREE = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/profile/did-toll-free/insert', { 
              multiple: data.multiple, 
              status: data.statu.value, 
              toll_free_category_id: data.toll_free_category_id,
              prefijo: data.prefijo,
              rate_per_minutes: data.rate_per_minutes
            })
            .then((response) => {

                commit(types.ADD_DID_TOLL_FREE, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updateDIDTOLLFREE = ({
    commit,
    dispatch,
    state
}, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/profile/did-toll-free/update/${data.id}`, data)
            .then((response) => {

                /* if (response.data.success) {
                  commit(types.UPDATE_DID_TOLL_FREE, response.data)
                } */
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deleteDIDTOLLFREE = ({
    commit,
    dispatch,
    state
}, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/profile/did-toll-free/delete/${id}`)
            .then((response) => {
                if (response.data.error) {
                    resolve(response)
                } else {
                    commit(types.DELETE_DID_TOLL_FREE, id)
                    resolve(response)
                }
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/* export const deleteMultipleDIDTOLLFREE = ({
  commit,
  dispatch,
  state
}, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/did-toll-free/delete`, {
        ids: state.selectedDIDTOLLFREE
      })
      .then((response) => {
        commit(types.DELETE_MULTIPLE_DID_TOLL_FREE, state.selectedDIDTOLLFREE)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectDIDTOLLFREE = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.SET_SELECTED_DID_TOLL_FREE, data)
  if (state.selectedDIDTOLLFREE.length === state.didtollfree.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const selectAllDIDTOLLFREE = ({
  commit,
  dispatch,
  state
}) => {
  if (state.selectedDIDTOLLFREE.length === state.didtollfree.length) {
    commit(types.SET_SELECTED_DID_TOLL_FREE, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allDIDIds = state.didtollfree.map((dd) => dd.id)
    commit(types.SET_SELECTED_DID_TOLL_FREE, allDIDIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const setSelectAllState = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const resetSelectedDID = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.RESET_SELECTED_DID_TOLL_FREE)
} */