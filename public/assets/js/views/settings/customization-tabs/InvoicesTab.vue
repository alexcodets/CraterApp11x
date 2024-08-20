<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateInvoiceSetting">
      <sw-input-group
        :label="$t('settings.customization.invoices.invoice_prefix')"
        :error="invoicePrefixError"
      >
        <sw-input
          v-model="invoices.invoice_prefix"
          :invalid="$v.invoices.invoice_prefix.$error"
          style="max-width: 30%"
          @input="$v.invoices.invoice_prefix.$touch()"
          @keyup="changeToUppercase('INVOICES')"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.invoices.default_invoice_email_body')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.invoice_mail_body"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.company_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.company_address_format"
          :fields="companyFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.shipping_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.shipping_address_format"
          :fields="shippingFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.billing_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.billing_address_format"
          :fields="billingFields"
          class="mt-2"
        />
      </sw-input-group>

      <div class="tabs mb-5 grid col-span-12">
              <div class="border-b tab">
                <div class="border-l-2 border-transparent relative">
                  <input
                    class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-6"
                    type="checkbox"
                    id="chck1"
                  />
                  <header
                    class="col-span-5 flex justify-between items-center p-3 pl-0 pr-8 cursor-pointer select-none tab-label"
                    for="chck1"
                  >
                    <span class="text-grey-darkest font-thin text-xl">
                      {{$t('settings.customization.email_template')}}
                    </span>
                    <div
                      class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
                    >
                      <!-- icon by feathericons.com -->
                      <svg
                        aria-hidden="true"
                        class=""
                        data-reactid="266"
                        fill="none"
                        height="24"
                        stroke="#606F7B"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        viewbox="0 0 24 24"
                        width="24"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <polyline points="6 9 12 15 18 9"></polyline>
                      </svg>
                    </div>
                  </header>
                  <div class="tab-content">
                    <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                      <ul class="pl-0">
                        <li class="pb-2">
                          <sw-tabs :active-tab="activeTab">
                            <sw-tab-item :title="$t('settings.customization.invoices.notice_one')">
                              <base-custom-input
                                v-model="invoices.invoice_notice_one"
                                :fields="InvoiceMailFields"
                                class="mt-2"
                              />
                            </sw-tab-item>
                            <sw-tab-item :title="$t('settings.customization.invoices.notice_two')">
                              <base-custom-input
                                v-model="invoices.invoice_notice_two"
                                :fields="InvoiceMailFields"
                                class="mt-2"
                              />
                            </sw-tab-item>
                            <sw-tab-item :title="$t('settings.customization.invoices.notice_three')">
                              <base-custom-input
                                v-model="invoices.invoice_notice_three"
                                :fields="InvoiceMailFields"
                                class="mt-2"
                              />
                            </sw-tab-item>
                            <sw-tab-item :title="$t('settings.customization.invoices.notice_auto_debit')">
                              <base-custom-input
                                v-model="invoices.invoice_notice_unpaid"
                                :fields="InvoiceMailFields"
                                class="mt-2"
                              />
                            </sw-tab-item>
                          </sw-tabs>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

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

    <sw-divider class="mt-6 mb-8" />

    <div class="flex">
      <div class="relative w-12">
        <sw-switch
          v-model="invoiceAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setInvoiceSetting"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.invoices.autogenerate_invoice_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.invoices.invoice_setting_description')
          }}
        </p>
      </div>
    </div>

    <sw-divider class="mt-6 mb-8" />

    <form action="" class="mt-6" @submit.prevent="updateScheduling">

      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.customization.invoices.task_scheduling') }}
      </h3>

      <div class="flex mb-6">
        <div class="relative w-12">
          <sw-switch
            v-model="allow_send_invoice_job"
            class="absolute"
            style="top: -18px"
            @change="(option) => slideSendInvoice(option)"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.customization.invoices.allow_send_invoice_job') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.customization.invoices.send_invoice_job_desc') }}
          </p>
        </div>
      </div>

      <sw-input-group
        :label="$t('settings.customization.invoices.period_time_run_job')"
        :error="timeJobError"
        required
      >
        <sw-input
          v-model="period_time_run_send_invoice_job"
          :invalid="$v.period_time_run_send_invoice_job.$error"
          style="max-width: 30%"
          @input="$v.period_time_run_send_invoice_job.$touch()"
          type="number"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
      >
        <save-icon v-if="!isLoading" class="mr-2"/>
        {{ $t('settings.customization.invoices.save_scheduling') }}
      </sw-button>
    </form>

  </div>
