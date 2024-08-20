<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateInvoiceSetting">

      <sw-input-group
        
        :label="$t('settings.customization.invoices.invoice_prefix')"
        :error="invoicePrefixError"
      >
        <sw-input
          class="input-expand"
          v-model="invoices.invoice_prefix"
          :invalid="$v.invoices.invoice_prefix.$error"
          style="max-width: 30%"
          @input="$v.invoices.invoice_prefix.$touch()"
          @keyup="changeToUppercase('INVOICES')"
        />
      </sw-input-group>

      <!-- Email -->

      <sw-input-group
            :label="$t('settings.customization.invoices.invoice_bcc_email')"
            class="md:col-span- mt-2 col-12 col-md-6 col-lg-4 mb-3"
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

      <!-- Email -->

      <sw-input-group class=" mt-2 col-12 col-md-6 col-lg-4 mb-3 " :label="$t('settings.customization.invoices.default_invoice_email_subject')" >
        <base-custom-input v-model="invoices.invoice_mail_subject" 
        :fields="InvoiceMailFields" 
          />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.invoices.default_invoice_email_body')
        "
        class="mt-6 mb-4 col-12 col-md-6 col-lg-4 mb-3"
      >
        <base-custom-input
          v-model="invoices.invoice_mail_body"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.default_invoice_footer')"
        class="mt-6 mb-4 col-12 col-md-6 col-lg-4 mb-3"
      >
        <base-custom-input
          v-model="invoices.invoice_footer"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.company_address_format')"
        class="mt-6 mb-4 col-12 col-md-6 col-lg-4 mb-3"
      >
        <base-custom-input
          v-model="invoices.company_address_format"
          :fields="companyFields"
          class="mt-2 col-12 col-md-6 col-lg-4 mb-3"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.shipping_address_format')"
        class="mt-6  col-12 col-md-6 col-lg-4 mb-4"
      >
        <base-custom-input
          v-model="invoices.shipping_address_format"
          :fields="shippingFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.billing_address_format')"
        class="mt-6 col-12 col-md-6 col-lg-4 mb-4"
      >
        <base-custom-input
          v-model="invoices.billing_address_format"
          :fields="billingFields"
          class="mt-2"
        />
      </sw-input-group>

      <div class="tabs mb-5 grid col-span-12 col-12 col-md-6 col-lg-4">
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
                {{ $t('settings.customization.email_template') }}
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
            <div class="tab-content col-12 col-md-6 col-lg-4 mb-3">
              <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                <ul class="pl-0">
                  <li class="pb-2">
                    <sw-tabs :active-tab="activeTab">
                      <sw-tab-item
                        :title="
                          $t('settings.customization.invoices.notice_one')
                        "
                      >
                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.invoice_subject')" >
                        <base-custom-input v-model="invoices.invoice_notice_one_subject" 
                        :fields="InvoiceMailFields" 
                        />
                      </sw-input-group>
                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.invoice_body')" >
                        <base-custom-input
                        v-model="invoices.invoice_notice_one"
                        :fields="InvoiceMailFields"
                        class="mt-2"
                        />
                      </sw-input-group>
                      </sw-tab-item>

                      <sw-tab-item
                        :title="
                          $t('settings.customization.invoices.notice_two')
                        "
                      >
                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.invoice_subject')" >
                        <base-custom-input v-model="invoices.invoice_notice_two_subject" 
                        :fields="InvoiceMailFields" 
                        />
                      </sw-input-group>
                      
                      <sw-input-group class=" mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.invoice_body')" >
                        <base-custom-input
                          v-model="invoices.invoice_notice_two"
                          :fields="InvoiceMailFields"
                          class="mt-2"
                          />
                        </sw-input-group>
                      </sw-tab-item>

                      <sw-tab-item
                        :title="
                          $t('settings.customization.invoices.notice_three')
                        "
                      >

                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.invoice_subject')" >
                        <base-custom-input v-model="invoices.invoice_notice_three_subject" 
                        :fields="InvoiceMailFields" 
                        />
                      </sw-input-group>
                      
                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.invoice_body')" >
                        <base-custom-input
                        v-model="invoices.invoice_notice_three"
                        :fields="InvoiceMailFields"
                        class="mt-2"
                        />
                      </sw-input-group>
                      </sw-tab-item>
                      <sw-tab-item
                        :title="
                          $t(
                            'settings.customization.invoices.notice_auto_debit'
                          )
                        "
                      >
                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.auto_debit_subject')" >
                        <base-custom-input v-model="invoices.invoice_notice_unpaid_subject" 
                        :fields="InvoiceMailFields" 
                        />
                      </sw-input-group>
                      
                      <sw-input-group class="mt-2 col-12 col-md-6 col-lg-4 mb-3" :label="$t('settings.customization.invoices.auto_debit_body')" >
                        <base-custom-input
                        v-model="invoices.invoice_notice_unpaid"
                        :fields="InvoiceMailFields"
                        class="mt-2"
                        />
                      </sw-input-group>
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
        v-if="permission.update"
      >
        <save-icon v-if="!isLoading" class="mr-2"  />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 col-12 col-md-6 col-lg-4 mb-8" />

    <div class="flex">
      <div class="relative w-12">
        <sw-switch
          v-model="invoiceAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setInvoiceSetting"
        />
      </div>

      <div class="ml-4 col-12 col-md-6 col-lg-4 mb-3">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.invoices.autogenerate_invoice_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500 col-12 col-md-6 col-lg-4 mb-3"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.invoices.invoice_setting_description')
          }}
        </p>
      </div>
    </div>

    <sw-divider class="mt-6 mb-8 col-12 col-md-6 col-lg-4 mb-3" />


    <form action="" class="mt-6 col-12 col-md-6 col-lg-4 mb-3" @submit.prevent="updateDueDate">
      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('corePbx.customization.invoice_due_date') }}
      </h3>

      <sw-input-group
        class="margin-grid"
        :label="$t('corePbx.customization.invoice_due_date')"
        :error="InvoiceDuedateError"
        required
      >
        <sw-input
          v-model="invoice_issuance_period"
          :invalid="$v.invoice_issuance_period.$error"
          style="max-width: 30%"
          @input="$v.invoice_issuance_period.$touch()"
          type="number"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-8"
        v-if="permission.update" 
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('corePbx.customization.save_duedate') }}
      </sw-button>

    </form>
    <sw-divider class="mt-6 mb-8" />

    <form action="" class="mt-6 col-12 col-md-6 col-lg-4 mb-3" @submit.prevent="updateScheduling">
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
        class="margin-grid"
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

      <sw-divider class="mt-8 mb-8" />

      <h3 class="my-6 text-lg font-medium text-black">
        {{ $t('settings.customization.invoices.late_fees') }}
      </h3>

      <sw-input-group
        class="margin-grid"
        :label="$t('settings.customization.invoices.late_fees')"
        :error="lateFeesError"
        required
      >      

        <base-time-picker
          class="margin-calendar"
          v-model="late_fees"
          :invalid="$v.late_fees.$error"
          :calendar-button="true"
          style="max-width: 30%"
          :placeholder="'HH:mm'"
          calendar-button-icon="calendar"
          @input="$v.late_fees.$touch()"
        />
      </sw-input-group>

      <h3 class="text-lg font-medium text-black">
        {{ $t('settings.customization.invoices.late_fee_one') }}
      </h3>

      <div class="grid grid-cols-3 display-grid">
        <sw-input-group
          class="my-6"
          :label="$t('settings.customization.invoices.activate')"
          required
        >
          <sw-switch
            v-model="late_fee_one"
            class="absolute margin-tl"
            style="top: -16px; left: 28%;"
            @change=""
          />
        </sw-input-group>

        <sw-input-group
          class="my-6"
          :label="$t('settings.customization.invoices.late_fee_days')"
          required
        >
          <sw-input
            class="margin-tl-wh"
            type="number"
            max="90"
            style="top: 5%; left: 14%; max-width: 70%; position: absolute;"
            v-model="late_fee_one_days"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.customization.invoices.late_fee_value')"
          required
          class="mr-6 my-6"
        >
          <div class="ml-12" role="group">
            <sw-input
              v-model="late_fee_one_value"
              class="ml-3 border-r-0 rounded-tr-sm rounded-br-sm  margin-tl-wh"
              style="top: 5%; position: absolute; max-width: 70%;"
            />
            <sw-dropdown position="bottom-end">
              <sw-button
                class="margin-tl2"
                v-model="late_fee_one_type"
                slot="activator"
                type="button"
                data-toggle="dropdown"
                size="discount"
                aria-haspopup="true"
                aria-expanded="false"
                style="height: 41px; position: absolute; left: 74%; top: -26px"
                variant="white"                
              >
                <span class="flex">
                  {{
                    late_fee_one_type == 'fixed'
                        ? "$"
                        : '%'
                  }}
                  <chevron-down-icon class="h-5" />
                </span>
                
              </sw-button>

              <sw-dropdown-item @click="selectFixed_1">
                {{ $t('general.fixed') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="selectPercentage_1">
                {{ $t('general.percentage') }}
              </sw-dropdown-item>
            </sw-dropdown>
            
          </div>
        </sw-input-group>
      </div>      
           

      <h3 class="mt-6 mb-3 text-lg font-medium text-black">
        {{ $t('settings.customization.invoices.late_fee_two') }}
      </h3>

      <div class="grid grid-cols-3 display-grid">

        <sw-input-group
        class="my-6"
          :label="$t('settings.customization.invoices.activate')"
          required
        >
          <sw-switch
            v-model="late_fee_two"
            class="absolute margin-tl"
            style="top: -16px; left: 28%;"
            @change=""
          />              
        </sw-input-group>

        <sw-input-group
        class="my-6"
          :label="$t('settings.customization.invoices.late_fee_days')"
          required
        >
          <sw-input
            class="margin-tl-wh"
            type="number"
            max="90"
            style="top: 5%; left: 14%; max-width: 70%; position: absolute;"
            v-model="late_fee_two_days"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.customization.invoices.late_fee_value')"
          required
          class="mr-6 my-6"
        >
          <div class="ml-12" role="group">
              <sw-input
                v-model="late_fee_two_value"                
                class="ml-3 border-r-0 rounded-tr-sm rounded-br-sm margin-tl-wh"
                style="top: 5%; position: absolute; max-width: 70%;"
              />
              <sw-dropdown position="bottom-end">
                <sw-button
                  class="margin-tl2"
                  v-model="late_fee_two_type"
                  slot="activator"
                  type="button"
                  data-toggle="dropdown"
                  size="discount"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="height: 41px; position: absolute; left: 74%; top: -26px"
                  variant="white"
                >
                
                <span class="flex">
                  {{
                    late_fee_two_type == 'fixed'
                        ? "$"
                        : '%'
                  }}
                  <chevron-down-icon class="h-5" />
                </span>

                </sw-button>

                <sw-dropdown-item @click="selectFixed_2">
                  {{ $t('general.fixed') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="selectPercentage_2">
                  {{ $t('general.percentage') }}
                </sw-dropdown-item>
              </sw-dropdown>
          </div>
          
        </sw-input-group>

      </div>
      
      <h3 class="mt-6 mb-3 text-lg font-medium text-black">
        {{ $t('settings.customization.invoices.late_fee_three') }}
      </h3>

      <div class="grid grid-cols-3 display-grid">

        <sw-input-group
        class="my-6"
          :label="$t('settings.customization.invoices.activate')"
          required
        >
          <sw-switch
            v-model="late_fee_three"
            class="absolute margin-tl"
            style="top: -16px; left: 28%;"
            @change=""
          />
        </sw-input-group>

        <sw-input-group
        class="my-6"
          :label="$t('settings.customization.invoices.late_fee_days')"
          required
        >
          <sw-input
          class="margin-tl-wh"
            type="number"
            max="90"
            style="top: 5%; left: 14%; max-width: 70%; position: absolute;"
            v-model="late_fee_three_days"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.customization.invoices.late_fee_value')"
          required
          class="mr-6 my-6"
        >
          <div class="ml-12" role="group">
              <sw-input
                v-model="late_fee_three_value"
                class="ml-3 border-r-0 rounded-tr-sm rounded-br-sm margin-tl-wh"
                style="top: 5%; position: absolute; max-width: 70%;"
              />
              <sw-dropdown position="bottom-end">
                <sw-button
                  class="margin-tl2"
                  v-model="late_fee_three_type"
                  slot="activator"
                  type="button"
                  data-toggle="dropdown"
                  size="discount"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="height: 41px; position: absolute; left: 74%; top: -26px"
                  variant="white"
                >

                <span class="flex">
                  {{
                    late_fee_three_type == 'fixed'
                        ? "$"
                        : '%'
                  }}
                  <chevron-down-icon class="h-5" />
                </span>
                  
                </sw-button>

                <sw-dropdown-item @click="selectFixed_3">
                  {{ $t('general.fixed') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="selectPercentage_3">
                  {{ $t('general.percentage') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </div>
          
        </sw-input-group>
      </div>

      <!-- ALLO INVOICE LATE FEE RETROACTIVE -->

      <h3 class="mt-6 mb-3 text-lg font-medium text-black">
        {{ $t('settings.customization.invoices.invoice_late_fee_retroactive') }}
      </h3>
      <div class="grid grid-cols-3 display-grid">
        <sw-input-group
          :label="$t('settings.customization.invoices.activate')"
          required
        >
          <sw-switch
            v-model="invoice_late_fee_retroactive"
            class="absolute margin-tl"
            style="top: -16px; left: 28%;"
            @change=""
          />
        </sw-input-group>
      </div>
      <!-- END ALLOW INVOICE LATE FEE RETROACTIVE -->

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-8 margin-buttonl"
        v-if="permission.update" 
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.invoices.save_scheduling') }}
      </sw-button>
    </form>
  </div>
</template>

<script>
import { requiredIf } from 'vuelidate/lib/validators'

const {
  required,
  email,
  maxLength,
  alpha,
  between,
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
      invoiceAutogenerate: false,
    
      activeTab: 'Invoice Notice One',
      discount: 0,
      invoices: {
        invoice_prefix: null,
        invoice_mail_body: null,
        invoice_mail_subject: null,
        invoice_footer: null,
        invoice_notice_one: null,
        invoice_notice_one_subject: null,
        invoice_notice_two: null,
        invoice_notice_two_subject: null,
        invoice_notice_three: null,
        invoice_notice_three_subject: null,
        invoice_notice_unpaid: null,
        invoice_notice_unpaid_subject: null,
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
      invoice_issuance_period:0,
      late_fees: '00:00',
      late_fee_one: false,
      late_fee_two: false,
      late_fee_three: false,
      invoice_late_fee_retroactive: false,
      late_fee_one_days: 0,
      late_fee_two_days: 0,
      late_fee_three_days: 0,
      late_fee_one_value: 0,
      late_fee_two_value: 0,
      late_fee_three_value: 0,
      late_fee_one_type: 'fixed',
      late_fee_two_type: 'fixed',
      late_fee_three_type: 'fixed',
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
    InvoiceDuedateError() {
      if (!this.$v.invoice_issuance_period.$error) {
        return ''
      }
      if (!this.$v.invoice_issuance_period.required) {
        return this.$t('validation.required')
      }
    },
    lateFeesError() {
      if (!this.$v.late_fees.$error) {
        return ''
      }
      if (!this.$v.late_fees.required) {
        return this.$t('validation.required')
      }
    }
  },

  watch: {

    settings(val) {

     // console.log(val)
      this.invoices.invoice_prefix = val ? val.invoice_prefix : ''
      this.invoices.invoice_mail_body = val ? val.invoice_mail_body : null
      this.invoices.invoice_mail_subject = val ? val.invoice_mail_subject : null
      this.invoices.invoice_footer = val ? val.invoice_footer : null

      this.invoices.company_address_format = val ? val.invoice_company_address_format : null
      this.invoices.shipping_address_format = val ? val.invoice_shipping_address_format : null
      this.invoices.billing_address_format = val ? val.invoice_billing_address_format : null
      this.invoices.invoice_notice_one = val ? val.invoice_notice_one : null
      this.invoices.invoice_notice_one_subject = val ? val.invoice_notice_one_subject : null
      this.invoices.invoice_notice_two = val ? val.invoice_notice_two : null
      this.invoices.invoice_notice_two_subject = val ? val.invoice_notice_two_subject : null
      this.invoices.invoice_notice_three = val ? val.invoice_notice_three : null
      this.invoices.invoice_notice_three_subject = val ? val.invoice_notice_three_subject : null
      this.invoices.invoice_notice_unpaid = val ? val.invoice_notice_unpaid : null
      this.invoices.invoice_notice_unpaid_subject = val ? val.invoice_notice_unpaid_subject : null
      this.invoice_auto_generate = val ? val.invoice_auto_generate : '' 
      this.invoice_late_fee_retroactive = val.invoice_late_fee_retroactive == '0' ? false : true

     
      if (this.invoice_auto_generate === 'YES') {
        this.invoiceAutogenerate = true
      } else {
        this.invoiceAutogenerate = false
      }
    
      this.allow_send_invoice_job = val
        ? val.allow_send_invoice_job == 1
        : false

      this.period_time_run_send_invoice_job = val
        ? val.period_time_run_send_invoice_job
        : null

      // late_fee_hour
      
  this.invoice_issuance_period = val
        ? val.invoice_issuance_period 
        : 0
     
      if(val.hasOwnProperty('late_fee_hour'))
      {
        this.late_fee_hour = val
        ? val.late_fee_hour
        : false

        this.late_fees = val
        ? val.late_fee_hour
        : false
      }

      // console.log(this.late_fee_hour)

      // actives

      this.late_fee_one = val.invoice_late_fee_active_one == 1 ? true : false
      this.late_fee_two = val.invoice_late_fee_active_two == 1 ? true : false
      this.late_fee_three = val.invoice_late_fee_active_three == 1 ? true : false
      
      // days

      if(val.hasOwnProperty('invoice_late_fee_days_one'))
      {
        this.late_fee_one_days = val
        ? val.invoice_late_fee_days_one
        : false
      }

      if(val.hasOwnProperty('invoice_late_fee_days_two'))
      {
          this.late_fee_two_days = val
          ? val.invoice_late_fee_days_two
          : false
      }

      if(val.hasOwnProperty('invoice_late_fee_days_three'))
      {
          this.late_fee_three_days = val
          ? val.invoice_late_fee_days_three
          : false
      }

      // value

      if(val.hasOwnProperty('invoice_late_fee_type_one_value'))
      {
          this.late_fee_one_value = val
          ? val.invoice_late_fee_type_one_value
          : false  
      }

      if(val.hasOwnProperty('invoice_late_fee_type_two_value'))
      {
          this.late_fee_two_value = val
          ? val.invoice_late_fee_type_two_value
          : false 
      }

      if(val.hasOwnProperty('invoice_late_fee_type_three_value'))
      {
          this.late_fee_three_value = val
          ? val.invoice_late_fee_type_three_value
          : false  
      }
       
      // fixed o percentage

      if(val.hasOwnProperty('invoice_late_fee_type_one'))
      {
          this.late_fee_one_type = val
          ? val.invoice_late_fee_type_one
          : false
      }

      if(val.hasOwnProperty('invoice_late_fee_type_two'))
      {
          this.late_fee_two_type = val
          ? val.invoice_late_fee_type_two
          : false
      }

      if(val.hasOwnProperty('invoice_late_fee_type_three'))
      {
          this.late_fee_three_type = val
          ? val.invoice_late_fee_type_three
          : false      
      }     

      this.email = val.invoice_bbc_email

      
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
      between: between(1, 59),
    },late_fees: {
      required
    },
    invoice_issuance_period: {
      required
    },
    email: {
      required,
      email,
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),

    // Fixed  
    selectFixed_1() {
      this.late_fee_one_type = 'fixed'   
    },
    selectFixed_2() {     
      this.late_fee_two_type = 'fixed'    
    },
    selectFixed_3() { 
      this.late_fee_three_type = 'fixed'     
    },

    // Percentage
    selectPercentage_1() {
      this.late_fee_one_type ='percentage'
    },
    selectPercentage_2() {
      this.late_fee_two_type = 'percentage'
    },
    selectPercentage_3() {   
      this.late_fee_three_type = 'percentage'
    },

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
   

    async updateInvoiceSetting() {
      this.$v.invoices.$touch()

      if (this.$v.invoices.$invalid) {
        return false
      }
      
      let data = {
        settings: {
          invoice_prefix: this.invoices.invoice_prefix,
          invoice_mail_body: this.invoices.invoice_mail_body,
          invoice_mail_subject: this.invoices.invoice_mail_subject,
          invoice_footer: this.invoices.invoice_footer,
          invoice_company_address_format: this.invoices.company_address_format,
          invoice_billing_address_format: this.invoices.billing_address_format,
          invoice_shipping_address_format: this.invoices.shipping_address_format,
          invoice_notice_one: this.invoices.invoice_notice_one,
          invoice_notice_one_subject: this.invoices.invoice_notice_one_subject,
          invoice_notice_two: this.invoices.invoice_notice_two,
          invoice_notice_two_subject: this.invoices.invoice_notice_two_subject,
          invoice_notice_three: this.invoices.invoice_notice_three,
          invoice_notice_three_subject: this.invoices.invoice_notice_three_subject,
          invoice_notice_unpaid: this.invoices.invoice_notice_unpaid,
          invoice_notice_unpaid_subject: this.invoices.invoice_notice_unpaid_subject, // Email
          invoice_bbc_email: this.email,
          invoice_late_fee_retroactive: this.invoice_late_fee_retroactive,
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
        this.period_time_run_send_invoice_job = null
      }
    },

    async updateDueDate() {
      this.$v.invoice_issuance_period.$touch()
      if (this.$v.invoice_issuance_period.$error) {
        return true
      }

      let data = {
        settings: {
          invoice_issuance_period: this.invoice_issuance_period ?? 7,
        },
      }

      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
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
          period_time_run_send_invoice_job: this.period_time_run_send_invoice_job ?? 10,

          // Field Value "late_fess_hour"
          late_fee_hour: this.late_fees,

           // Field Value "active"
          invoice_late_fee_active_one: this.late_fee_one,
          invoice_late_fee_active_two: this.late_fee_two,
          invoice_late_fee_active_three: this.late_fee_three,

          // Field Value "days"
          invoice_late_fee_days_one: this.late_fee_one_days,  
          invoice_late_fee_days_two: this.late_fee_two_days,
          invoice_late_fee_days_three: this.late_fee_three_days,  
          
          // Field Value "value"
          invoice_late_fee_type_one_value: this.late_fee_one_value,
          invoice_late_fee_type_two_value: this.late_fee_two_value,
          invoice_late_fee_type_three_value: this.late_fee_three_value,
          
          // Field Value "fixed or percentage"
          invoice_late_fee_type_one: this.late_fee_one_type,
          invoice_late_fee_type_two: this.late_fee_two_type,
          invoice_late_fee_type_three: this.late_fee_three_type,

          // Email
          invoice_bbc_email: this.email,

          // Allow invoice late fee retroactive

          invoice_late_fee_retroactive: this.invoice_late_fee_retroactive,

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

