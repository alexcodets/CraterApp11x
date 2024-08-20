<template>
  <div v-if="isSuperAdmin" class="items">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card>
      <sw-tabs class="p-2">

        <!-- Packages -->
        <sw-tab-item :title="$t('corePbx.menu_title.packages')">
          <packages-tab :settings="settings" :permission="permissionModule"/>
        </sw-tab-item>

       <!-- Extension -->
        <sw-tab-item :title="$t('corePbx.menu_title.extensions')">
          <extensions-tab :settings="settings" :permission="permissionModule"/>
        </sw-tab-item>

         <!-- Didd -->
        <sw-tab-item :title="$t('corePbx.menu_title.did')">
          <did-tab :settings="settings" :permission="permissionModule"/>
        </sw-tab-item>

        <!-- Services -->
        <sw-tab-item :title="$t('corePbx.menu_title.services')">
          <services-tab :settings="settings" :permission="permissionModule"/>
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
      permissionModule:{
        create: false,
        delete: false,
        read: false,
        update: false,
      }
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
    this.permissionsUserModule()
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('user', ['getUserModules']),
    async fetchSettings() {
      this.isRequestOnGoing = true

      let res = await this.fetchCompanySettings([
        'packages_pbx_prefix',
        'server_notification',
        'activate_notification',
        'pbx_creation_services',
        'pbx_creation_services_subject',
        'pbx_suspension_services',
        'pbx_suspension_services_subject',
        'pbx_cancellation_services',
        'pbx_cancellation_services_subject',
        'pbx_reactivation_services',
        'pbx_reactivation_services_subject',
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
        'pbx_server_emailbody',
        'server_subject_up',
        'pbx_server_emailbody_up',
        'pbx_service_bbc_email',
        'pbx_ext_subject_down',
        'pbx_ext_body_down',
        'pbx_ext_subject_up',
        'pbx_ext_body_up'
      ])     
      this.settings = res.data.length!=0 ? res.data : {} 
      this.isRequestOnGoing = false
    },
    async permissionsUserModule(){
      const data = {
        module: "pbx_customization" 
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          } else if(modulePermissions.access == 0){
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if(permissions.super_admin == true){
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      }else if(permissions.exist == true ){
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions.create == 1){
            this.permissionModule.create = true
        }
        if(modulePermissions.update == 1){
            this.permissionModule.update = true
        }
        if(modulePermissions.delete == 1){
            this.permissionModule.delete = true
        }
        if(modulePermissions.read == 1){
            this.permissionModule.read = true
        }
      }

    }
  },
}
</script>