</template>

<script>
import {requiredIf} from "vuelidate/lib/validators";

const {
  required,
  maxLength,
  alpha,
  between
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
      invoiceAutogenerate: false,
      activeTab: 'Invoice Notice One',

      invoices: {
        invoice_prefix: null,
        invoice_mail_body: null,
        invoice_notice_one: null, 
        invoice_notice_two: null, 
        invoice_notice_three: null, 
        invoice_notice_unpaid: null, 
        company_address_format: null,
        shipping_address_format: null,
        billing_address_format: null,
      },
      isLoading: false,
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'invoice',
        'invoiceCustom',
        'company',
      ],
      billingFields: ['billing', 'customer', 'customerCustom', 'invoiceCustom'],
      shippingFields: [
        'shipping',
        'customer',
        'customerCustom',
        'invoiceCustom',
      ],
      companyFields: ['company', 'invoiceCustom'],
      allow_send_invoice_job: false,
      period_time_run_send_invoice_job: null,
    }
  },

  computed: {
    invoicePrefixError() {
      if (!this.$v.invoices.invoice_prefix.$error) {
        return ''
      }

      if (!this.$v.invoices.invoice_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.invoices.invoice_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.invoices.invoice_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
    timeJobError() {
      if (!this.$v.period_time_run_send_invoice_job.$error) {
        return ''
      }
      if (!this.$v.period_time_run_send_invoice_job.required) {
        return this.$t('validation.required')
      }
    },
  },

  watch: {
    settings(val) {
      
      this.invoices.invoice_prefix = val ? val.invoice_prefix : ''

      this.invoices.invoice_mail_body = val ? val.invoice_mail_body : null
      this.invoices.company_address_format = val
        ? val.invoice_company_address_format
        : null
      this.invoices.shipping_address_format = val
        ? val.invoice_shipping_address_format
        : null
      this.invoices.billing_address_format = val
        ? val.invoice_billing_address_format
        : null

      this.invoices.invoice_notice_one= val ? val.invoice_notice_one: null
      this.invoices.invoice_notice_two= val ? val.invoice_notice_two: null 
      this.invoices.invoice_notice_three= val ? val.invoice_notice_three: null 
      this.invoices.invoice_notice_unpaid= val ? val.invoice_notice_unpaid: null

      this.invoice_auto_generate = val ? val.invoice_auto_generate : ''

      if (this.invoice_auto_generate === 'YES') {
        this.invoiceAutogenerate = true
      } else {
        this.invoiceAutogenerate = false
      }

      this.period_time_run_send_invoice_job = val ? val.period_time_run_send_invoice_job : null
      this.allow_send_invoice_job = val ? val.allow_send_invoice_job == 1 : false
    },
  },

  validations: {
    invoices: {
      invoice_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
    period_time_run_send_invoice_job: {
      required: requiredIf(function () {
        return this.allow_send_invoice_job
      }),
      between: between(1, 59)
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    async setInvoiceSetting() {
      let data = {
        settings: {
          invoice_auto_generate: this.invoiceAutogenerate ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'INVOICES') {
        this.invoices.invoice_prefix = this.invoices.invoice_prefix.toUpperCase()

        return true
      }
    },

    async updateInvoiceSetting() {
      this.$v.invoices.$touch()

      if (this.$v.invoices.$invalid) {
        return false
      }

      let data = {
        settings: {
          invoice_prefix: this.invoices.invoice_prefix,
          invoice_mail_body: this.invoices.invoice_mail_body,
          invoice_company_address_format: this.invoices.company_address_format,
          invoice_billing_address_format: this.invoices.billing_address_format,
          invoice_shipping_address_format: this.invoices
            .shipping_address_format,
          invoice_notice_one: this.invoices.invoice_notice_one, 
          invoice_notice_two: this.invoices.invoice_notice_two, 
          invoice_notice_three: this.invoices.invoice_notice_three, 
          invoice_notice_unpaid: this.invoices.invoice_notice_unpaid,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.invoices.invoice_setting_updated')
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

    slideSendInvoice(val) {
      if (!val) {
        this.period_time_run_send_invoice_job = null;
      }
    },

    async updateScheduling() {
      this.$v.period_time_run_send_invoice_job.$touch()
      if (this.$v.period_time_run_send_invoice_job.$error) {
        return true
      }
      let data = {
        settings: {
          allow_send_invoice_job: this.allow_send_invoice_job ?? 0,
          period_time_run_send_invoice_job: this.period_time_run_send_invoice_job ?? 10
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
