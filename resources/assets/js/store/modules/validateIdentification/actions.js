import * as types from './mutation-types'

export const validateDocumentation = ({ commit, dispatch, state }, payload) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/verify/document`, payload)
      .then((response) => {
        resolve(response.data)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
