/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'
import router from './router.js'
import store from './store/index'
import utils from './helpers/utilities'
import i18n from './plugins/i18n'
import swal from 'sweetalert'
import JsonCSV from 'vue-json-csv'

// VueSweetalert2 (Alerts/Modals)
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);

require('./bootstrap')

Vue.prototype.$utils = utils
Vue.component('downloadCsv', JsonCSV)

import changeColorPrimaryTailwind from '@/helpers/changeColorPrimaryTailwind'
changeColorPrimaryTailwind()

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
window.hub = new Vue()

window.i18n = i18n

new Vue({
  router,
  store,
  i18n,
  swal  
}).$mount('#app')
