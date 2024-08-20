<template>
  <div v-if="isAppLoaded" class="h-full">
    <base-modal />
    <site-header-customer/>
    <div class="flex h-screen pt-16 pb-10 overflow-hidden">
      <site-sidebar />
      <router-view />
    </div>
    <auto-logout
      v-if="intiAutoLogout"
      :idle-time="parseInt(idleTimeLogout)"
    />
    <site-footer :footer="footer"/>
  </div>
  <div v-else class="h-full">
    <refresh-icon class="h-6 animate-spin" />
  </div>
</template>

<script type="text/babel">
import SiteHeader from './partials/TheSiteHeader.vue'
import SiteHeaderCustomer from './partials/TheSiteHeaderCustomer.vue'
import SiteFooter from './partials/TheSiteFooter.vue'
import SiteFooterCustomer from './partials/TheSiteFooterCustomer.vue'
import SiteSidebar from './partials/TheSiteSidebarCustomer.vue'
import BaseModal from '../../components/base/modal/BaseModal'
import AutoLogout from './partials/AutoLogout'
import { RefreshIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    SiteHeader,
    SiteSidebar,
    SiteFooter,
    SiteHeaderCustomer,
    SiteFooterCustomer,
    BaseModal,
    AutoLogout,
    RefreshIcon,
  },
  data() {
    return {
      intiAutoLogout: false,
      footer: {}
    }
  },
  computed: {
    ...mapGetters(['isAppLoaded']),

    ...mapGetters('company', {
      selectedCompany: 'getSelectedCompany',
      idleTimeLogout: 'getIdleTimeLogout'
    }),

    isShow() {
      return true
    },

    footerData() {
      return {       
        ...this.footer,
      }
    }
  },

  created() {
    this.setInitialData()
    this.bootstrap().then(() => {
      this.setInitialCompany()
      this.intiAutoLogout = true
    })
  },

  methods: {
    ...mapActions(['bootstrap']),

    ...mapActions('company', ['setSelectedCompany', 'fetchCompanySettings']),

    setInitialCompany() {
      this.setSelectedCompany(this.selectedCompany)
    },

    async setInitialData() {
      let res = await this.fetchCompanySettings(['footer_text_value', 'footer_url_value', 'footer_url_name', 'current_year'])

      if (res.data.footer_text_value) {
        this.footer.footer_text_value = res.data.footer_text_value
      }
      if (res.data.footer_url_value) {
        this.footer.footer_url_value = res.data.footer_url_value
      }
      if (res.data.footer_url_value) {
        this.footer.footer_url_name = res.data.footer_url_name
      }
      if (res.data.footer_url_value) {
        this.footer.current_year = res.data.current_year
      }            

    },
  },  
  mounted() {
    //this.setInitialData()
  },
}
</script>
