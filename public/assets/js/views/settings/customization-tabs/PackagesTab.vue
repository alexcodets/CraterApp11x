<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateServicesSetting">
      <sw-input-group
        :label="$t('corePbx.customization.services_creation')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.creation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('corePbx.customization.services_suspension')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.suspension_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('corePbx.customization.services_cancellation')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.cancellation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('corePbx.customization.services_reactivation')"
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
        class="mt-4"
      >
        <save-icon v-if="!isLoadingunsuspend" class="mr-2" />
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
} = require('vuelidate/lib/validators')
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
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
        suspension_services: null,
        cancellation_services: null,
        reactivation_services: null,
      },
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'service:except:SERVICE_PBX_TENANT_ID,SERVICE_PBX_SERVICES_NUMBER,SERVICE_PBX_PACKAGES_PRICE,PACKAGES_PRICE',
        'invoiceCustom',
        'company',
      ],
    }
  },

  computed: {
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
      this.services.suspension_services = val ? val.suspension_services : ''
      this.services.cancellation_services = val ? val.cancellation_services : ''
      this.services.reactivation_services = val ? val.reactivation_services : ''
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
          suspension_services: this.services.suspension_services,
          cancellation_services: this.services.cancellation_services,
          reactivation_services: this.services.reactivation_services,
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