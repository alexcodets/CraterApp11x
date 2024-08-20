<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateCustomerSetting">
      <!--  -->

      <!-- Email -->

      <sw-input-group
        :label="$t('settings.customization.customer_bcc_email')"
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

      <sw-divider class="mt-6 mb-8" />

      <sw-input-group
        :label="$t('customers.customer_type_default')"
        class="md:col-span-3"
      >
        <sw-select
          v-model="customer_type_selected"
          :options="types"
          :searchable="true"
          :show-labels="false"
          class="mt-2"
          style="max-width: 30%"
          label="name"
          @select="CutomerTypeSelected"
        >
        </sw-select>
      </sw-input-group>
      <sw-divider class="mt-6 mb-8" />

      <h1>{{ $t('settings.customization.customer_customer_creation') }}</h1>
      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_customer_creation_subject"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>

      <!-- Email -->

      <sw-input-group
        :label="$t('settings.customization.customer_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_customer_creation"
          :fields="InvoiceMailFieldsCreation"
          class="mt-2"
        />
      </sw-input-group>
      <!--  -->
      <sw-divider class="mt-6 mb-8" />
      <h1>{{ $t('settings.customization.customer_account_registration') }}</h1>
      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_account_registration_subject"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.customer_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_account_registration"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <!--  -->
      <!-- <sw-input-group
                :label="
                $t('settings.customization.customer_password_creation')
                "
                class="mt-6 mb-4"
            >
            
                <base-custom-input
                v-model="emailCustomer.customer_password_creation"
                :fields="InvoiceMailFields"
                class="mt-2"
                />
            </sw-input-group> -->
      <sw-divider class="mt-6 mb-8" />
      <h1>{{ $t('settings.customization.customer_password_reset') }}</h1>
      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_password_reset_subject"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.customer_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_password_reset"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <!--  -->
      <sw-divider class="mt-6 mb-8" />
      <h1>{{ $t('settings.customization.customer_email_verification') }}</h1>
      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_email_verification_subject"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.customer_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_email_verification"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <!--  -->
      <sw-divider class="mt-6 mb-8" />
      <h1>{{ $t('settings.customization.customer_forgetting_username') }}</h1>
      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_forgetting_username_subject"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.customer_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_forgetting_username"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>
      <!--  -->
      <sw-divider class="mt-6 mb-8" />
      <h1>{{ $t('settings.customization.customer_email_notification') }}</h1>
      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_email_notification_subject"
          :fields="InvoiceMailFields"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.customer_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_email_notification"
          :fields="InvoiceMailFieldsCreation"
          class="mt-2"
        />
      </sw-input-group>

      <!-- lead email -->
      <sw-divider class="mt-6 mb-8" />
      <h1>{{ $t('settings.customization.customer_lead_notification') }}</h1>

      <sw-input-group
        class="mt-2"
        :label="$t('settings.customization.customer_lead_subject')"
      >
        <base-custom-input
          v-model="emailCustomer.customer_lead_notification_subject"
          :fields="LeadMailFields"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.customer_lead_body')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailCustomer.customer_lead_notification_body"
          :fields="LeadMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingCustomer"
        :disabled="isLoadingCustomer"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoadingCustomer" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>

      <sw-divider class="mt-6 mb-4" />
    </form>

    <form action="" class="mt-6" @submit.prevent="updatePrefix">
      <sw-input-group :label="$t('settings.customization.customer_prefix')">
        <sw-input v-model="customers.customers_prefix" style="max-width: 30%" />
      </sw-input-group>
      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="customers.customers_prefix_general"
            class="absolute"
            style="top: -20px"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.apply_general_prefix') }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="$t('settings.customization.service_prefix')"
        :error="servicePrefixError"
      >
        <sw-input
          v-model="customers.service_prefix"
          :invalid="$v.customers.service_prefix.$error"
          style="max-width: 30%"
          @input="$v.customers.service_prefix.$touch()"
          @keyup="changeToUppercase()"
        />
      </sw-input-group>

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="customers.general_service_prefix"
            class="absolute"
            style="top: -20px"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.general_service_prefix') }}
          </p>
        </div>
      </div>

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

      <sw-divider class="mt-6 mb-4" />
    </form>

    <form action="" class="mt-6" @submit.prevent="updateScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.customer.services_renewal') }}
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
            {{ $t('settings.customization.customer.allow_renewal_date_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.customization.customer.renewal_date_job_desc') }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="$t('settings.customization.customer.time_run_renewal_date_job')"
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
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.customer.save_renewal') }}
      </sw-button>
      <sw-divider class="mt-6 mb-4" />
    </form>
    <!-- Customer Autodebit -->
    <form action="" class="mt-6" @submit.prevent="updateAutoDebitScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.customer.customer_autodebit') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_autodebit_customer_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideAutoDebitDate(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.allow_autodebit_date_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.customization.customer.autodebit_date_job_desc') }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="
          $t('settings.customization.customer.time_run_autodebit_date_job')
        "
        :error="timeAutoDebitJobError"
        class="mb-4"
        required
      >
        <base-time-picker
          v-model="time_run_autodebit_customer_job"
          :invalid="$v.time_run_autodebit_customer_job.$error"
          :calendar-button="true"
          style="max-width: 30%"
          :placeholder="'HH:mm'"
          calendar-button-icon="calendar"
          @input="$v.time_run_autodebit_customer_job.$touch()"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingAutoDebit"
        :disabled="isLoadingAutoDebit"
        variant="primary"
        type="submit"
        class="mt-4 margin-buttonl"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoadingAutoDebit" class="mr-2" />
        {{ $t('settings.customization.customer.auto_debit') }}
      </sw-button>
    </form>

    <!-- Customer make paymente  -->
    <form action="" class="mt-6" @submit.prevent="updatePaymentCustomerOption">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.customer.customer_payment_option') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_make_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableMakePayment(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_make_payment') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.customization.customer.enable_make_payment_desc') }}
          </p>
        </div>
      </div>

      <!-- Customer add credit  -->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_credit_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableAddCreditPayment(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_addcredit_payment') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t(
                'settings.customization.customer.enable_addcredit_payment_desc'
              )
            }}
          </p>
        </div>
      </div>

      <!-- Customer invoices -->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_invoice_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableInvoiceCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_invoice_customer') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.customer.enable_invoice_customer_desc')
            }}
          </p>
        </div>
      </div>

      <!-- Customer quotes -->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_quotes_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableQuotesCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_quotes_customer') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.customer.enable_quotes_customer_desc')
            }}
          </p>
        </div>
      </div>

      <!-- Customer PAyment -->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_payment_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnablePaymentCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_payment_customer') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.customer.enable_payment_customer_desc')
            }}
          </p>
        </div>
      </div>

      <!-- Customer report -->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_report_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableReportCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_report_customer') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.customer.enable_report_customer_desc')
            }}
          </p>
        </div>
      </div>

      <!-- Customer service -->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_service_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableServiceCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_service_customer') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.customer.enable_service_customer_desc')
            }}
          </p>
        </div>
      </div>

      <!-- Customer ticket-->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_tickets_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnableTicketCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.customer.enable_tickets_customer') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.customer.enable_tickets_customer_desc')
            }}
          </p>
        </div>
      </div>

      <!-- Customer Payment account-->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_paymentaccount_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnablePaymentAccountCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{
              $t(
                'settings.customization.customer.enable_paymentaccount_customer'
              )
            }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t(
                'settings.customization.customer.enable_paymentaccount_customer_desc'
              )
            }}
          </p>
        </div>
      </div>

      <!-- Customer pbx services-->
      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="enable_pbxservice_customer"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideEnablePbxServiceCustomer(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{
              $t('settings.customization.customer.enable_pbxservice_customer')
            }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t(
                'settings.customization.customer.enable_pbxservice_customer_desc'
              )
            }}
          </p>
        </div>
      </div>

      <sw-button
        :loading="isLoadingAutoDebit"
        :disabled="isLoadingAutoDebit"
        variant="primary"
        type="submit"
        class="mt-4 margin-buttonl"
        v-if="permission.update"
      >
        <save-icon v-if="!isLoadingAutoDebit" class="mr-2" />
        {{ $t('settings.customization.save') }}
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
  email,
} = require('vuelidate/lib/validators')
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
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
      customerAutogenerate: false,
      emailCustomer: {
        customer_customer_creation: null,
        customer_customer_creation_subject: null,
        customer_account_registration: null,
        customer_account_registration_subject: null,
        /* customer_password_creation:null, */
        customer_password_reset: null,
        customer_password_reset_subject: null,
        customer_email_verification: null,
        customer_email_verification_subject: null,
        customer_forgetting_username: null,
        customer_forgetting_username_subject: null,
        customer_email_notification: null,
        customer_email_notification_subject: null,

        customer_lead_notification_body: null,
        customer_lead_notification_subject: null,
      },
      customers: {
        customers_prefix: null,
        customers_prefix_general: null,
        service_prefix: '',
        general_service_prefix: false,
      },
      InvoiceMailFields: ['customerE', 'company'],
      InvoiceMailFieldsCreation: ['customerC', 'company'],
      LeadMailFields: ['customer', 'customerCustom', 'company'],
      allow_renewal_date_job: false,
      allow_autodebit_customer_job: false,
      time_run_renewal_date_job: null,
      time_run_autodebit_customer_job: null,
      isLoading: false,
      isLoadingCustomer: false,
      isLoadingAutoDebit: false,
      enable_make_customer: false,
      enable_credit_customer: false,
      enable_invoice_customer: false,
      enable_quotes_customer: false,
      enable_payment_customer: false,
      enable_report_customer: false,
      enable_service_customer: false,
      enable_tickets_customer: false,
      enable_paymentaccount_customer: false,
      enable_pbxservice_customer: false,
      email: null,
      types: [
        // { name: 'None', value: 'N' },
        { name: 'Business', value: 'B' },
        { name: 'Residential', value: 'R' },
      ],
      customer_type_selected: { name: 'Business', value: 'B' },
    }
  },
  mounted() {
    this.getFirstUserPrefix()
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
    customerPrefixError() {
      if (!this.$v.customers.customers_prefix.$error) {
        return ''
      }
      if (!this.$v.customers.customers_prefix.required) {
        return this.$t('validation.required')
      }
    },
    servicePrefixError() {
      if (!this.$v.customers.service_prefix.$error) {
        return ''
      }
      if (!this.$v.customers.service_prefix.required) {
        return this.$t('validation.required')
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
    timeAutoDebitJobError() {
      if (!this.$v.time_run_autodebit_customer_job.$error) {
        return ''
      }
      if (!this.$v.time_run_autodebit_customer_job.required) {
        return this.$t('validation.required')
      }
    },
  },
  watch: {
    settings(val) {
      // console.log(val)
      this.customers.service_prefix = val ? val.service_prefix : ''
      this.customers.customers_prefix = val ? val.customer_prefix : ''
      this.time_run_renewal_date_job = val
        ? val.time_run_renewal_date_job
        : null
      this.time_run_autodebit_customer_job = val
        ? val.time_run_autodebit_customer_job
        : null
      this.allow_renewal_date_job = val
        ? val.allow_renewal_date_job == 1
        : false
      this.allow_autodebit_customer_job = val
        ? val.allow_autodebit_customer_job == 1
        : false
      this.emailCustomer.customer_account_registration = val
        ? val.customer_account_registration
        : ''
      this.emailCustomer.customer_account_registration_subject = val
        ? val.customer_account_registration_subject
        : ''
      this.emailCustomer.customer_customer_creation = val
        ? val.customer_customer_creation
        : ''
      this.emailCustomer.customer_customer_creation_subject = val
        ? val.customer_customer_creation_subject
        : ''
      /* this.emailCustomer.customer_password_creation = val ? val.customer_password_creation : '' */
      this.emailCustomer.customer_password_reset = val
        ? val.customer_password_reset
        : ''
      this.emailCustomer.customer_password_reset_subject = val
        ? val.customer_password_reset_subject
        : ''
      this.emailCustomer.customer_email_verification = val
        ? val.customer_email_verification
        : ''
      this.emailCustomer.customer_email_verification_subject = val
        ? val.customer_email_verification_subject
        : ''
      this.emailCustomer.customer_forgetting_username = val
        ? val.customer_forgetting_username
        : ''
      this.emailCustomer.customer_forgetting_username_subject = val
        ? val.customer_forgetting_username_subject
        : ''
      this.emailCustomer.customer_email_notification = val
        ? val.customer_email_notification
        : ''
      this.emailCustomer.customer_email_notification_subject = val
        ? val.customer_email_notification_subject
        : ''
      this.email = val.customer_bbc_email

      this.emailCustomer.customer_lead_notification_body = val
        ? val.customer_lead_notification_body
        : ''
      this.emailCustomer.customer_lead_notification_subject = val
        ? val.customer_lead_notification_subject
        : ''

      this.enable_make_customer =
        val &&
        (val.enable_make_customer === '1' || val.enable_make_customer === 1)
          ? true
          : false
      this.enable_credit_customer =
        val &&
        (val.enable_credit_customer === '1' || val.enable_credit_customer === 1)
          ? true
          : false

      this.enable_invoice_customer =
        val &&
        (val.enable_invoice_customer === '1' ||
          val.enable_invoice_customer === 1)
          ? true
          : false

      this.enable_quotes_customer =
        val &&
        (val.enable_quotes_customer === '1' || val.enable_quotes_customer === 1)
          ? true
          : false

      this.enable_payment_customer =
        val &&
        (val.enable_payment_customer === '1' ||
          val.enable_payment_customer === 1)
          ? true
          : false

      this.enable_report_customer =
        val &&
        (val.enable_report_customer === '1' || val.enable_report_customer === 1)
          ? true
          : false

      this.enable_service_customer =
        val &&
        (val.enable_service_customer === '1' ||
          val.enable_service_customer === 1)
          ? true
          : false

      this.enable_tickets_customer =
        val &&
        (val.enable_tickets_customer === '1' ||
          val.enable_tickets_customer === 1)
          ? true
          : false

      this.enable_paymentaccount_customer =
        val &&
        (val.enable_paymentaccount_customer === '1' ||
          val.enable_paymentaccount_customer === 1)
          ? true
          : false

      this.enable_pbxservice_customer =
        val &&
        (val.enable_pbxservice_customer === '1' ||
          val.enable_pbxservice_customer === 1)
          ? true
          : false

      if (val.customer_type_selected) {
        this.customer_type_selected = this.types.find(
          (item) => item.value === val.customer_type_selected
        )
      }
    },
  },
  validations: {
    customers: {
      customers_prefix: {
        required,
        alpha,
      },
      service_prefix: {
        required,
        alpha,
      },
    },
    time_run_renewal_date_job: {
      required: requiredIf(function () {
        return this.allow_renewal_date_job
      }),
    },
    time_run_autodebit_customer_job: {
      required,
    },
    email: {
      required,
      email,
    },
  },
  methods: {
    ...mapActions('customer', ['setPrefix', 'fetchPrefix']),
    ...mapActions('company', ['updateCompanySettings']),

    async updateCustomerSetting() {
      let data = {
        settings: {
          customer_account_registration:
            this.emailCustomer.customer_account_registration,
          customer_account_registration_subject:
            this.emailCustomer.customer_account_registration_subject,
          customer_customer_creation:
            this.emailCustomer.customer_customer_creation,
          customer_customer_creation_subject:
            this.emailCustomer.customer_customer_creation_subject,
          /* customer_password_creation: this.emailCustomer.customer_password_creation, */
          customer_password_reset: this.emailCustomer.customer_password_reset,
          customer_password_reset_subject:
            this.emailCustomer.customer_password_reset_subject,
          customer_email_verification:
            this.emailCustomer.customer_email_verification,
          customer_email_verification_subject:
            this.emailCustomer.customer_email_verification_subject,
          customer_forgetting_username:
            this.emailCustomer.customer_forgetting_username,
          customer_forgetting_username_subject:
            this.emailCustomer.customer_forgetting_username_subject,
          customer_email_notification:
            this.emailCustomer.customer_email_notification,
          customer_email_notification_subject:
            this.emailCustomer.customer_email_notification_subject,
          // Email
          customer_bbc_email: this.email,
          customer_lead_notification_subject:
            this.emailCustomer.customer_lead_notification_subject,
          customer_lead_notification_body:
            this.emailCustomer.customer_lead_notification_body,
          customer_type_selected: this.customer_type_selected.value,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('corePbx.customization.services_updated')
        )
      }
    },

    async updateSetting(data) {
      this.isLoadingCustomer = true
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        this.isLoadingCustomer = false
        return true
      }

      return false
    },

    async getFirstUserPrefix() {
      this.isLoading = true
      let res = await this.fetchPrefix({})
      if (res.data.success) {
        this.isLoading = false

        this.customers.customers_prefix = res.data.customcode
      } else {
        this.isLoading = false
      }
    },

    async updatePrefix() {
      this.isLoading = true
      let res = await this.setPrefix(this.customers)
      if (res.data.success) {
        this.isLoading = false
        window.toastr['success'](
          this.$t('settings.customization.customer.customer_setting_updated')
        )
        this.getFirstUserPrefix()
        return true
      } else {
        this.isLoading = false
        return true
      }
      return false
    },

    changeToUppercase() {
      this.customers.service_prefix =
        this.customers.service_prefix.toUpperCase()
      return true
    },

    slideRenewalDate(val) {
      if (!val) {
        this.time_run_renewal_date_job = null
      }
    },

    async updateScheduling() {
      this.$v.time_run_renewal_date_job.$touch()
      if (this.$v.time_run_renewal_date_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_renewal_date_job: this.allow_renewal_date_job,
          time_run_renewal_date_job: this.time_run_renewal_date_job,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    slideAutoDebitDate(val) {
      if (!val) {
        this.time_run_autodebit_customer_job = null
      }
    },
    slideEnableMakePayment(val) {
      if (!val) {
        this.enable_make_customer = false
      }
    },

    slideEnableAddCreditPayment(val) {
      if (!val) {
        this.enable_credit_customer = false
      }
    },

    slideEnableInvoiceCustomer(val) {
      if (!val) {
        this.enable_invoice_customer = false
      }
    },
    slideEnableQuotesCustomer(val) {
      if (!val) {
        this.enable_quotes_customer = false
      }
    },
    slideEnablePaymentCustomer(val) {
      if (!val) {
        this.enable_payment_customer = false
      }
    },
    slideEnableReportCustomer(val) {
      if (!val) {
        this.enable_report_customer = false
      }
    },
    slideEnableServiceCustomer(val) {
      if (!val) {
        this.enable_service_customer = false
      }
    },
    slideEnableTicketCustomer(val) {
      if (!val) {
        this.enable_tickets_customer = false
      }
    },
    slideEnablePaymentAccountCustomer(val) {
      if (!val) {
        this.enable_paymentaccount_customer = false
      }
    },
    slideEnablePbxServiceCustomer(val) {
      if (!val) {
        this.enable_pbxservice_customer = false
      }
    },
    async updateAutoDebitScheduling() {
      this.$v.time_run_autodebit_customer_job.$touch()
      if (this.$v.time_run_autodebit_customer_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_autodebit_customer_job: this.allow_autodebit_customer_job,
          time_run_autodebit_customer_job: this.time_run_autodebit_customer_job,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updatePaymentCustomerOption() {
      // Asegúrate de que las variables no sean null, de lo contrario, asigna false
      this.enable_make_customer =
        this.enable_make_customer !== null ? this.enable_make_customer : false

      this.enable_credit_customer =
        this.enable_credit_customer !== null
          ? this.enable_credit_customer
          : false

      this.enable_invoice_customer =
        this.enable_invoice_customer !== null
          ? this.enable_invoice_customer
          : false

      this.enable_quotes_customer =
        this.enable_quotes_customer !== null
          ? this.enable_quotes_customer
          : false

      this.enable_payment_customer =
        this.enable_payment_customer !== null
          ? this.enable_payment_customer
          : false

      this.enable_report_customer =
        this.enable_report_customer !== null
          ? this.enable_report_customer
          : false

      this.enable_service_customer =
        this.enable_service_customer !== null
          ? this.enable_service_customer
          : false

      this.enable_tickets_customer =
        this.enable_tickets_customer !== null
          ? this.enable_tickets_customer
          : false

      this.enable_paymentaccount_customer =
        this.enable_paymentaccount_customer !== null
          ? this.enable_paymentaccount_customer
          : false

      this.enable_pbxservice_customer =
        this.enable_pbxservice_customer !== null
          ? this.enable_pbxservice_customer
          : false

      let data = {
        settings: {
          enable_make_customer: this.enable_make_customer,
          enable_credit_customer: this.enable_credit_customer,
          enable_invoice_customer: this.enable_invoice_customer,
          enable_quotes_customer: this.enable_quotes_customer,
          enable_payment_customer: this.enable_payment_customer,
          enable_report_customer: this.enable_report_customer,
          enable_service_customer: this.enable_service_customer,
          enable_tickets_customer: this.enable_tickets_customer,
          enable_paymentaccount_customer: this.enable_paymentaccount_customer,
          enable_pbxservice_customer: this.enable_pbxservice_customer,
        },
      }

      //console.log(data)

      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
  },
}
</script>