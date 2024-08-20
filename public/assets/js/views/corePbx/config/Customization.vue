<template>
  <div v-if="isSuperAdmin" class="items">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card>
      <sw-tabs class="p-2">

        <!-- Packages -->
        <sw-tab-item :title="$t('corePbx.menu_title.packages')">
          <packages-tab :settings="settings"/>
        </sw-tab-item>

       <!-- Extension -->
        <sw-tab-item :title="$t('corePbx.menu_title.extensions')">
          <extensions-tab :settings="settings"/>
        </sw-tab-item>

         <!-- Didd -->
        <sw-tab-item :title="$t('corePbx.menu_title.did')">
          <did-tab :settings="settings"/>
        </sw-tab-item>

        <!-- Services -->
        <sw-tab-item :title="$t('corePbx.menu_title.services')">
          <services-tab :settings="settings"/>
        </sw-tab-item>
        
      </sw-tabs>
    </sw-card>
  </div>
</template>


<script>
import DidTab from './customization-tabs/DIDTab'
import ExtensionsTab from './customization-tabs/ExtensionTab'
import PackagesTab from './customization-tabs/PackagesTab'
import ServicesTab from './customization-tabs/ServicesTab'
import { mapGetters,mapActions } from 'vuex'

export default {
  data() {
    return {
      settings: {},
      isRequestOnGoing: false,
    }
  },

  components: {
    DidTab,
    ExtensionsTab,
    PackagesTab,
    ServicesTab,
  },

computed: {
        ...mapGetters('user', ['currentUser']),
         isSuperAdmin() {
            return this.currentUser.role == 'super admin'
        },
    },

   

  created() {
    this.fetchSettings()
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    async fetchSettings() {
      this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
        'packages_pbx_prefix',
        'pbx_creation_services',
        'pbx_suspension_services',
        'pbx_cancellation_services',
        'pbx_reactivation_services',
        'extension_pbx_prefix',
        'did_pbx_prefix',
        'pbx_services_prefix',
        'allow_renewal_date_job_pbx',
        'time_run_renewal_date_job_pbx',
        'time_run_suspension_pbx_job',
        'allow_suspension_pbx_job',
        'period_run_unsuspend_job',
        'allow_unsuspend_pbx_job',
        'server_subject',
        'pbx_server_emailbody'
      ])

      this.settings = res.data.length!=0 ? res.data : {}
      console.log(this.settings);
      this.isRequestOnGoing = false
    },
  },
}
</script>
