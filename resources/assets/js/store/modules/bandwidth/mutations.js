import * as types from './mutation-types'

export default {
    [types.BOOTSTRAP_BANDWIDTHS](state, bandwidths) {
        state.bandwidths = bandwidths
    },

    [types.SET_TOTAL_BANDWIDTHS](state, totalBandwidths) {
        state.totalBandwidths = totalBandwidths
    },

    [types.ADD_BANDWIDTH](state, data) {
        state.bandwidths.push(data.bw_config)
    },

    [types.UPDATE_BANDWIDTH](state, data) {
        let pos = state.bandwidths.findIndex((bandwidth) => bandwidth.id === data.bw_config.id)

        state.bandwidths[pos] = data.bw_config
    },

    [types.DELETE_BANDWIDTH](state, id) {
        let index = state.bandwidths.findIndex((bandwidth) => bandwidth.id === id)
        state.bandwidths.splice(index, 1)
    }
}
