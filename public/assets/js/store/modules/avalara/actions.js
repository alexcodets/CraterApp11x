import * as types from './mutation-types'

export const addAvalaraConfig = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/avalara/config', data)
      .then((response) => {
        commit(types.ADD_AVALARA_CONFIG, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchAvalaraConfigs = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/avalara/config`, { params })
      .then((response) => {
        commit(types.SET_AVALARA_CONFIGS, response.data.list.data)
        commit(types.SET_TOTAL_AVALARA_CONFIGS, response.data.avalaraConfigsTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchGroupMembership = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/avalara/config/groups`, { params })
      .then((response) => {
        commit(types.SET_AVALARA_CONFIGS_GROUP, response.data.data)
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const saveName = ({ commit, dispatch, state }, name) => {
  return new Promise((resolve, reject) => {
    commit(types.CREATE_AVALARA_CONFIG_GROUP, name)
    resolve(true)
  })
}

export const fetchItemGroups = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/avalara/config/item-groups`, { params })
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
    window.axios.get(`/api/v1/avalara/config/tax-groups`, { params })
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchAvalaraConfig = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/avalara/config/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const checkAvalaraCredentials = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/avalara/config/${id}/check/credentials/`)
      .then((response) => {
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
      .post('/api/v1/avalara/config/add-groups', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const destroyItemAvalaraConfigsGroup = ({ commit, dispatch, state }, params) => {
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

export const deleteAvalaraConfig = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/v1/avalara/config/${id}`, )
      .then((response) => {
        if (response.data.error) {
          resolve(response)
        } else {
          commit(types.DELETE_AVALARA_CONFIG, id)
          resolve(response)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteMultipleAvalaraConfigs = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/package/delete`, { ids: state.selectedAvalaraConfigs })
      .then((response) => {
        if (response.data.error) {
          resolve(response)
        } else {
          commit(types.DELETE_MULTIPLE_AVALARA_CONFIGS, state.selectedAvalaraConfigs)
          resolve(response)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateAvalaraConfig = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/avalara/config/${data.id}`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const searchAvalaraConfig = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/package?${data}`)
      .then((response) => {
        // commit(types.UPDATE_AVALARA_CONFIG, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectAvalaraConfig = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_AVALARA_CONFIGS, data)
  if (state.selectedAvalaraConfigs.length === state.avalara.configs.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllAvalaraConfigs = ({ commit, dispatch, state }) => {
  if (state.selectedAvalaraConfigs.length === state.avalara.configs.length) {
    commit(types.SET_SELECTED_AVALARA_CONFIGS, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allAvalaraConfigIds = state.avalara.configs.map((inv) => inv.id)
    commit(types.SET_SELECTED_AVALARA_CONFIGS, allAvalaraConfigIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const resetSelectedAvalaraConfigs = ({ commit, dispatch, state }) => {
  commit(types.RESET_SELECTED_AVALARA_CONFIGS)
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

export const fetchAvalaraItemTaxes = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(
        `/api/v1/invoices/avalara-tax/${data.user_id}/${data.id}`,
        data.data
      )
      .then((response) => {
        //commit(types.ADD_INVOICE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchAvalaraItems = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/avalara/config/avalara-items`, { params })
      .then((response) => {
        commit(types.SET_AVALARA_ITEMS, response.data.items)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}


export const fetchAvalaraLogs = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/v1/avalara/config/logs`, { params })
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}




