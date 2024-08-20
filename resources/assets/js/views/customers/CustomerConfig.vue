<template>
  <base-page class="bg-white">
    <form @submit.prevent="submitConfigData">
      <sw-page-header :title="$t('customers.customer_config')">
        <template slot="actions">
          <sw-button
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/view`"
            variant="primary-outline"
            class="mr-3 text-sm hidden sm:flex"
          >
            {{ $t('general.back') }}
          </sw-button>
          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="hidden sm:flex"
          >
            <save-icon class="mr-2 -ml-1" />
            {{ $t('general.save') }}
          </sw-button>
        </template>
      </sw-page-header>

      <!-- Basic info -->
      <customer-info />

      <sw-divider class="my-6" />

      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('customers.invoice_charge_options') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6 sm:grid-cols-1"
        >
          <sw-input-group
            :label="$t('customers.invoice_days_before_renewal')"
            class="md:col-span-3"
            :error="daysBeforeRenewal"
          >
            <div class="flex" role="group">
              <sw-input
                v-model="formData.invoice_days_before_renewal"
                :invalid="$v.formData.invoice_days_before_renewal.$error"
                type="number"
                @input="$v.formData.invoice_days_before_renewal.$touch()"
              />
              
            </div>
          </sw-input-group>

          <sw-input-group
            :label="$t('customers.auto_debit_days_before_due_date')"
            class="md:col-span-3"
            :error="daysBeforeDue"
          >
            <div class="flex" role="group">
              <sw-input
                v-model="formData.auto_debit_days_before_due"
                :invalid="$v.formData.auto_debit_days_before_due.$error"
                type="number"
                @input="$v.formData.auto_debit_days_before_due.$touch()"
              />
             
            </div>
          </sw-input-group>

          <sw-input-group
            :label="$t('customers.suspend_services_days_after_due')"
            class="md:col-span-3"
            :error="suspendServiceDaysAfterDue"
          >
            <div class="flex" role="group">
              <sw-input
                v-model="formData.suspend_services_days_after_due"
                :invalid="$v.formData.suspend_services_days_after_due.$error"
                type="number"
                @input="$v.formData.suspend_services_days_after_due.$touch()"
              />
             
            </div>
          </sw-input-group>

          <sw-input-group
            :label="$t('customers.auto_debit_attempts')"
            class="md:col-span-3"
            :error="autoDebitAttemtps"
          >
            <div class="flex" role="group">
              <sw-input
                v-model="formData.auto_debit_attempts"
                :invalid="$v.formData.auto_debit_attempts.$error"
                type="number"
                @input="$v.formData.auto_debit_attempts.$touch()"
              />
            </div>
          </sw-input-group>

          <div class="flex md:col-span-3 my-4">
            <div class="relative w-12">
              <sw-checkbox
                v-model="formData.enable_auto_debit"
                class="absolute"
                @change=""
              />
            </div>

            <div class="ml-4">
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                {{ $t('customers.enable_auto_debit') }}
              </p>
            </div>
          </div>

          <div class="flex md:col-span-3 my-4">
            <div class="relative w-12">
              <sw-checkbox
                v-model="formData.invoice_suspended_services"
                class="absolute"
                @change=""
              />
            </div>

            <div class="ml-4">
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                {{ $t('customers.invoice_suspended_services') }}
              </p>
            </div>
          </div>

          <div class="flex md:col-span-3 my-4">
            <div class="relative w-12">
              <sw-checkbox
                v-model="formData.cancel_services"
                class="absolute"
                @change=""
              />
            </div>

            <div class="ml-4">
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                {{ $t('customers.cancel_services') }}
              </p>
            </div>
          </div>

          <div class="flex md:col-span-3 my-4">
            <div class="relative w-12">
              <sw-checkbox
                v-model="formData.auto_apply_credits"
                class="absolute"
                @change=""
              />
            </div>

            <div class="ml-4">
              <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                {{ $t('customers.auto_apply_credits') }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <sw-button
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/view`"
        variant="primary-outline"
        class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
      >
        {{ $t('general.back') }}
      </sw-button>
      <sw-button
        :loading="isLoading"
        variant="primary"
        type="submit"
        class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
      >
        <save-icon class="mr-2 -ml-1" />
        {{ $t('general.save') }}
      </sw-button>
    </form>
  </base-page>
</template>

<script>
import CustomerInfo from './partials/CustomerInfo'
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minValue,
  between,
  maxValue,
} = require('vuelidate/lib/validators')

