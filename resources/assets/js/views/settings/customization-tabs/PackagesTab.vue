<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateServicesSetting">


      <!-- Email -->

      <sw-input-group
            :label="$t('corePbx.customization.services_bcc_email')"
            class="md:col-span- mt-2"
            :error="emailError"
          >                  
            <sw-input
              class="input-expand"
              style="max-width: 30%"
              :invalid="$v.email.$error"
              v-model="email"
              type="text"
              name="email"
              @input="$v.email.$touch()"
            />                     
      </sw-input-group>

      <sw-divider class="mt-6 mb-4" />

       <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.packages.email_templates') }}
      </h3>

      <div class="mt-4 mb-4">
        <h1>{{$t('corePbx.customization.services_creation')}}</h1>
      </div>

      <sw-input-group class=" mt-2" :label="$t('corePbx.customization.services_subject')" >
        <base-custom-input v-model="services.creation_services_subject" 
        :fields="InvoiceMailFields" 
          />
      </sw-input-group>



      <!-- Email -->

      <sw-input-group
        :label="$t('corePbx.customization.services_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.creation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <!--  -->

      <sw-divider class="mt-6 mb-8" />
      <h1>{{$t('corePbx.customization.services_suspension')}}</h1>
      <sw-input-group class=" mt-2" :label="$t('corePbx.customization.services_subject')" >
        <base-custom-input v-model="services.suspension_services_subject" 
        :fields="InvoiceMailFields" 
          />
      </sw-input-group>

      <sw-input-group
        :label="$t('corePbx.customization.services_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.suspension_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <!--  -->

      <sw-divider class="mt-6 mb-8" />
      <h1>{{$t('corePbx.customization.services_cancellation')}}</h1>
      <sw-input-group class=" mt-2" :label="$t('corePbx.customization.services_subject')" >
        <base-custom-input v-model="services.cancellation_services_subject" 
        :fields="InvoiceMailFields" 
          />
      </sw-input-group>

      <sw-input-group
        :label="$t('corePbx.customization.services_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.cancellation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <!--  -->

      <sw-divider class="mt-6 mb-8" />
      <h1>{{$t('corePbx.customization.services_reactivation')}}</h1>
      <sw-input-group class=" mt-2" :label="$t('corePbx.customization.services_subject')" >
        <base-custom-input v-model="services.reactivation_services_subject" 
        :fields="InvoiceMailFields" 
          />
      </sw-input-group>


      <sw-input-group
        :label="$t('corePbx.customization.services_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.reactivation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <form action="" class="mt-6" @submit.prevent="updateInvoiceSetting">
      <sw-input-group
        :label="$t('settings.customization.packages.package_prefix')"
        :error="packagePrefixError"
      >
        <sw-input
          v-model="packages.packages_prefix"
          :invalid="$v.packages.packages_prefix.$error"
          style="max-width: 30%"
          @input="$v.packages.packages_prefix.$touch()"
          @keyup="changeToUppercase('PACKAGE')"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>
    <sw-divider class="mt-6 mb-4" />
    <!-- Services suspension -->
    <form action="" class="mt-6" @submit.prevent="updateSuspensionScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.packages.package_suspension') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_suspension_packages_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slidePackagesSuspensionDate(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{
              $t(
                'settings.customization.packages.allow_packagesuspension_date_job'
              )
            }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t(
                'settings.customization.packages.packagesuspension_date_job_desc'
              )
            }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="
          $t(
            'settings.customization.packages.time_run_packagesuspension_date_job'
          )
        "
        :error="timeSuspensionError"
        class="mb-4"
        required
      >
        <base-time-picker
          v-model="time_run_suspension_packages_job"
          :invalid="$v.time_run_suspension_packages_job.$error"
          :calendar-button="true"
          style="max-width: 30%"
          :placeholder="'HH:mm'"
          calendar-button-icon="calendar"
          @input="$v.time_run_suspension_packages_job.$touch()"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingsuspension"
        :disabled="isLoadingsuspension"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoadingsuspension" class="mr-2" />
        {{ $t('settings.customization.payments.save_suspension') }}
      </sw-button>
    </form>
    <sw-divider class="mt-6 mb-8" />
    <!-- Services Unsuspend -->
    <form action="" class="mt-6" @submit.prevent="updateUnsuspendScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.packages.unsuspend_services') }}
      </h3>

      <div class="flex mb-6">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_unsuspend_packages_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideUnsuspendPackages(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('corePbx.customization.allow_unsuspend_date_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('corePbx.customization.unsuspend_date_job_desc') }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="$t('corePbx.customization.period_time_run_job')"
        :error="timeUnsuspendError"
        required
      >
        <sw-input
          v-model="period_run_unsuspend_packages_job"
          :invalid="$v.period_run_unsuspend_packages_job.$error"
          style="max-width: 30%"
          @input="$v.period_run_unsuspend_packages_job.$touch()"
          type="number"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingunsuspend"
        :disabled="isLoadingunsuspend"
        variant="primary"
        type="submit"
        class="mt-4 margin-buttonl"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoadingunsuspend" class="mr-2"  />
        {{ $t('corePbx.customization.save_unsuspend') }}
      </sw-button>
    </form>
  </div>
</template>

<script>
import { requiredIf } from 'vuelidate/lib/validators'
const {
  required,
  maxLength,
  alpha,
  minValue,
  email
} = require('vuelidate/lib/validators')
import { mapActions, mapGetters } from 'vuex'

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
      packageAutogenerate: false,
      packages: {
        packages_prefix: null,
      },
      allow_suspension_packages_job: false,
      time_run_suspension_packages_job: null,
      allow_unsuspend_packages_job: false,
      period_run_unsuspend_packages_job: 1,
      isLoadingsuspension: false,
      isLoadingunsuspend: false,
      isLoading: false,
      services: {
        creation_services: null,
        creation_services_subject: null,
        suspension_services: null,
        suspension_services_subject: null,
        cancellation_services: null,
        cancellation_services_subject: null,
        reactivation_services: null,
        reactivation_services_subject: null,
      },
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'service:except:SERVICE_PBX_TENANT_ID,SERVICE_PBX_SERVICES_NUMBER,SERVICE_PBX_PACKAGES_PRICE,PACKAGES_PRICE',
        'invoiceCustom',
        'company',
      ],                
      email: null
    }
  },

  computed: {
    emailError() {
      if (!this.$v.email.$error) {
        return ''
      }

      if (!this.$v.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    packagePrefixError() {
      if (!this.$v.packages.packages_prefix.$error) {
        return ''
      }

      if (!this.$v.packages.packages_prefix.required) {
        return this.$t('validation.required')
      }
    },
    timeSuspensionError() {
      if (!this.$v.time_run_suspension_packages_job.$error) {
        return ''
      }
      if (!this.$v.time_run_suspension_packages_job.required) {
        return this.$t('validation.required')
      }
    },
    timeUnsuspendError() {
      if (!this.$v.period_run_unsuspend_packages_job.$error) {
        return ''
      }
      if (!this.$v.period_run_unsuspend_packages_job.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.period_run_unsuspend_packages_job.minValue.min) {
        return this.$tc('validation.min_number_one')
      }
    },
  },

  watch: {
    settings(val) {
      this.services.creation_services = val ? val.creation_services : ''
      this.services.creation_services_subject = val ? val.creation_services_subject : ''
      this.services.suspension_services = val ? val.suspension_services : ''
      this.services.suspension_services_subject = val ? val.suspension_services_subject : ''
      this.services.cancellation_services = val ? val.cancellation_services : ''
      this.services.cancellation_services_subject = val ? val.cancellation_services_subject : ''
      this.services.reactivation_services = val ? val.reactivation_services : ''
      this.services.reactivation_services_subject = val ? val.reactivation_services_subject : ''
      this.packages.packages_prefix = val ? val.packages_prefix : ''
      this.time_run_suspension_packages_job = val
        ? val.time_run_suspension_packages_job
        : null
      this.allow_suspension_packages_job = val
        ? val.allow_suspension_packages_job == 1
        : false
      this.period_run_unsuspend_packages_job = val
        ? val.period_run_unsuspend_packages_job
        : 1
      this.allow_unsuspend_packages_job = val
        ? val.allow_unsuspend_packages_job == 1
        : false

      this.email = val.package_bbc_email  
    },
  },

  validations: {
    packages: {
      packages_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
    time_run_suspension_packages_job: {
      required,
    },
    period_run_unsuspend_packages_job: {
      required,
      minValue: minValue(1),
    },    
    email: {
      required,
      email,
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    async setInvoiceSetting() {
      let data = {
        settings: {},
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'PACKAGE') {
        this.packages.packages_prefix =
          this.packages.packages_prefix.toUpperCase()

        return true
      }
    },
    updateServicesSetting() {
      let data = {
        settings: {
          creation_services: this.services.creation_services,
          creation_services_subject: this.services.creation_services_subject,
          suspension_services: this.services.suspension_services,
          suspension_services_subject: this.services.suspension_services_subject,
          cancellation_services: this.services.cancellation_services,
          cancellation_services_subject: this.services.cancellation_services_subject,
          reactivation_services: this.services.reactivation_services,
          reactivation_services_subject: this.services.reactivation_services_subject,
          // Email
          package_bbc_email: this.email,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('corePbx.customization.services_updated')
        )
      }
    },

    async updateInvoiceSetting() {
      this.$v.packages.$touch()

      if (this.$v.packages.$invalid) {
        return false
      }

      let data = {
        settings: {
          packages_prefix: this.packages.packages_prefix,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.packages.package_setting_updated')
        )
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },
    slidePackagesSuspensionDate(val) {
      if (!val) {
        this.time_run_suspension_packages_job = null
      }
    },
    async updateSuspensionScheduling() {
      this.$v.time_run_suspension_packages_job.$touch()
      if (this.$v.time_run_suspension_packages_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_suspension_packages_job: this.allow_suspension_packages_job,
          time_run_suspension_packages_job:
            this.time_run_suspension_packages_job,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    slideUnsuspendPackages(val) {
      if (!val) {
        this.period_run_unsuspend_packages_job = 1
      }
    },
    async updateUnsuspendScheduling() {
      this.$v.period_run_unsuspend_packages_job.$touch()
      if (this.$v.period_run_unsuspend_packages_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_unsuspend_packages_job: this.allow_unsuspend_packages_job ?? 0,
          period_run_unsuspend_packages_job:
            this.period_run_unsuspend_packages_job ?? 1,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
  },
}
</script>