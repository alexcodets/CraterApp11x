import * as types from './mutation-types'
import * as dashboardTypes from '../dashboard/mutation-types'
import {
  reject
} from 'lodash'

export const fetchInvoicesCustomer = ({
  commit,
  dispatch,
  state
}, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices-customer`, {
        params
      })
      .then((response) => {
        commit(types.SET_INVOICES_CUSTOMER, response.data.invoices.data)
        commit(types.SET_TOTAL_INVOICES_CUSTOMER, response.data.invoiceTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchArchivedCustomer = ({commit, dispatch, state}) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices/archived`)
      .then((response) => {
        commit(types.SET_INVOICES_ARCHIVED, response.data.invoices.data)
        commit(types.SET_TOTAL_INVOICES_ARCHIVED, response.data.invoiceTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchInvoiceCustomer = ({
  commit,
  dispatch,
  state
}, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices/${id}`)
      .then((response) => {
        commit(types.SET_TEMPLATE_ID_CUSTOMER, response.data.invoice.invoice_template_id)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const sendEmailCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/${data.id}/send`, data)
      .then((response) => {
        if (response.data.success) {
          commit(types.UPDATE_INVOICE_STATUS, {
            id: data.id,
            status: 'SENT'
          })
          commit(
            'dashboard/' + dashboardTypes.UPDATE_INVOICE_STATUS, {
              id: data.id,
              status: 'SENT'
            }, {
              root: true
            }
          )
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addInvoice = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/invoices', data)
      .then((response) => {
        commit(types.ADD_INVOICE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteInvoiceCustomer = ({
  commit,
  dispatch,
  state
}, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/delete`, id)
      .then((response) => {
        if (response.data.error) {
          resolve(response)
        } else {
          commit(types.DELETE_INVOICE, id)
          commit('dashboard/' + dashboardTypes.DELETE_INVOICE, id, {
            root: true,
          })
          resolve(response)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteMultipleInvoicesCustomer = ({
  commit,
  dispatch,
  state
}, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/delete`, {
        ids: state.selectedInvoices
      })
      .then((response) => {
        if (response.data.error) {
          resolve(response)
        } else {
          commit(types.DELETE_MULTIPLE_INVOICES, state.selectedInvoices)
          resolve(response)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateInvoice = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/invoices/${data.id}`, data)
      .then((response) => {
        if (response.data.invoice) {
          commit(types.UPDATE_INVOICE, response.data)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const RestoreInvoiceCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/invoices/${data.id}/invoice-archived`, data)
      .then((response) => {
        if (response.data.invoice) {
          commit(types.UPDATE_ARCHIVED, response.data)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const markAsSentCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/${data.id}/status`, data)
      .then((response) => {
        commit(types.UPDATE_INVOICE_STATUS, {
          id: data.id,
          status: 'SENT'
        })
        commit(
          'dashboard/' + dashboardTypes.UPDATE_INVOICE_STATUS, {
            id: data.id,
            status: 'SENT'
          }, {
            root: true,
          }
        )
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const cloneInvoiceCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/${data.id}/clone`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const searchInvoiceCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices-customer?${data}`)
      .then((response) => {
        // commit(types.UPDATE_INVOICE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const getInvoiceNumber = ({
  commit,
  dispatch,
  state
}) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/next-number?key=invoice`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectInvoiceCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.SET_SELECTED_INVOICES, data)
  if (state.selectedInvoices.length === state.invoices.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const setSelectAllStateCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllInvoicesCustomer = ({
  commit,
  dispatch,
  state
}) => {
  if (state.selectedInvoices.length === state.invoices.length) {
    commit(types.SET_SELECTED_INVOICES, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allInvoiceIds = state.invoices.map((inv) => inv.id)
    commit(types.SET_SELECTED_INVOICES, allInvoiceIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const resetSelectedInvoicesCustomer = ({
  commit,
  dispatch,
  state
}) => {
  commit(types.RESET_SELECTED_INVOICES)
}

export const setCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.RESET_CUSTOMER)
  commit(types.SET_CUSTOMER, data)
}

export const resetCustomer = ({
  commit,
  dispatch,
  state
}) => {
  commit(types.RESET_CUSTOMER)
}

export const setTemplate = ({
  commit,
  dispatch,
  state
}, data) => {
  return new Promise((resolve, reject) => {
    commit(types.SET_TEMPLATE_ID_CUSTOMER, data)
    resolve({})
  })
}

export const selectCustomer = ({
  commit,
  dispatch,
  state
}, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/customers/${id}`)
      .then((response) => {
        commit(types.RESET_SELECTED_CUSTOMER)
        commit(types.SELECT_CUSTOMER, response.data.customer)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const resetSelectedCustomer = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.RESET_SELECTED_CUSTOMER)
}

export const setItem = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.RESET_ITEM)
  commit(types.SET_ITEM, data)
}

export const resetItem = ({
  commit,
  dispatch,
  state
}) => {
  commit(types.RESET_ITEM)
}

export const selectNote = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.RESET_SELECTED_NOTE)
  commit(types.SET_SELECTED_NOTE, data.notes)
}

export const resetSelectedNote = ({
  commit,
  dispatch,
  state
}, data) => {
  commit(types.RESET_SELECTED_NOTE)
}
