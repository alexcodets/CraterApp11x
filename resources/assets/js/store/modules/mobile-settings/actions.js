import * as types from './mutation-types'

export const fetchMobileSetting = ({ commit, dispatch, state }, idCompany = null) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/mobile-settings/${idCompany}`)
      .then((response) => {
        // commit(types.BOOTSTRAP_CURRENT_USER, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const createPushNotification = ({ commit, dispatch, state }, data = null) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/mobile/notification/push`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchMobileLogs = ({ commit, dispatch, state }, data = null) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/mobile/logs/login/list`, data)
      .then((response) => {
        // commit(types.BOOTSTRAP_CURRENT_USER, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchNotificationsLogs = ({ commit, dispatch, state }, data = null) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/mobile/logs/notifications/list`, data)
      .then((response) => {
        // commit(types.BOOTSTRAP_CURRENT_USER, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchMessagingCustomers = ({ commit, dispatch, state }, data = null) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/mobile/messaging/customer/list`, data)
      .then((response) => {
        commit(types.BOOTSTRAP_CUSTOMERS_MESSAGING, response.data.mobileMessagingCustomers.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  //console.log(state.customersMessaging);
  //console.log(state.selectedCustomersMessaging);
  commit(types.SET_SELECTED_CUSTOMERS_MESSAGING, data)
  if (state.selectedCustomersMessaging.length === state.customersMessaging.length) {
      commit(types.SET_SELECT_ALL_STATE_MESSAGING, true)
  } else {
      commit(types.SET_SELECT_ALL_STATE_MESSAGING, false)
  }
}

export const selectAllCustomers = ({
  commit,
  dispatch,
  state
}) => {
  //console.log(state.customersMessaging);
  //console.log(state.selectedCustomersMessaging);
  if (state.selectedCustomersMessaging.length === state.customersMessaging.length) {
      commit(types.SET_SELECTED_CUSTOMERS_MESSAGING, [])
      commit(types.SET_SELECT_ALL_STATE_MESSAGING, false)
  } else {
      let allCustomerIds = state.customersMessaging.map((cust) => cust.id)
      //console.log("🚀 ~ file: actions.js ~ line 209 ~ allCustomerIds", allCustomerIds)
      commit(types.SET_SELECTED_CUSTOMERS_MESSAGING, allCustomerIds)
      commit(types.SET_SELECT_ALL_STATE_MESSAGING, true)
  }
}

export const getUserPermission = ({ commit, dispatch, state }, items) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/v1/get-user-permission`, items)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const saveMobileSettings = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/mobile-settings', data)
      .then((response) => {
        // commit(types.UPDATE_USER_AVATAR, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchUserSettings = ({ commit, dispatch, state }, settings) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get('/api/v1/me/settings', {
        params: {
          settings,
        },
      })
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

/* export const updateUserSettings = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put('/api/v1/me/settings', data)
      .then((response) => {
        commit(types.SET_DEFAULT_LANGUAGE, data.settings.language)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchCompanyLogo = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/company-logo`, params)
      .then((response) => {
        commit(types.SET_COMPANY_LOGO, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
} */
