import * as types from './mutation-types'

export const fetchPaypals = ({ commit, dispatch, state }, params) => {

    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/paypal-settings`, { params })
            .then((response) => {
                commit(types.BOOTSTRAP_PAYPAL, response.data.paypal.data)
                commit(types.SET_TOTAL_PAYPAL, response.data.paypalTotalCount)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const fetchPaypal = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/paypal-settings/${params}`)
            .then((response) => {
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/*export const fetchViewAuthorization = ({ commit, dispatch }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/authorize-settings/${params}`, { params })
      .then((response) => {
        commit(types.SET_SELECTED_VIEW_AUTHORIZATION, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}*/

export const addPaypal = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        //data['_token'] =  document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.axios
            .post('/api/v1/paypal-settings', data)
            .then((response) => {
                commit(types.ADD_PAYPAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const updatePaypal = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .put(`/api/v1/paypal-settings/${data.id}`, data)
            .then((response) => {
                if (response.data.success) {
                    commit(types.UPDATE_PAYPAL, response.data)
                }
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const deletePaypal = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post(`/api/v1/paypal-settings/delete`, id)
            .then((response) => {
                commit(types.DELETE_PAYPAL, id)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

/*export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}
  
export const resetSelectedAuthorization = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_AUTHORIZATION)
}
  
export const addAuthorize = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/authorize-charge', data)
      .then((response) => {
        commit(types.ADD_AUTHORIZE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
  
export const saveAuthorizeDB = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/authorize-save', data)
      .then((response) => {
        commit(types.SAVE_AUTHORIZE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
  
export const voidAuthorize = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/authorize-void', data)
      .then((response) => {
        commit(types.VOID_AUTHORIZE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
  
export const refundedAuthorize = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/authorize-refunded', data)
      .then((response) => {
        commit(types.REFUNDED_AUTHORIZE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}*/

export const updatePaypalStatus = ({ commit, dispatch, state }, data) => {
    return new Promise((resolve, reject) => {
        window.axios
            .post('/api/v1/paypal-settings/change-status', data)
            .then((response) => {

                commit(types.UPDATE_STATUS_PAYPAL, response.data)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

export const savePaypalDB = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/paypal-checkout', data)
      .then((response) => {
        commit(types.SAVE_PAYPAL, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const getPublicKeyPaypal = ({ commit}) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/paypal-settings/public-key-paypal')
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const savePaypalPayment = ({ commit}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/paypal-save-payment', data)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}



/*export const addAuthorizeACH = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    const headers = {
      'Content-Type': 'application/json'
    }
    window.axios
      .post('/api/v1/authorize-ach', data,{
        headers: headers
      })
      .then((response) => {
        commit(types.ADD_AUTHORIZE_ACH, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const saveAuthorizeACH = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/authorize-save-ach', data)
      .then((response) => {
        commit(types.SAVE_AUTHORIZE_ACH, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}*/