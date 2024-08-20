<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updatePaymentEmailSetting">
      <sw-input-group
        :label="$t('settings.customization.payments.payment_with_approved_ach')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailPayment.payment_approved_ach"
          :fields="mailFieldsACH"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.payments.ach_payment_declined')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailPayment.payment_ach_declined"
          :fields="mailFieldsACH"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="
          $t('settings.customization.payments.payment_approved_credit_card')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailPayment.payment_approved_credit_card"
          :fields="mailFields"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="
          $t('settings.customization.payments.payment_rejected_credit_card')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailPayment.payment_credit_card_rejected"
          :fields="mailFields"
          class="mt-2"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('settings.customization.payments.payment_card_expiration')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="emailPayment.payment_card_expiration_reminders"
          :fields="mailRemindersFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingPayment"
        :disabled="isLoadingPayment"
        variant="primary"
        type="submit"
        class="my-4"
      >
        <save-icon v-if="!isLoadingPayment" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <form action="" class="mt-6" @submit.prevent="updatePaymentSetting">
      <sw-input-group
        :label="$t('settings.customization.payments.payment_prefix')"
        :error="paymentPrefixError"
      >
        <sw-input
          v-model="payments.payment_prefix"
          :invalid="$v.payments.payment_prefix.$error"
          class="mt-2"
          style="max-width: 30%"
          @input="$v.payments.payment_prefix.$touch()"
          @keyup="changeToUppercase('PAYMENTS')"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.payments.default_payment_email_body')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="payments.payment_mail_body"
          :fields="mailFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.payments.company_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="payments.company_address_format"
          :fields="companyFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.payments.from_customer_address_format')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="payments.from_customer_address_format"
          :fields="customerAddressFields"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="my-4"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <div class="flex">
      <div class="relative w-12">
        <sw-switch
          v-model="paymentAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setPaymentSetting"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.payments.autogenerate_payment_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.payments.payment_setting_description')
          }}
        </p>
      </div>
    </div>
    <sw-divider class="mt-6 mb-4" />
    <!-- Payment reminder -->
    <form action="" class="mt-6" @submit.prevent="updateReminderScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.payments.payment_reminder') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_reminder_payment_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slidePaymentReminderDate(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{
              $t(
                'settings.customization.payments.allow_paymentreminder_date_job'
              )
            }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t(
                'settings.customization.payments.paymentreminder_date_job_desc'
              )
            }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="
          $t(
            'settings.customization.payments.time_run_paymentreminder_date_job'
          )
        "
        :error="timeReminderError"
        class="mb-4"
        required
      >
        <base-time-picker
          v-model="time_run_reminder_payment_job"
          :invalid="$v.time_run_reminder_payment_job.$error"
          :calendar-button="true"
          style="max-width: 30%"
          :placeholder="'HH:mm'"
          calendar-button-icon="calendar"
          @input="$v.time_run_reminder_payment_job.$touch()"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingReminder"
        :disabled="isLoadingReminder"
        variant="primary"
        type="submit"
        class="mt-4"
      >
        <save-icon v-if="!isLoadingReminder" class="mr-2" />
        {{ $t('settings.customization.payments.save_renewal') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-4" />
    <!-- Paint Pending services -->
    <!-- <form action="" class="mt-6" @submit.prevent="updatePendingScheduling">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.payments.payment_paint_pending') }}
      </h3>

      <div class="flex mb-6">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_pending_payment_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slidePendingPayment(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.payments.allow_paint_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t(
                'settings.customization.payments.allow_paint_job_date_job_desc'
              )
            }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="$t('corePbx.customization.period_time_run_job')"
        :error="timePendingError"
        required
      >
        <sw-input
          v-model="period_run_pending_payment_job"
          :invalid="$v.period_run_pending_payment_job.$error"
          style="max-width: 30%"
          @input="$v.period_run_pending_payment_job.$touch()"
          type="number"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingpending"
        :disabled="isLoadingpending"
        variant="primary"
        type="submit"
        class="mt-4"
      >
        <save-icon v-if="!isLoadingpending" class="mr-2" />
        {{ $t('settings.customization.payments.save_paint_pending') }}
      </sw-button>
    </form>
    <sw-divider class="mt-6 mb-4" /> -->
    <!-- Card Expiration Reminders 15th of Month -->
    <form
      action=""
      class="mt-6"
      @submit.prevent="updateCardExpirationReminderScheduling"
    >
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.payments.payment_card_expiration') }}
      </h3>

      <div class="flex mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_cardexpiration_payment_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideCardExpirationDate(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.payments.allow_cardexpiration_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{
              $t('settings.customization.payments.cardexpiration_date_job_desc')
            }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="
          $t('settings.customization.payments.time_run_cardexpiration_date_job')
        "
        :error="timeCardExpirationError"
        class="mb-4"
        required
      >
        <base-time-picker
          v-model="time_run_cardexpiration_payment_job"
          :invalid="$v.time_run_cardexpiration_payment_job.$error"
          :calendar-button="true"
          style="max-width: 30%"
          :placeholder="'HH:mm'"
          calendar-button-icon="calendar"
          @input="$v.time_run_cardexpiration_payment_job.$touch()"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoadingCardExpiration"
        :disabled="isLoadingCardExpiration"
        variant="primary"
        type="submit"
        class="mt-4"
      >
        <save-icon v-if="!isLoadingCardExpiration" class="mr-2" />
        {{ $t('settings.customization.payments.save_cardexpiration') }}
      </sw-button>
    </form>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { requiredIf } from 'vuelidate/lib/validators'
const {
  required,
  maxLength,
  alpha,
  minValue,
} = require('vuelidate/lib/validators')

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
      paymentAutogenerate: false,

      emailPayment: {
        payment_approved_ach: null,
        payment_ach_declined: null,
        payment_approved_credit_card: null,
        payment_credit_card_rejected: null,
        payment_card_expiration_reminders: null,
      },

      payments: {
        payment_prefix: null,
        payment_mail_body: null,
        from_customer_address_format: null,
        company_address_format: null,
      },

      mailFields: [
        'customer',
        'customerCustom',
        'company',
        'payment',
        'paymentA',
        'paymentCustom',
      ],
      mailFieldsACH: [
        'customer',
        'customerCustom',
        'company',
        'payment',
        'paymentCustom',
      ],
      mailRemindersFields: [
        'customer',
        'customerCustom',
        'paymentA',
        'company',
        'paymentCustom',
      ],
      customerAddressFields: [
        'billing',
        'customer',
        'customerCustom',
        'paymentCustom',
      ],
      allow_reminder_payment_job: false,
      time_run_reminder_payment_job: null,
      allow_pending_payment_job: false,
      period_run_pending_payment_job: 1,
      allow_cardexpiration_payment_job: false,
      time_run_cardexpiration_payment_job: null,
      companyFields: ['company', 'paymentCustom'],
      isLoading: false,
      isLoadingpending: false,
      isLoadingPayment: false,
      isLoadingReminder: false,
      isLoadingCardExpiration: false,
    }
  },
  computed: {
    paymentPrefixError() {
      if (!this.$v.payments.payment_prefix.$error) {
        return ''
      }

      if (!this.$v.payments.payment_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.payments.payment_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.payments.payment_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
    timeReminderError() {
      if (!this.$v.time_run_reminder_payment_job.$error) {
        return ''
      }
      if (!this.$v.time_run_reminder_payment_job.required) {
        return this.$t('validation.required')
      }
    },
    timeCardExpirationError() {
      if (!this.$v.time_run_cardexpiration_payment_job.$error) {
        return ''
      }
      if (!this.$v.time_run_cardexpiration_payment_job.required) {
        return this.$t('validation.required')
      }
    },
    timePendingError() {
      if (!this.$v.period_run_pending_payment_job.$error) {
        return ''
      }
      if (!this.$v.period_run_pending_payment_job.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.period_run_pending_payment_job.minValue.min) {
        return this.$tc('validation.min_number_one')
      }
    },
  },

  validations: {
    payments: {
      payment_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
    time_run_reminder_payment_job: {
      required,
    },
    time_run_cardexpiration_payment_job: {
      required,
    },
    period_run_pending_payment_job: {
      required,
      minValue: minValue(1),
    },
  },

  watch: {
    settings(val) {
      this.emailPayment.payment_approved_ach = val
        ? val.payment_approved_ach
        : ''
      this.emailPayment.payment_ach_declined = val
        ? val.payment_ach_declined
        : ''
      this.emailPayment.payment_approved_credit_card = val
        ? val.payment_approved_credit_card
        : ''
      this.emailPayment.payment_credit_card_rejected = val
        ? val.payment_credit_card_rejected
        : ''
      this.emailPayment.payment_card_expiration_reminders = val
        ? val.payment_card_expiration_reminders
        : ''

      this.payments.payment_prefix = val ? val.payment_prefix : ''

      this.payments.payment_mail_body = val ? val.payment_mail_body : ''

      this.payments.company_address_format = val
        ? val.payment_company_address_format
        : ''

      this.payments.from_customer_address_format = val
        ? val.payment_from_customer_address_format
        : ''

      this.payment_auto_generate = val ? val.payment_auto_generate : ''

      if (this.payment_auto_generate === 'YES') {
        this.paymentAutogenerate = true
      } else {
        this.paymentAutogenerate = false
      }
      this.time_run_reminder_payment_job = val
        ? val.time_run_reminder_payment_job
        : null
      this.allow_reminder_payment_job = val
        ? val.allow_reminder_payment_job == 1
        : false
      this.period_run_pending_payment_job = val
        ? val.period_run_pending_payment_job
        : 1
      this.allow_pending_payment_job = val
        ? val.allow_pending_payment_job == 1
        : false
      this.time_run_cardexpiration_payment_job = val
        ? val.time_run_cardexpiration_payment_job
        : null
      this.allow_cardexpiration_payment_job = val
        ? val.allow_cardexpiration_payment_job == 1
        : false
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('company', ['updateCompanySettings']),

    changeToUppercase(currentTab) {
      if (currentTab === 'PAYMENTS') {
        this.payments.payment_prefix =
          this.payments.payment_prefix.toUpperCase()
        return true
      }
    },

    async setPaymentSetting() {
      let data = {
        settings: {
          payment_auto_generate: this.paymentAutogenerate ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updatePaymentSetting() {
      this.$v.payments.$touch()

      if (this.$v.payments.$invalid) {
        return false
      }

      let data = {
        settings: {
          payment_prefix: this.payments.payment_prefix,
          payment_mail_body: this.payments.payment_mail_body,
          payment_company_address_format: this.payments.company_address_format,
          payment_from_customer_address_format:
            this.payments.from_customer_address_format,
        },
      }

      if (this.updateSetting(data, 1)) {
        window.toastr['success'](
          this.$t('settings.customization.payments.payment_setting_updated')
        )
      }
    },

    async updatePaymentEmailSetting() {
      let data = {
        settings: {
          payment_approved_ach: this.emailPayment.payment_approved_ach,
          payment_ach_declined: this.emailPayment.payment_ach_declined,
          payment_approved_credit_card:
            this.emailPayment.payment_approved_credit_card,
          payment_credit_card_rejected:
            this.emailPayment.payment_credit_card_rejected,
          payment_card_expiration_reminders:
            this.emailPayment.payment_card_expiration_reminders,
        },
      }

      if (this.updateSetting(data, 2)) {
        window.toastr['success'](
          this.$t('corePbx.customization.services_updated')
        )
      }
    },

    async updateSetting(data, band) {
      if (band == 1) {
        this.isLoading = true
      } else {
        this.isLoadingPayment = true
      }
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        if (band == 1) {
          this.isLoading = false
        } else {
          this.isLoadingPayment = false
        }
        return true
      }

      return false
    },
    slidePaymentReminderDate(val) {
      if (!val) {
        this.time_run_reminder_payment_job = null
      }
    },
    async updateReminderScheduling() {
      this.$v.time_run_reminder_payment_job.$touch()
      if (this.$v.time_run_reminder_payment_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_reminder_payment_job: this.allow_reminder_payment_job,
          time_run_reminder_payment_job: this.time_run_reminder_payment_job,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    slidePendingPayment(val) {
      if (!val) {
        this.period_run_pending_payment_job = 1
      }
    },
    async updatePendingScheduling() {
      this.$v.period_run_pending_payment_job.$touch()
      if (this.$v.period_run_pending_payment_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_pending_payment_job: this.allow_pending_payment_job ?? 0,
          period_run_pending_payment_job:
            this.period_run_pending_payment_job ?? 1,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    slideCardExpirationDate(val) {
      if (!val) {
        this.time_run_cardexpiration_payment_job = null
      }
    },
    async updateCardExpirationReminderScheduling() {
      this.$v.time_run_cardexpiration_payment_job.$touch()
      if (this.$v.time_run_cardexpiration_payment_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_cardexpiration_payment_job:
            this.allow_cardexpiration_payment_job,
          time_run_cardexpiration_payment_job:
            this.time_run_cardexpiration_payment_job,
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
