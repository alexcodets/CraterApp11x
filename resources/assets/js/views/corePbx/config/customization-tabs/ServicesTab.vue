<template>
  <div>

    <form action="" class="mt-6" @submit.prevent="updateServicesSetting">

      <!-- Email -->

      <sw-input-group
            :label="$t('corePbx.customization.pbx_service_bbc_email')"
            class="md:col-span- mt-2 mb-2"
            :error="emailError"
          >                  
            <sw-input
              style="max-width: 30%"
              :invalid="$v.email.$error"
              v-model="email"
              type="text"
              name="email"
              @input="$v.email.$touch()"
            />                     
      </sw-input-group>

      <!-- Email -->
      
      <sw-input-group :label="$t('corePbx.customization.services_creation_subject')" >
        <base-custom-input v-model="services.pbx_creation_services_subject" 
          :fields="InvoiceMailFields"  />
      </sw-input-group>

        <sw-input-group
        :label="
          $t('corePbx.customization.services_creation')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.pbx_creation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group :label="$t('corePbx.customization.services_suspension_subject')" >
        <base-custom-input v-model="services.pbx_suspension_services_subject" 
          :fields="InvoiceMailFields"  />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('corePbx.customization.services_suspension')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.pbx_suspension_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group :label="$t('corePbx.customization.services_cancellation_subject')" >
        <base-custom-input v-model="services.pbx_cancellation_services_subject" 
          :fields="InvoiceMailFields"  />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('corePbx.customization.services_cancellation')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.pbx_cancellation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group :label="$t('corePbx.customization.services_reactivation_subject')" >
        <base-custom-input v-model="services.pbx_reactivation_services_subject" 
          :fields="InvoiceMailFields"  />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('corePbx.customization.services_reactivation')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="services.pbx_reactivation_services"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <sw-button
        :loading="isLoadingTemplateEmail"
        :disabled="isLoadingTemplateEmail"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update">
        <save-icon v-if="!isLoadingTemplateEmail" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <form action="" class="mt-6" @submit.prevent="updatePrefixSetting">
      <sw-input-group
        :label="$t('corePbx.customization.services_prefix')"
        :error="ExpensePrefixError">
        <sw-input
          v-model="expenses.pbx_services_prefix"
          :invalid="$v.expenses.pbx_services_prefix.$error"
          style="max-width: 30%"
          @input="$v.expenses.pbx_services_prefix.$touch()"
          @keyup="changeToUppercase('EXPENSE')" />
      </sw-input-group>
      
        <div class="flex mt-3 mb-4">
            <div class="relative w-12">
                <sw-switch
                v-model="expenses.services_prefix_general"
                class="absolute"
                style="top: -20px"/>
            </div>
            <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                     {{ $t('settings.customization.items.apply_general_prefix') }}              </p>
            </div>
        </div>

      <sw-button
        :loading="isLoadingPrefix"
        :disabled="isLoadingPrefix"
        variant="primary"
        type="submit"
        class="mt-4" v-if="permission.update">
        <save-icon v-if="!isLoadingPrefix" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>
    
    <sw-divider class="mt-6 mb-8" />

    <form action="" class="mt-6" @submit.prevent="updateScheduling">

      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('corePbx.customization.task_scheduling') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_renewal_date_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideRenewalDate(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('corePbx.customization.allow_renewal_date_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('corePbx.customization.renewal_date_job_desc') }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="$t('corePbx.customization.time_run_renewal_date_job')"
        :error="timeJobError"
        class="mb-4"
        required
      >
        <base-time-picker
          v-model="time_run_renewal_date_job"
          :calendar-button="true"
          style="max-width: 30%"
          :placeholder="'HH:mm'"
          calendar-button-icon="calendar"
          @input="$v.time_run_renewal_date_job.$touch()"
        />

      </sw-input-group>

      <sw-button
        :loading="isLoadingRenewal"
        :disabled="isLoadingRenewal"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if=permission.update
      >
        <save-icon v-if="!isLoadingRenewal" class="mr-2"/>
        {{ $t('corePbx.customization.save_scheduling') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-4" />
    <!-- Services suspension -->
        <form action="" class="mt-6" @submit.prevent="updateSuspensionScheduling">

            <h3 class="mb-5 text-lg font-medium text-black">
                {{ $t('corePbx.customization.package_suspension') }}
            </h3>

            <div class="flex mb-4">
                <div class="relative w-12">
                    <sw-switch
                        v-model="allow_suspension_pbx_job"
                        class="absolute"
                        style="top: -18px"
                        @change="(option) => slidePackagesSuspensionDate(option)"
                    />
                </div>

                <div class="ml-4">
                    <p class="p-0 mb-1 text-base leading-snug text-black">
                        {{ $t('corePbx.customization.allow_packagesuspension_date_job') }}
                    </p>

                    <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
                        {{ $t('corePbx.customization.packagesuspension_date_job_desc') }}
                    </p>
                </div>
            </div>

            <sw-input-group
                :label="$t('corePbx.customization.time_run_packagesuspension_date_job')"
                :error="timeSuspensionError"
                class="mb-4"
                required
            >
                <base-time-picker
                    v-model="time_run_suspension_pbx_job"
                    :invalid="$v.time_run_suspension_pbx_job.$error"
                    :calendar-button="true"
                    style="max-width: 30%"
                    :placeholder="'HH:mm'"
                    calendar-button-icon="calendar"
                    @input="$v.time_run_suspension_pbx_job.$touch()"
                />

            </sw-input-group>

            <sw-button
                :loading="isLoadingSuspension"
                :disabled="isLoadingSuspension"
                variant="primary"
                type="submit"
                class="mt-4"
                v-if="permission.update"
            >
                <save-icon v-if="!isLoadingSuspension" class="mr-2"/>
                {{ $t('corePbx.customization.save_suspension') }}
            </sw-button>
        </form>

    <sw-divider class="mt-6 mb-8" />

    <form action="" class="mt-6" @submit.prevent="updateUnsuspendScheduling">

      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('corePbx.customization.unsuspend_services') }}
      </h3>

      <div class="flex mb-6">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_unsuspend_pbx_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideUnsuspendPbx(option)"
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
          v-model="period_run_unsuspend_job"
          :invalid="$v.period_run_unsuspend_job.$error"
          style="max-width: 30%"
          @input="$v.period_run_unsuspend_job.$touch()"
          type="number"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingUnsuspend"
        :disabled="isLoadingUnsuspend"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoadingUnsuspend" class="mr-2"/>
        {{ $t('corePbx.customization.save_suspension') }}
      </sw-button>
    </form>

  
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {requiredIf} from "vuelidate/lib/validators";
const { required, maxLength, minValue, alpha, email } = require('vuelidate/lib/validators')

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
      expenseAutogenerate: false,
      services:{
        pbx_creation_services:null,
        pbx_creation_services_subject:null,
        pbx_suspension_services:null,
        pbx_suspension_services_subject:null,
        pbx_cancellation_services:null,
        pbx_cancellation_services_subject:null,
        pbx_reactivation_services:null,
        pbx_reactivation_services_subject:null,
      },
      expenses: {
        pbx_services_prefix: null,
        services_prefix_general: null,
      },
      isLoading: false,
      isLoadingTemplateEmail: false,
      isLoadingPrefix: false,
      isLoadingPrefixService: false,
      isLoadingRenewal: false,
      allow_renewal_date_job: false,
      isLoadingSuspension: false,
      isLoadingUnsuspend: false,
      time_run_renewal_date_job: null,
      allow_suspension_pbx_job:false,
      time_run_suspension_pbx_job:null,
      allow_unsuspend_pbx_job: false,
      period_run_unsuspend_job: 1,
      isLoadingunsuspend: false,
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'service',
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
    ExpensePrefixError() {
      if (!this.$v.expenses.pbx_services_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.pbx_services_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.pbx_services_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.pbx_services_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
    timeJobError() {
      if (!this.$v.time_run_renewal_date_job.$error) {
        return ''
      }
      if (!this.$v.time_run_renewal_date_job.required) {
        return this.$t('validation.required')
      }
    },
    timeSuspensionError() {
            if (!this.$v.time_run_suspension_pbx_job.$error) {
                return ''
            }
            if (!this.$v.time_run_suspension_pbx_job.required) {
                return this.$t('validation.required')
            }
        },
    timeUnsuspendError() {
      if (!this.$v.period_run_unsuspend_job.$error) {
        return ''
      }
      if (!this.$v.period_run_unsuspend_job.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.period_run_unsuspend_job.minValue.min) {
        return this.$tc('validation.min_number_one')
      }
    },
  },

  validations: {
    expenses: {
      pbx_services_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
    time_run_renewal_date_job: {
      required: requiredIf(function () {
        return this.allow_renewal_date_job
      }),
    },
    time_run_suspension_pbx_job: {
      required
        },
    period_run_unsuspend_job: {
      required,
      minValue: minValue(1),
    },
    email: {
        required,
        email,
    },
  },

  watch: {
    settings(val) {
      this.services.pbx_creation_services = val ? val.pbx_creation_services : ''
      this.services.pbx_creation_services_subject = val ? val.pbx_creation_services_subject : ''
      this.services.pbx_suspension_services = val ? val.pbx_suspension_services : ''
      this.services.pbx_suspension_services_subject = val ? val.pbx_suspension_services_subject : ''
      this.services.pbx_cancellation_services = val ? val.pbx_cancellation_services: ''
      this.services.pbx_cancellation_services_subject = val ? val.pbx_cancellation_services_subject: ''
      this.services.pbx_reactivation_services = val ? val.pbx_reactivation_services: ''
      this.services.pbx_reactivation_services_subject = val ? val.pbx_reactivation_services_subject: ''
      this.expenses.pbx_services_prefix = val ? val.pbx_services_prefix : ''
      this.expense_auto_generate = val ? val.expense_auto_generate : ''
      this.time_run_renewal_date_job = val ? val.time_run_renewal_date_job_pbx : null
      this.allow_renewal_date_job = val ? val.allow_renewal_date_job_pbx == 1 : false
      this.time_run_suspension_pbx_job = val ? val.time_run_suspension_pbx_job : null
      this.allow_suspension_pbx_job = val ? val.allow_suspension_pbx_job == 1 : false
      this.period_run_unsuspend_job = val ? val.period_run_unsuspend_job : null
      this.allow_unsuspend_pbx_job = val ? val.allow_unsuspend_pbx_job == 1 : false
      this.email = val.pbx_service_bbc_email
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    changeToUppercase(currentTab) {
      if (currentTab === 'EXPENSE') {
        this.expenses.pbx_services_prefix = this.expenses.pbx_services_prefix.toUpperCase()
        return true
      }
    },

    async setExpenseSetting() {
      let data = {
        settings: {
          expense_auto_generate: this.expenseAutogenerate ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updatePrefixSetting() {
      try {
        if (this.$v.expenses.$invalid) return false

        this.$v.expenses.$touch()
        this.isLoadingPrefix = true
        const data = {
          settings: {
            pbx_services_prefix: this.expenses.pbx_services_prefix,
          },
      }
        await this.updateCompanySettings(data)
        window.toastr['success'](this.$t('general.setting_updated'))

      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.services_update_error')
        )
      } finally {
        this.isLoadingPrefix = false
      }
    },

    async updateServicesSetting(){

      try {
        this.isLoadingTemplateEmail = true
        //const result = this.validateFieldIsEmpty()
        const data = {
          settings: {
            pbx_creation_services: this.services.pbx_creation_services,
            pbx_creation_services_subject: this.services.pbx_creation_services_subject,
            pbx_suspension_services: this.services.pbx_suspension_services,
            pbx_suspension_services_subject: this.services.pbx_suspension_services_subject,
            pbx_cancellation_services: this.services.pbx_cancellation_services,
            pbx_cancellation_services_subject: this.services.pbx_cancellation_services_subject,
            pbx_reactivation_services: this.services.pbx_reactivation_services,          
            pbx_reactivation_services_subject: this.services.pbx_reactivation_services_subject,
            // Email   
            pbx_service_bbc_email: this.email,   
          },
        }

        await this.updateCompanySettings(data)
        window.toastr['success'](
          this.$t('corePbx.customization.packages_prefix_updated')
        )
        /*
        if (!result.status) {
          window.toastr['error'](
            this.$t(result.message)
          )
        } else {
          await this.updateCompanySettings(data)
          window.toastr['success'](
            this.$t('corePbx.customization.services_updated')
          )
        }
        */
      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.services_update_error')
        )
      } finally {
        this.isLoadingTemplateEmail = false
      }
    },
    async updateScheduling(){
      try {
        if (this.$v.time_run_renewal_date_job.$error) return true

        this.$v.time_run_renewal_date_job.$touch()
        this.isLoadingRenewal = true
        const data = {
          settings: {
            allow_renewal_date_job_pbx: this.allow_renewal_date_job,
            time_run_renewal_date_job_pbx: this.time_run_renewal_date_job
          },
        }
        await this.updateCompanySettings(data)
        window.toastr['success'](this.$t('general.setting_updated'))

      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.services_update_error')
        )
      } finally {
        this.isLoadingRenewal = false
      }
    },

    slideRenewalDate(val) {
      if (!val) {
        this.time_run_renewal_date_job = null;
      }
    },
    
    slidePackagesSuspensionDate(val) {
            if (!val) {
                this.time_run_suspension_pbx_job = null;
            }
    },
    async updateSuspensionScheduling(){
      try {
        if (this.$v.time_run_suspension_pbx_job.$error) return true

        this.$v.time_run_suspension_pbx_job.$touch()
        this.isLoadingSuspension = true
        const data = {
          settings: {
            allow_suspension_pbx_job: this.allow_suspension_pbx_job,
            time_run_suspension_pbx_job: this.time_run_suspension_pbx_job
          },
        }
        await this.updateCompanySettings(data)
        window.toastr['success'](this.$t('general.setting_updated'))

      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.services_update_error')
        )
      } finally {
        this.isLoadingSuspension = false
      }
    },

      slideUnsuspendPbx(val) {
      if (!val) {
        this.period_run_unsuspend_job = 1;
      }
    },

    async updateUnsuspendScheduling(){
      try {
        if (this.$v.period_run_unsuspend_job.$error) return true

        this.$v.period_run_unsuspend_job.$touch()
        this.isLoadingUnsuspend = true
        const data = {
          settings: {
            allow_unsuspend_pbx_job: this.allow_unsuspend_pbx_job ?? 0,
            period_run_unsuspend_job: this.period_run_unsuspend_job ?? 1
          },
        }
        await this.updateCompanySettings(data)
        window.toastr['success'](this.$t('general.setting_updated'))

      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.services_update_error')
        )
      } finally {
        this.isLoadingUnsuspend = false
      }
    },
    // funcion para validar que los campos no lleguen vacios o menos de 10 caracteres
    validateFieldIsEmpty() {
      if (this.services.pbx_creation_services.length <= 10 || this.services.pbx_creation_services == null || this.services.pbx_creation_services == undefined) {
        return {status: false, message: 'validation.validation_body_email'}
      }
      if (this.services.pbx_creation_services_subject.length <= 10 || this.services.pbx_creation_services_subject == null || this.services.pbx_creation_services_subject == undefined) {
        return {status: false, message: 'validation.validation_subject'}
      }
      if (this.services.pbx_suspension_services.length <= 10 || this.services.pbx_suspension_services == null || this.services.pbx_suspension_services == undefined) {
        return {status: false, message: 'validation.validation_body_email'}
      } 
      if (this.services.pbx_suspension_services_subject.length <= 10 || this.services.pbx_suspension_services_subject == null || this.services.pbx_suspension_services_subject == undefined) {
        return {status: false, message: 'validation.validation_subject'}
      } 
      if (this.services.pbx_cancellation_services.length <= 10 || this.services.pbx_cancellation_services == null || this.services.pbx_cancellation_services == undefined) {
        return {status: false, message: 'validation.validation_body_email'}
      }
      if (this.services.pbx_cancellation_services_subject.length <= 10 || this.services.pbx_cancellation_services_subject == null || this.services.pbx_cancellation_services_subject == undefined) {
        return {status: false, message: 'validation.validation_subject'}
      }
      if (this.services.pbx_reactivation_services.length <= 10 || this.services.pbx_reactivation_services == null || this.services.pbx_reactivation_services == undefined) {
        return {status: false, message: 'validation.validation_body_email'}
      }
      if (this.services.pbx_reactivation_services_subject.length <= 10 || this.services.pbx_reactivation_services_subject == null || this.services.pbx_reactivation_services_subject == undefined) {
        return {status: false, message: 'validation.validation_subject'}
      }else {
        return {status: true}
      }
    }
  },
}
</script>