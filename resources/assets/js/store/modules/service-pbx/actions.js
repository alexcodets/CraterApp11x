import * as types from './mutation-types';

export const fetchViewPbxService = ({ commit, dispatch, state }, id) => {
    return new Promise((resolve, reject) => {
        window.axios
            .get(`/api/v1/pbx/services/${id}`)
            .then((response) => {
                // commit(types.SET_SELECTED_PBX_SERVICE, response.data.pbxService)
                resolve(response)
            })
            .catch((err) => {
                reject(err)
            })
    })
}