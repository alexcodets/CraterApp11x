<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateSetting">
      <sw-input-group :label="$t('corePbx.customization.packages_prefix')" :error="ExpensePrefixError">
        <sw-input v-model="expenses.packages_pbx_prefix" :invalid="$v.expenses.packages_pbx_prefix.$error"
          style="max-width: 30%" @input="$v.expenses.packages_pbx_prefix.$touch()"
          @keyup="changeToUppercase('EXPENSE')" />
      </sw-input-group>

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch v-model="expenses.packages_prefix_general" class="absolute" style="top: -20px" />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('corePbx.customization.apply_general_prefix') }}
          </p>
        </div>
      </div>

      <sw-button :loading="isLoading" :disabled="isLoading" variant="primary" type="submit" class="mt-4" v-if="permission.update">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <h1 class="">{{$t('corePbx.customization.pbx_server_notification')}}</h1>

    <form action="" class="mt-6" @submit.prevent="updateServerNotification">
      <sw-input-group :label="$t('corePbx.customization.server_notification')" :error="ServerNotificationError">
        <sw-input v-model="server_notification.server_notification"
          :invalid="$v.server_notification.server_notification.$error"
          @input="$v.server_notification.server_notification.$touch()" style="max-width: 30%" class="mt-6 mb-4"/>
      </sw-input-group>

      <!-- server down -->

      <sw-input-group :label="$t('corePbx.customization.pbx_server_down_subject')" :error="subjectError">
        <base-custom-input v-model="server_notification.subject" :invalid="$v.server_notification.subject.$error"
          :fields="companyFields" input="$v.server_notification.subject.$touch()"  />
      </sw-input-group>

      <sw-input-group :label="
        $t('corePbx.customization.pbx_server_down_body')
      " class="mt-6 mb-4">
        <base-custom-input v-model="server_notification.pbx_server_emailbody" :fields="companyFields" />
      </sw-input-group>

      <!-- server up-->

      <sw-input-group :label="$t('corePbx.customization.pbx_server_up_subject')" >
        <base-custom-input v-model="server_notification.subject_up" 
          :fields="companyFields"  />
      </sw-input-group>

      <sw-input-group :label="
        $t('corePbx.customization.pbx_server_up_body')
      " class="mt-6 mb-4">
        <base-custom-input v-model="server_notification.pbx_server_emailbody_up" :fields="companyFields" />
      </sw-input-group>

       <!-- extension down-->

       <sw-input-group :label="$t('corePbx.customization.pbx_extension_suspended_subject')" >
        <base-custom-input v-model="pbx_ext_subject_down" 
          :fields="companyeXTFields"  />
      </sw-input-group>

      <sw-input-group :label="$t('corePbx.customization.pbx_extension_suspended_body')" class="mt-6 mb-4">
        <base-custom-input v-model="pbx_ext_body_down" :fields="companyeXTFields" />
      </sw-input-group>

      <!-- extension up-->

      <sw-input-group :label="$t('corePbx.customization.pbx_extension_active_subject')" >
        <base-custom-input v-model="pbx_ext_subject_up" 
          :fields="companyeXTFields"  />
      </sw-input-group>

      <sw-input-group :label="$t('corePbx.customization.pbx_extension_active_body')" class="mt-6 mb-4">
        <base-custom-input v-model="pbx_ext_body_up" :fields="companyeXTFields" />
      </sw-input-group>

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch v-model="server_notification.activate_notification" class="absolute" style="top: -20px" />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('corePbx.customization.pbx_activation_notification') }}
          </p>
        </div>
      </div>

      <sw-button :loading="isLoadingSV" :disabled="isLoadingSV" variant="primary" type="submit" class="mt-4" v-if="permission.update">
        <save-icon v-if="!isLoadingSV" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  maxLength,
  alpha,
  email,
} = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
    permission: {
      type: Object,
      require: true,
    },
  },

  data() {
    return {
      expenses: {
        packages_pbx_prefix: null,
        packages_prefix_general: false,
      },
      server_notification: {
        server_notification: null,
        subject: null,
        subject_up: null,
        activate_notification: false,
        pbx_server_emailbody: null,
        pbx_server_emailbody_up: null,
      },
      pbx_ext_subject_down: null,
      pbx_ext_body_down: null,
      pbx_ext_subject_up: null,
      pbx_ext_body_up: null,
      companyFields: ['company', 'pbx_server'],
      companyeXTFields: ['company', 'pbx_extension'],
      isLoading: false,
      isLoadingSV: false,
    }
  },

  computed: {
    ExpensePrefixError() {
      if (!this.$v.expenses.packages_pbx_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.packages_pbx_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.packages_pbx_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.packages_pbx_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },

    ServerNotificationError() {
      if (!this.$v.server_notification.server_notification.$error) {
        return ''
      }
      if (!this.$v.server_notification.server_notification.email) {
        return this.$tc('validation.email_incorrect')
      }
      if (!this.$v.server_notification.server_notification.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }
    },

    subjectError() {
      if (!this.$v.server_notification.subject.$error) {
        return ''
      }
    },
  },

  validations: {
    expenses: {
      packages_pbx_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
    server_notification: {
      server_notification: {
        required,
        maxLength: maxLength(30),
        email,
      },
      subject: {
        server_notification: {
          required,
        },
      },
    },
  },

  watch: {
    settings(val) {
      if (val.hasOwnProperty('server_notification')) {
        this.server_notification.server_notification = val.server_notification
      } else {
        this.server_notification.server_notification = val.default_email
      }

      if (val.hasOwnProperty('activate_notification')) {
        this.server_notification.activate_notification =
          val.activate_notification == 1 ? true : false
      } else {
        this.server_notification.activate_notification = false
      }

      if (val.hasOwnProperty('server_subject')) {
        this.server_notification.subject = val.server_subject
      } else {
        this.server_notification.subject = 'PBX server down'
      }


      if (val.hasOwnProperty('pbx_server_emailbody')) {
        this.server_notification.pbx_server_emailbody = val.pbx_server_emailbody
      }

      if (val.hasOwnProperty('server_subject_up')) {
        this.server_notification.subject_up = val.server_subject_up
      }

      if (val.hasOwnProperty('pbx_server_emailbody_up')) {
        this.server_notification.pbx_server_emailbody_up = val.pbx_server_emailbody_up
      }

      this.expenses.packages_pbx_prefix = val ? val.packages_pbx_prefix : ''
      this.pbx_ext_subject_down = val.pbx_ext_subject_down
      this.pbx_ext_body_down = val.pbx_ext_body_down
      this.pbx_ext_subject_up = val.pbx_ext_subject_up
      this.pbx_ext_body_up = val.pbx_ext_body_up
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    changeToUppercase(currentTab) {
      if (currentTab === 'EXPENSE') {
        this.expenses.packages_pbx_prefix =
          this.expenses.packages_pbx_prefix.toUpperCase()
        return true
      }
    },

    async updateSetting() {
      try {
        this.$v.expenses.$touch()
        if (this.$v.expenses.$invalid) return false

        this.isLoading = true
        const data = {
          settings: {
            packages_pbx_prefix: this.expenses.packages_pbx_prefix,
            packages_prefix_general: this.expenses.packages_prefix_general,
          },
        }
        await this.updateCompanySettings(data)
        window.toastr['success'](
          this.$t('corePbx.customization.packages_prefix_updated')
        )
      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.packages_prefix_update_error')
        )
      } finally {
        this.isLoading = false
      }
    },

    async updateServerNotification() {
      try {

        const result = this.validateFieldIsEmpty()
        const data = {
          settings: {
            server_notification: this.server_notification.server_notification,
            activate_notification:
            this.server_notification.activate_notification,
            server_subject: this.server_notification.subject,
            pbx_server_emailbody: this.server_notification.pbx_server_emailbody,
            server_subject_up: this.server_notification.subject_up,
            pbx_server_emailbody_up: this.server_notification.pbx_server_emailbody_up,

            pbx_ext_subject_down: this.pbx_ext_subject_down,
            pbx_ext_body_down: this.pbx_ext_body_down,
            pbx_ext_subject_up: this.pbx_ext_subject_up,
            pbx_ext_body_up: this.pbx_ext_body_up
          },
          
        }
       
        if (!result.status) {
          window.toastr['error'](
            this.$t(result.message)
          )
        } else {
          await this.updateCompanySettings(data)
          window.toastr['success']('Server Notification Updated')
        }
        
      } catch (error) {
        window.toastr['error']('Error')
      } finally {
        this.isLoadingSV = false
      }
    },
    // Funcion para validar si el subject y body se envian con menos de 10 caracteres 
    // return 0 == exito, 1 == fallo de subject, 2 == fallo de body
    validateFieldIsEmpty() {
      if (this.server_notification.subject.length <= 10 || this.server_notification.subject == null || this.server_notification.subject == undefined) {
        return {status: false, message: 'validation.validation_subject'}
      }
      if (this.server_notification.pbx_server_emailbody.length <= 10 || this.server_notification.pbx_server_emailbody == null || this.server_notification.pbx_server_emailbody == undefined) {
        return {status: false, message: 'validation.validation_body_email'}
      } 
      if (this.server_notification.subject_up.length <= 10 || this.server_notification.subject_up == null || this.server_notification.subject_up == undefined) {
        return {status: false, message: 'validation.validation_subject'}
      }
      if (this.server_notification.pbx_server_emailbody_up.length <= 10 || this.server_notification.pbx_server_emailbody_up == null || this.server_notification.pbx_server_emailbody_up == undefined) {
        return {status: false, message: 'validation.validation_body_email'}
      }else {
        return {status: true}
      }
    }
  },
}
</script>