export default {
  components: {
    CustomerInfo,
  },
  data() {
    return {
      isLoading: false,
      formData: {
        invoice_days_before_renewal: 0,
        auto_debit_days_before_due: 0,
        suspend_services_days_after_due: 0,
        auto_debit_attempts: 0,
        cancel_service_changes_days: 0,
        apply_invoice_late_fees: 0,
        enable_auto_debit: false,
        set_invoice_method: false,
        invoice_suspended_services: false,
        invoice_service_together: false,
        display_range_date: false,
        cancel_services: false,
        synchronize_addons: false,
        client_create_addons: false,
        client_change_service_term: false,
        client_change_service_package: false,
        client_prorate_credits: false,
        auto_apply_credits: false,
        auto_paid_pending_services: false,
        void_invoice_canceled_service: false,
        void_invoice_canceled_service_days: 0,
        show_client_tax_id: false,
        queue_service_changes: false,
        send_cancellation_notice: false,
        send_payment_notices: false,
        notice_1: 0,
        notice_1_type: null,
        notice_2: 0,
        notice_2_type: null,
        notice_3: 0,
        notice_3_type: null,
        auto_debit_pending_notice: 0,
      },
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),

    daysBeforeRenewal() {
      if (!this.$v.formData.invoice_days_before_renewal.$error) {
        return ''
      }
      if (!this.$v.formData.invoice_days_before_renewal.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.invoice_days_before_renewal.minValue) {
        return this.$tc('validation.min_number')
      }
      if (!this.$v.formData.invoice_days_before_renewal.maxValue) {
        return this.$tc(
          'validation.max_number',
          this.$v.formData.invoice_days_before_renewal.$params.maxValue.max,
          {
            count:
              this.$v.formData.invoice_days_before_renewal.$params.maxValue.max,
          }
        )
      }
    },
    daysBeforeDue() {
      if (!this.$v.formData.auto_debit_days_before_due.$error) {
        return ''
      }
      if (!this.$v.formData.auto_debit_days_before_due.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.auto_debit_days_before_due.minValue) {
        return this.$tc('validation.min_number')
      }
      if (!this.$v.formData.auto_debit_days_before_due.maxValue) {
        return this.$tc(
          'validation.max_number',
          this.$v.formData.auto_debit_days_before_due.$params.maxValue.max,
          {
            count:
              this.$v.formData.auto_debit_days_before_due.$params.maxValue.max,
          }
        )
      }
    },
    suspendServiceDaysAfterDue() {
      if (!this.$v.formData.suspend_services_days_after_due.$error) {
        return ''
      }
      if (!this.$v.formData.suspend_services_days_after_due.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.suspend_services_days_after_due.minValue) {
        return this.$tc('validation.min_number')
      }
      if (!this.$v.formData.suspend_services_days_after_due.maxValue) {
        return this.$tc(
          'validation.max_number',
          this.$v.formData.suspend_services_days_after_due.$params.maxValue.max,
          {
            count:
              this.$v.formData.suspend_services_days_after_due.$params.maxValue
                .max,
          }
        )
      }
    },
    autoDebitAttemtps() {
      if (!this.$v.formData.auto_debit_attempts.$error) {
        return ''
      }
      if (!this.$v.formData.auto_debit_attempts.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.auto_debit_attempts.minValue) {
        return this.$tc('validation.min_number')
      }
      if (!this.$v.formData.auto_debit_attempts.maxValue) {
        return this.$tc(
          'validation.max_number',
          this.$v.formData.auto_debit_attempts.$params.maxValue.max,
          { count: this.$v.formData.auto_debit_attempts.$params.maxValue.max }
        )
      }
    },
  },
  validations() {
    return {
      formData: {
        invoice_days_before_renewal: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(30),
        },
        auto_debit_days_before_due: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(6),
        },
        suspend_services_days_after_due: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(90),
        },
        auto_debit_attempts: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(5),
        },
        cancel_service_changes_days: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(300),
        },
        apply_invoice_late_fees: {
          required,
          minValue: minValue(0),
          maxValue: maxValue(300),
        },
      },
    }
  },
  created() {
    this.loadData()
  },
  methods: {
    ...mapActions('customer', ['fetchViewCustomer', 'setConfig', 'getConfig']),

    async loadData() {
      this.fetchViewCustomer({ id: this.$route.params.id })
      let response = await this.getConfig(this.$route.params.id)
      if (response.data.config) {
        this.formData = response.data.config
        this.formData.enable_auto_debit = this.formData.enable_auto_debit === 1
        this.formData.set_invoice_method =
          this.formData.set_invoice_method === 1
        this.formData.invoice_suspended_services =
          this.formData.invoice_suspended_services === 1
        this.formData.invoice_service_together =
          this.formData.invoice_service_together === 1
        this.formData.display_range_date =
          this.formData.display_range_date === 1
        this.formData.cancel_services = this.formData.cancel_services === 1
        this.formData.synchronize_addons =
          this.formData.synchronize_addons === 1
        this.formData.client_create_addons =
          this.formData.client_create_addons === 1
        this.formData.client_change_service_term =
          this.formData.client_change_service_term === 1
        this.formData.client_change_service_package =
          this.formData.client_change_service_package === 1
        this.formData.client_prorate_credits =
          this.formData.client_prorate_credits === 1
        this.formData.auto_apply_credits =
          this.formData.auto_apply_credits === 1
        this.formData.auto_paid_pending_services =
          this.formData.auto_paid_pending_services === 1
        this.formData.void_invoice_canceled_service =
          this.formData.void_invoice_canceled_service === 1
        this.formData.show_client_tax_id =
          this.formData.show_client_tax_id === 1
        this.formData.queue_service_changes =
          this.formData.queue_service_changes === 1
        this.formData.send_cancellation_notice =
          this.formData.send_cancellation_notice === 1
        this.formData.send_payment_notices =
          this.formData.send_payment_notices === 1
      }
    },

    async submitConfigData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        customer_id: this.$route.params.id,
        ...this.formData,
      }

      let response = await this.setConfig(data)
      this.isLoading = false

      if (response.data.success) {
        window.toastr['success'](this.$t('customers.config_saved'))
        this.$router.push('/admin/customers/' + this.$route.params.id + '/view')
        return true
      }

      window.toastr['error'](response.data.message)
      return true
    },
  },
}
</script>

<style scoped>
</style>