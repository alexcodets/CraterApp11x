import * as types from './mutation-types'
import changeColorPrimaryTailwind from '@/helpers/changeColorPrimaryTailwind'

export default {
  [types.RESET_CURRENT_USER](state, user) {
    state.currentUser = null
  },

  [types.BOOTSTRAP_CURRENT_USER](state, user) {
    state.currentUser = user
  },

  [types.BOOTSTRAP_MODULES_ACTIVE](state, modules) {
    state.activeModules = modules
  },

  [types.UPDATE_CURRENT_USER](state, user) {
    state.currentUser = user
  },

  [types.UPDATE_USER_AVATAR](state, data) {
    if (state.currentUser) {
      state.currentUser.avatar = data.avatar
    }
  },

  [types.SET_DEFAULT_LANGUAGE](state, data) {
    window.i18n.locale = data
  },

  [types.SET_COMPANY_LOGO](state, user) {
    state.companyLogo = user
  },
  [types.BOOTSTRAP_SETTINGS_COMPANY](state, settingsCompany) {
    state.settingsCompany = settingsCompany
  },
  [types.SET_PRIMARY_COLOR](state, primary_color) {
    if (primary_color) {
      changeColorPrimaryTailwind(primary_color)
      localStorage.setItem('primary_color', primary_color)
    }
  },
}
