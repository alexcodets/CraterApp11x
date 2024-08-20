<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.mail.mail_config') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.mail.mail_config_desc') }}
        </p>
      </template>
      <div v-if="mailConfigData">
        <component
          :is="mail_driver"
          :config-data="mailConfigData"
          :loading="isLoading"
          :mail-drivers="mail_drivers"
          @on-change-driver="
            (val) => (mail_driver = mailConfigData.mail_driver = val)
          "
          @submit-data="saveEmailConfig"
        >
          <sw-button
            variant="primary-outline"
            type="button"
            class="ml-2"
            @click="openMailTestModal"
          >
            {{ $t('general.test_mail_conf') }}
          </sw-button>
        </component>
      </div>

      <div class="flex w-full" v-if="permissionModule.update">
        <div class="relative w-12">
          <sw-switch
            v-model="send_email_deactive"
            class="absolute"
            style="top: -18px"
            @change="setChangeModeTestSettings"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.mail.send_email_deactive') }}
          </p>
        </div>
      </div>
    </sw-card>
  </div>
</template>

<script>
import Smtp from './mail-driver/SmtpMailDriver'
import Mailgun from './mail-driver/MailgunMailDriver'
import Ses from './mail-driver/SesMailDriver'
import Basic from './mail-driver/BasicMailDriver'
import { mapActions } from 'vuex'

export default {
  components: {
    Smtp,
    Mailgun,
    Ses,
    sendmail: Basic,
    mail: Basic,
  },

  data() {
    return {
      mailConfigData: null,
      mail_driver: 'smtp',
      isLoading: false,
      isRequestOnGoing: false,
      mail_drivers: [],
      send_email_deactive: false,
      permissionModule:{
        create: false,
        read: false,
        update: false,
        delete: false,
      }
    }
  },
  created(){
    this.permissionsUserModule()
  },

  mounted() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    ...mapActions('company', [
      'fetchMailDrivers',
      'fetchMailConfig',
      'updateMailConfig',
      'fetchCompanySettings',
      'updateCompanySettings',
    ]),

    async loadData() {
      this.isRequestOnGoing = true
      let mailDrivers = await this.fetchMailDrivers()

      let mailData = await this.fetchMailConfig()

      let configRes = await this.fetchCompanySettings([
        'send_email_deactive',
      ])

      this.send_email_deactive = configRes.data.send_email_deactive == 'YES' ? true : false

      if (mailDrivers.data) {
        this.mail_drivers = mailDrivers.data
      }

      if (mailData.data) {
        this.mailConfigData = mailData.data
        this.mail_driver = mailData.data.mail_driver
      }
      this.isRequestOnGoing = false
    },

    async saveEmailConfig(mailConfigData) {
      try {
        this.isLoading = true
        let response = await this.updateMailConfig(mailConfigData)
        if (response.data.success) {
          this.isLoading = false
          window.toastr['success'](
            this.$t('wizard.success.' + response.data.success)
          )
        } else {
          window.toastr['error'](
            this.$t('wizard.errors.' + response.data.error)
          )
        }
        return true
      } catch (e) {
        window.toastr['error']('Something went wrong')
      }
    },

    openMailTestModal() {
      this.openModal({
        title: 'Test Mail Configuration',
        componentName: 'MailTestModal',
      })
    },
    async setChangeModeTestSettings() {
      try{
        let data = {
        settings: {
            send_email_deactive: this.send_email_deactive ? 'YES' : 'NO',
          },
        }

        let response = await this.updateCompanySettings(data)
        if (response.data.success) {
          window.toastr['success'](
            this.$t('settings.mail.send_email_deactive_updated_successfully')
          )
        } else {
          window.toastr['error'](
            this.$t('wizard.errors.' + response.data.error)
          )
        }

      }catch(e){
        window.toastr['error']('Something went wrong')
      }
      // this.send_email_deactive = !this.send_email_deactive
    },
    
async permissionsUserModule(){
    const data = {
       module: "mail_configuration" 
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
        }else if(modulePermissions.access == 0 ){
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
