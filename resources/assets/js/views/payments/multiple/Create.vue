<template>
    <base-page class="relative payment-create">
      <form action="" @submit.prevent="submitPaymentMultiple">
        <sw-page-header :title="pageTitle" class="mb-5">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              :title="$t('general.home')"
              to="/admin/dashboard"
            />
            <sw-breadcrumb-item
              :title="$tc('payments.payment', 2)"
              to="/admin/payments"
            />
            <sw-breadcrumb-item
              v-if="$route.name === 'payments.multiple.edit'"
              :title="$t('payments.edit_payment')"
              to="#"
              active
            />
            <sw-breadcrumb-item
              v-else
              :title="$t('payments.new_payment')"
              to="#"
              @click.native="$router.go()"
              active
            />
          </sw-breadcrumb>
  
          <template slot="actions">
            <sw-button
              v-if="!notEditable"
              :loading="isLoading"
              :disabled="isEdit"
              variant="primary"
              type="submit"
              size="lg"
              class="hidden sm:flex"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{
                isEdit
                  ? $t('payments.update_payment')
                  : $t('payments.save_payment')
              }}
            </sw-button>
            <sw-button v-else style="display: none"> </sw-button>
          </template>
        </sw-page-header>
  
        <base-loader v-if="isRequestOnGoing" />
  
        <sw-card v-else>
          <div class="grid gap-6 grid-col-1 md:grid-cols-2">
            <sw-input-group
              :label="$t('payments.date')"
              :error="DateError"
              required
            >
              <base-date-picker
                v-model="newPayment.payment_date"
                :invalid="$v.newPayment.payment_date.$error"
                :calendar-button="true"
                class="mt-1"
                calendar-button-icon="calendar"
                :disabled="isEdit"
                @input="$v.newPayment.payment_date.$touch()"
              />
            </sw-input-group>
  
            <sw-input-group
              :label="$t('payments.payment_number')"
              :error="paymentNumError"
              required
            >
              <sw-input
                :prefix="`${newPayment.paymentPrefix} - `"
                :invalid="$v.newPayment.paymentNumAttribute.$error"
                v-model.trim="newPayment.paymentNumAttribute"
                class="mt-1"
                :disabled="isEdit"
                autocomplete="off"
                @input="$v.newPayment.paymentNumAttribute.$touch()"
              />
            </sw-input-group>
  
            <sw-input-group
              :label="$t('payments.customer')"
              :error="customerError"
              required
            >
              <sw-select
                v-model="customer"
                :options="customers"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$t('customers.select_a_customer')"
                label="name"
                class="mt-1"
                track-by="id"
                :invalid="$v.customer.$error"
                @input="$v.customer.$touch()"
                :disabled=true
              />
            </sw-input-group>
  
            <sw-input-group 
              :label="$t('payments.invoice_title')"            
            >           
              <sw-select
                v-model="invoice"
                :options="invoiceList"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"                
                :placeholder="$t('invoices.select_invoice')"
                :custom-label="invoiceWithAmount"
                class="mt-1"
                track-by="invoice_number"
                :disabled=true
              />
            </sw-input-group>
  
             
            <sw-divider
              v-if="
                formData.user_id != null &&
                formData.invoice_id != null &&
                creditv &&
                this.customer.balance > 1 &&
                formData.payment_method === null
              "
              class="opacity-0"
            />
  
            <sw-input-group
              :label="$t('payments.amount')"
              required
            >
              <div class="relative w-full mt-1">
                <div class=" min-h-10 block pt-1.5 pr-10 pb-0 pl-2 rounded border border-solid text-sm  border-gray-300 bg-white bg-gray-200 text-gray-600 min-h-10 block pt-1.5 pr-10 pb-0 pl-2 rounded border border-solid text-sm"
                v-html="$utils.formatMoney(amount, currency)" />
                <!--
                <sw-money
                  v-model="amount"
                  :currency="customerCurrency"
                  :invalid="$v.formData.amount.$error"
                  :disabled="true"
                  class="
                    relative
                    w-full
                    focus:border focus:border-solid focus:border-primary-500
                  "
                  @input="$v.formData.amount.$touch()"
                />
                -->
              </div>
            </sw-input-group>
            
            <sw-input-group
              :label="$t('general.go_to_invoice')"              
            >
              <div class="relative w-12 ml-1 mt-2">
                <sw-switch v-model="go_to_invoice" class="absolute"
                  style="top: -18px" />
              </div>
            </sw-input-group>
           
            <!-- Select Status -->
            <sw-input-group
              v-if="isEdit"
              :label="$t('tax_groups.status')"
              class="mt-1"
              :error="statusError"
              required
            >
              <sw-select
                v-model="newPayment.status"
                :invalid="$v.newPayment.status ? $v.newPayment.status.$error : false"
                :options="status"
                :disabled="isEdit"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="false"
                :placeholder="$t('tax_groups.status')"
                label="text"
                track-by="value"
              />
            </sw-input-group>
  
          </div>

          <br>

          <div style="min-width: 50rem">
                <table class="w-full text-center item-table">
                <colgroup>
                    <col style="width: 60%" />
                    <col style="width: 20%" />
                    <col style="width: 5%" />  
                    <col style="width: 15%" />     
                </colgroup>
                <thead class="bg-white border border-gray-200 border-solid">
                    <tr>
                    <th
                        class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center">
                        <span class="pl-12">
                        {{ $t('payments.payment_mode') }}
                        </span>
                    </th>      
                    <th
                        class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center">
                        <span class="pr-0">
                        {{ $t('invoices.item.amount') }}
                        </span>
                    </th>
                    <th>
                    </th>  
                    <th
                        class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid text-center">
                        {{ $t('general.delete') }}
                    </th>                     
                    </tr>
                </thead>
                    <payment-method v-for="(payment_method, index) in newPayment.payment_methods"     
                    :key="payment_method.id_raw"               
                    :index="index"
                    :indexError="indexError"
                    :errorPmAmount="errorPmAmount"
                    :payment-method-data="payment_method"
                    :currency="currency"
                    :payments-methods="newPayment.payment_methods" 
                    :is-edit="isEdit"
                    :payment-methods-module="payment_methods_module"
                    :invoice-amount="maxPayableAmount"
                    :payment-amount="amount"
                    @remove="removePaymentMethod"
                    @update="updatePaymentMethod"
                    @paymentMethodValidate="checkPaymentMethodData"
                    />                
                </table>
          </div>

          <div
                class="flex items-center justify-center w-full px-6 py-3 text-base border-r border-b border-l border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
                @click="addItem">
                <plus-sm-icon class="h-5 mr-2" />                
                {{ $t('payments.add_payment_method') }}
          </div>
  
          <!-- END AUTHORIZE  
  
          <div v-if="customFields.length > 0">
            <div class="grid gap-6 mt-6 grid-col-1 md:grid-cols-2">
              <sw-input-group
                v-for="(field, index) in customFields"
                :label="field.label"
                :required="field.is_required ? true : false"
                :key="index"
              >
                <component
                  :type="field.type.label"
                  :field="field"
                  :is-edit="isEdit"
                  :is="field.type + 'Field'"
                  :invalid-fields="invalidFields"
                  @update="setCustomFieldValue"
                />
              </sw-input-group>
            </div>
          </div>-->
  
          <!-- integracion de paypal -->
  
          <sw-popup
            ref="notePopup"
            class="my-6 text-sm font-semibold leading-5 text-primary-400"
          >
            <div slot="activator" class="float-right mt-1">
              + {{ $t('general.insert_note') }}
            </div>
            <note-select-popup type="Payment" @select="onSelectNote" />
          </sw-popup>
  
          <sw-input-group :label="$t('payments.note')" class="mt-6 mb-4">
            <base-custom-input
              v-model="newPayment.notes"
              :fields="PaymentFields"
              class="mb-4"
            />
          </sw-input-group>
  
          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="flex w-full mt-4 sm:hidden md:hidden"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit ? $t('payments.update_payment') : $t('payments.save_payment')
            }}
          </sw-button>
        </sw-card>
      </form>
    </base-page>
  </template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex'
  import moment from 'moment'
  import { ShoppingCartIcon, XCircleIcon } from '@vue-hero-icons/solid'
  import CustomFieldsMixin from '../../../mixins/customFields'
  import { VueDatePicker } from '@mathieustan/vue-datepicker'
  import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css'
  import ItemModalVue from '../../../components/base/modal/ItemModal.vue'
  import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
  import Paypal from '.././Paypal.vue'

  import PaymentMethodStub from '../../../stub/paymentMethod'
  import PaymentMethod from './PaymentMethod'
  import Guid from 'guid'
  import draggable from 'vuedraggable'
  import { PlusSmIcon } from "@vue-hero-icons/solid";

  const {
    required,
    between,
    numeric,
    email,
    minLength,
    maxLength,
    requiredIf
  } = require('vuelidate/lib/validators')
  
  export default {
    components: {
      PaymentMethod,
      ShoppingCartIcon,
      XCircleIcon,
      VueDatePicker,
      EyeIcon,
      EyeOffIcon,
      Paypal,
      draggable,
      PlusSmIcon
    },
    mixins: [CustomFieldsMixin],
  
    data() {
      return {
        newPayment: {
            payment_date: new Date(),
            paymentPrefix: '',
            paymentNumAttribute: null,
            payment_methods: [
                {
                    ...PaymentMethodStub,
                    id_raw: Guid.raw()
                },
            ],            
            status: {
              value: 'Approved',
              text: 'Approved',
            },
            notes: null,
        },
        customer: null,
        invoice: null,
        PaymentFields: [
          'customer',
          'company',
          'customerCustom',
          'payment',
          'paymentCustom',
        ],
        go_to_invoice: true,
        payment_methods_module: [],
        allowPartialPay: false,
        indexError: 0,
        errorPmAmount: false,
        //
        
        selectedCurrency: '',
        customers_opt: null,
        isPaymentTypeAuthorize: false,
        creditv: false,
        isdisableed: true,
        isShowPassword: false,
        isShowPassword1: false,
        isShowPassword2: false,
        isedtiablefalse: false,
        formData: {
          user_id: null,
          payment_number: null,
          account_number: null,
          ACH_type: {
            value: 'checking',
            text: 'Checking',
          },
          routing_number: null,
          bank_name: null,
          num_check: null,
          
         
          customer_credit: false,
          payment_method: {
            name: null,
            account_accepted: null,
            add_payment_gateway: null,
            company_id: null,
            created_at: null,
            deleted_at: null,
            for_customer_use: null,
            generate_expense: null,
            generate_expense_id: null,
            id: null,
            payment_gateways_id: null,
            status: null,
            updated_at: null,
            void_refund: null,
            void_refund_expense_id: null,
          },
          invoice_id: null,
          notes: null,
          payment_method_id: null,
          payment_gateways: [],
          authorize: null,
          authorize_id: null,
          credit_cards: [],
          credit_card: null,
          updatebillinginformation: false,
          createaccount: false,
          transaction_status: null,
          isTransactionStatus: false,
          status_with_authorize: true,
          applied_credit_customer: true,
          add_payment_gateway: 0,
          status: {
            value: 'Approved',
            text: 'Approved',
          },
          void_status_change: false,
          refunded_status_change: false,
        },
        authorize: {
          payer_email: '',
          card_number: '',
          credit_cards: '',
          cvv: '',
          date: '',
          name: null,
          country_id: null,
          state_id: null,
          city: null,
          phone: null,
          zip: null,
          address_street_1: null,
          address_street_2: null,
          country: null,
          state: null,
          type: 'billing',
          first_name: null,
          last_name: null,
          company_name: null,
          email: null,
        },
        money: {
          decimal: '.',
          thousands: ',',
          prefix: '$ ',
          precision: 2,
          masked: false,
        },
        
        account: null,
        card: null,
        
        invoiceList: [],
        accountList: [],
        cardList: [],
        isLoading: false,
        isRequestOnGoing: false,
        fetchingPaymentMethod: false,
        maxPayableAmount: Number.MAX_SAFE_INTEGER,
        maxAmountIsNotCustomerCreditBalance: 0,
        isSettingInitialData: true,
        paymentNumAttribute: null,
        paymentPrefix: '',
        add_payment_gateway_select: false,
        type_ach: false,
        type_cc: false,
        payment_gateways: [],
        isAuthorizeEdit: false,
        is_authorize: false,
        is_paypal: false,
        locale: { lang: 'en' },
        isTransactionStatus: false,
        transactionStatusCheck: false,
        transactionStatusCheckUnapply: false,
        notEditable: false,
        updatebillinginformation: false,
        createaccount: false,
  
        billing_state: null,
        billing_country: null,
  
        countries: [],
        billing_states: [],
  
        credit_cards: [
          { name: 'VISA', value: 'VISA' },
          { name: 'MASTERCARD', value: 'MASTERCARD' },
          { name: 'AMERICAN EXPRESS', value: 'AMERICAN EXPRESS' },
          { name: 'DISCOVER', value: 'DISCOVER' },
        ],
        status: [
          { value: 'Approved', text: 'Approved' },
          { value: 'Void', text: 'Void' },
          { value: 'Refunded', text: 'Refunded' },
          { value: 'Unapply', text: 'Unapply' },
        ],
        isTransactionFail: false,
        transactionFail: {
          payment_gateway: null,
          transaction_number: null,
          date: null,
          amount: null,
          payment_number: null,
          customer_id: null,
          invoice_id: null,
          description: null,
          credit_card_number: null,
          credit_card_type: null,
          credit_card_expiration_date: null,
          authorize_object: null,
          type_trasaction: null,
        },
  
        bank_account_type: [
          {
            value: 'checking',
            text: 'Checking',
          },
          {
            value: 'Savings',
            text: 'Savings',
          },
        ],
        paymentSuccess: false,
        isLoadingPayments: false,
        paymentPaypalProccess: false,
  
        // variables para show input fiedl hide or show
        showCardFieldHide: true,
        showCvvFieldHide: true,
        showAccountFieldHide: true,
        showRoutingFieldHide: true,
      }
    },
    validations() {      
        return {
          newPayment: {
            payment_date: {
              required
            },
            paymentNumAttribute: {
              required,
              numeric,
            },
          },
          customer: {
            required,
          },
          invoice: {
            required
          },          
        }      
    },
    computed: {
      ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),
      ...mapGetters('payment', ['paymentModes', 'selectedNote']),
      ...mapGetters('customer', ['customers']),
   
      currency() {
        return this.selectedCurrency
      },

      dateExpirationYear: {
        get() {
          return this.authorize.date.split('-')[0]
        },
        set(year) {
          this.authorize.date = year + '-' + this.authorize.date.split('-')[1]
          const currentYear = new Date().getFullYear()
          if (year == currentYear) {
            this.authorize.date = year + '-' + this.monthsOptions[0]
          }
        },
      },
      dateExpirationMonth: {
        get() {
          return this.authorize.date.split('-')[1]
        },
        set(month) {
          this.authorize.date = this.authorize.date.split('-')[0] + '-' + month
        },
      },
      // generador de los 15 años para el select de fecha de expiración de la tarjeta de crédito
      yearsOptions() {
        const years = []
        const currentYear = new Date().getFullYear()
        for (let i = currentYear; i < currentYear + 15; i++) {
          years.push(`${i}`)
        }
        return years
      },
      // generador de los 12 meses del año formato MM
      monthsOptions() {
        if(this.isAuthorize){
          const months = []
          const yearSelect = this.authorize.date.split('-')[0]
          const currentMonth =
            yearSelect == new Date().getFullYear() ? new Date().getMonth() + 1 : 1
          for (let i = 1; i <= 12; i++) {
            months.push(i < 10 ? `0${i}` : `${i}`)
          }
          return months  
        }
      },
  
      /*
      amount: {
        get: function () {
          return this.formData.amount / 100
        },
        set: function (newValue) {
          this.formData.amount = Math.round(newValue * 100)
        },
      },
      */

      amount()
      {
        return this.newPayment.payment_methods.reduce(function (a, b) {
                    return a + b['amount']
               }, 0)
      },

      pageTitle() {
        if (this.$route.name === 'payments.multiple.edit') {
          return this.$t('payments.edit_payment')
        }
        return this.$t('payments.new_payment')
      },
      isEdit() {
        if (this.$route.name === 'payments.multiple.edit') {
          return true
        }
        return false
      },
      customerCurrency() {
        if (this.customer && this.customer.currency) {
          return {
            decimal: this.customer.currency.decimal_separator,
            thousands: this.customer.currency.thousand_separator,
            prefix: this.customer.currency.symbol + ' ',
            precision: this.customer.currency.precision,
            masked: false,
          }
        } else {
          return this.defaultCurrencyForInput
        }
      },
      customerError() {
        if (!this.$v.customer.$error) {
          return ''
        }
  
        if (!this.$v.customer.required) {
          return this.$tc('validation.required')
        }
      },
      /*
      invoiceError() {
        if (!this.$v.invoice.$error) {
          return ''
        }
  
        if (!this.$v.invoice.required) {
          return this.$tc('validation.required')
        }
      },
      */
      DateError() {
        if (!this.$v.newPayment.payment_date.$error) {
          return ''
        }
        if (!this.$v.newPayment.payment_date.required) {
          return this.$t('validation.required')
        }
      },
      amountError() {
        if (!this.$v.formData.amount.$error) {
          return ''
        }
  
        if (!this.$v.formData.amount.required) {
          return this.$t('validation.required')
        }
  
        if (
          !this.$v.formData.amount.between &&
          this.$v.formData.amount.numeric &&
          this.amount <= 0
        ) {
          return this.$t('validation.payment_greater_than_zero')
        }
        
        if (!this.$v.formData.amount.between && this.amount > 0) {
            return this.$t('validation.payment_greater_than_due_amount')
        }
        
        
      },
      paymentNumError() {
        if (!this.$v.newPayment.paymentNumAttribute.$error) {
          return ''
        }
  
        if (!this.$v.newPayment.paymentNumAttribute.required) {
          return this.$tc('validation.required')
        }
  
        if (!this.$v.newPayment.paymentNumAttribute.numeric) {
          return this.$tc('validation.numbers_only')
        }
      },
      creditCardError() {
        if (!this.isEdit) {
          if (!this.$v.formData.credit_cards.required) {
            return this.$tc('validation.required')
          }
        }
      },
      emailError() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.payer_email.$error) {
            return ''
          }
          if (!this.$v.authorize.payer_email.required) {
            return this.$tc('validation.required')
          }
          if (!this.$v.authorize.payer_email.email) {
            return this.$tc('validation.email_incorrect')
          }
        }
      },
      ccNumberError() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.card_number.$error) {
            return ''
          }
          if (!this.$v.authorize.card_number.required) {
            return this.$tc('validation.required')
          }
          if (!this.$v.authorize.card_number.numeric) {
            return this.$tc('validation.numbers_only')
          }
          if (!this.$v.authorize.card_number.minLength) {
            return this.$tc(
              'validation.name_min_length',
              this.$v.authorize.card_number.$params.minLength.min,
              { count: this.$v.authorize.card_number.$params.minLength.min }
            )
          }
          if (!this.$v.authorize.card_number.maxLength) {
            return this.$t('authorize.cc_number_maxLength')
          }
        }
      },
      CvVError() {      
        if (this.isAuthorize) {
          if (!this.$v.authorize.cvv.$error) {
            return ''
          }
          if (!this.$v.authorize.cvv.required) {
            return this.$tc('validation.required')
          }
          if (!this.$v.authorize.cvv.numeric) {
            return this.$tc('validation.numbers_only')
          }
          if (!this.$v.authorize.cvv.minLength) {
            return this.$tc(
              'validation.name_min_length',
              this.$v.authorize.cvv.$params.minLength.min,
              { count: this.$v.authorize.cvv.$params.minLength.min }
            )
          }
          if (!this.$v.authorize.cvv.maxLength) {
            return this.$t('authorize.cvv_maxLength')
          }
        }
      },
      countryIdError() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.country_id.$error) {
            return ''
          }
          if (!this.$v.authorize.country_id.required) {
            return this.$tc('validation.required')
          }
        }
      },
      stateIdError() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.state_id.$error) {
            return ''
          }
          if (!this.$v.authorize.state_id.required) {
            return this.$tc('validation.required')
          }
        }
      },
      cityError() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.city.$error) {
            return ''
          }
          if (!this.$v.authorize.city.required) {
            return this.$tc('validation.required')
          }
        }
      },
      billAddress1Error() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.address_street_1.$error) {
            return ''
          }
          if (!this.$v.authorize.address_street_1.required) {
            return this.$tc('validation.required')
          }
          if (!this.$v.authorize.address_street_1.maxLength) {
            return this.$t('validation.address_maxlength')
          }
        }
      },
      billAddress2Error() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.address_street_2.$error) {
            return ''
          }
          if (!this.$v.authorize.address_street_2.maxLength) {
            return this.$t('validation.address_maxlength')
          }
        }
      },
      zipError() {
        if (this.isAuthorize) {
          if (!this.$v.authorize.zip.$error) {
            return ''
          }
          if (!this.$v.authorize.zip.required) {
            return this.$tc('validation.required')
          }
        }
      },
      expirationDateError() {
        if (this.isAuthorize) {
          if (this.isAuthorize) {
            if (!this.$v.authorize.date.$error) {
              return ''
            }
            if (!this.$v.authorize.date.required) {
              return this.$tc('validation.required')
            }
          }
        }
      },
      statusError() {
        if (this.isEdit) {
          if (this.$v.newPayment.status && !this.$v.newPayment.status.$error) {
            return ''
          }
          if (this.$v.newPayment.status && !this.$v.newPayment.status.required) {
            return this.$tc('validation.required')
          }
        }
      },
      paymentMethodError() {
        if (this.isEdit) {
          if (
            this.$v.formData.payment_method &&
            !this.$v.formData.payment_method.$error
          ) {
            return ''
          }
          if (
            this.$v.formData.payment_method &&
            !this.$v.formData.payment_method.required
          ) {
            return this.$tc('validation.required')
          }
        }
      },
      isPaypal(){
        if(this.formData.payment_method && this.formData.payment_method.paypal_button){
          return true
        }
        return false
      },
      isAuthorize() {
        if (this.isEdit && this.isAuthorizeEdit) {
          this.is_authorize = true
          return true
        }
        if (this.formData.payment_gateways) {
          if (this.formData.payment_gateways.name === 'Authorize') {
            if (this.customer) {
              let params = {
                id: this.customer.id,
              }
              this.loadCustomerData(params)
              // console.log('si carga authorize')
            }
            this.is_authorize = true
            this.is_paypal = false
            return true
          } else if (this.formData.payment_gateways.name === 'Paypal') {
            if (this.customer) {
              let params = {
                id: this.customer.id,
              }
              this.loadCustomerData(params)
            }
            this.is_paypal = true
            this.is_authorize = false
            // console.log('yes paypal')
            return true
          } else {
            this.is_paypal = false
            this.is_authorize = false
            return false
          }
        }
        return false
      },
      ACHTypeError() {
        if (!this.$v.formData.ACH_type.$error) {
          return ''
        }
        if (!this.$v.formData.ACH_type.required) {
          return this.$tc('validation.required')
        }
      },
      accountNumberError() {
        if (!this.$v.formData.account_number.$error) {
          return ''
        }
        if (!this.$v.formData.account_number.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.formData.account_number.minLength) {
          return this.$t('validation.account_number_minLength')
        }
        if (!this.$v.formData.account_number.maxLength) {
          return this.$t('validation.account_number_maxLength')
        }
      },
      numCheckError() {},
      routingNumberError() {
        if (!this.$v.formData.routing_number.$error) {
          return ''
        }
        if (!this.$v.formData.routing_number.required) {
          return this.$tc('validation.required')
        }
        if (!this.$v.formData.routing_number.minLength) {
          return this.$t('validation.routing_number_minLength')
        }
        if (!this.$v.formData.routing_number.maxLength) {
          return this.$t('validation.routing_number_maxLength')
        }
      },
      getInputType() {
        if (this.isShowPassword) {
          return 'text'
        }
        return 'password'
      },
      getInputType1() {
        if (this.isShowPassword1) {
          return 'text'
        }
        return 'password'
      },
      getInputType2() {
        if (this.isShowPassword2) {
          return 'text'
        }
        return 'password'
      },
      codePayment() {
        return this.paymentPrefix + '-' + this.newPayment.paymentNumAttribute
      },
      invoice_number() {
        return this.invoice ? this.invoice.invoice_number : ''
      },
      voidStatusChange() {
        return this.formData.status.value === 'Void'
      },
      refundedStatusChange() {
        return this.formData.status.value === 'Refunded'
      },
    },
    watch: {    
      creditv(val) {
        this.formData.customer_credit = val
      },
      billing_country(newCountry) {
        if (newCountry) {
          this.authorize.country_id = newCountry.id
          this.authorize.country = newCountry.name
          this.isDisabledBillingState = false
        } else {
          this.authorize.country_id = null
        }
      },
      billing_state(newState) {
        if (newState) {
          this.authorize.state_id = newState.id
          this.authorize.state = newState.name
        } else {
          this.authorize.state_id = null
        }
      },
      /*
      customer(newValue) {
        this.isLoadingPayments = true
        this.formData.user_id = newValue.id
        this.formData.customcode = newValue.customcode
        this.creditv = false
  
        if (!this.isEdit) {
          if (this.isSettingInitialData) {
            this.isSettingInitialData = false
          } else {
            this.invoice = null
            this.formData.invoice_id = null
          }
          //this.formData.amount = 0
          this.invoiceList = []
          this.fetchCustomerInvoices(newValue.id)
          this.accountList = []
          this.fetchCustomerAccounts(newValue.id)
        }
      },
      */
      selectedNote() {
        if (this.selectedNote) {
          this.formData.notes = this.selectedNote
        }
      },
      /*
      invoice(newValue) {
        if (newValue) {
          this.formData.invoice_id = newValue.id
          this.authorize.invoice_number = newValue.invoice_number
          if (!this.isEdit) {
            this.setPaymentAmountByInvoiceData(newValue.id)
          }
          // en caso de que el monto de la factura sea menor que el credit del cliente
          if (this.customer.balance > 0 && !this.isEdit) {
            this.creditv = true
            //
            this.formData.payment_method = {}
            this.type_ach = false
            this.type_cc = false          
            this.formData.payment_gateways = []
          } else {
            this.creditv = false
            this.maxPayableAmount = Number.MAX_SAFE_INTEGER
          }
        } else {
          this.creditv = false
          this.formData.amount = 100 // 1
          this.maxPayableAmount = Number.MAX_SAFE_INTEGER
        }    
      },
      */
    },  
    async created()
    {      
        let response = await this.getPaymentMethods()
        if(response.data.success){
          this.payment_methods_module = response.data.payment_methods
        }
        this.fetchInitData()
    },
    async mounted() {
      this.$v.newPayment.$reset()
      this.resetSelectedNote()
      this.$nextTick(() => {
      this.loadData()
      })
    },
    methods: {
      ...mapActions('invoice', [
        'fetchInvoice',
        'fetchInvoices',
        'fetchInvoicespayments',
      ]),
  
      ...mapActions('paymentAccounts', [
        'fetchPaymentAccounts',
        'fetchPaymentAccount',
      ]),
  
      ...mapActions('payment', [
        'addPayment',
        'updatePayment',
        'fetchPayment',
        'fetchPaymentModes',
        'resetSelectedNote',
      ]),

      ...mapActions('paymentMultiple', ['addMultiplePayment', 'fetchMultiplePayment', 'getPaymentMethods']),

      ...mapActions('paymentGateways', ['fetchPaymentGateways']),
  
      ...mapActions('authorizations', [
        'addAuthorize',
        'saveAuthorizeDB',
        'voidAuthorize',
        'refundedAuthorize',
        'addAuthorizeACH',
        'saveAuthorizeACH',
        'addAuthorizePaypal',
        'chargePaypalPro',
      ]),
  
      ...mapActions('company', ['fetchCompanySettings']),
  
      ...mapActions('modal', ['openModal']),
  
      ...mapActions('customer', ['fetchCustomers', 'fetchCustomer']),
  
      ...mapActions('failedPaymentHistory', ['addFailedPaymentHistory']),
  
      addItem() {
        this.newPayment.payment_methods.push({
            ...PaymentMethodStub,
            id: Guid.raw(),
        })
        //this.searchItems()
      },      

      removePaymentMethod(index) {
        this.newPayment.payment_methods.splice(index, 1)
      },

      updatePaymentMethod(data) {
        Object.assign(this.newPayment.payment_methods[data.index], { ...data.payment_method })

        if(this.amount > this.maxPayableAmount)
        {
          this.indexError = data.index
          this.errorPmAmount = true
        }else{
          this.errorPmAmount = false 
        } 
          
      },

      checkPaymentMethodData(index, isValid) {
        this.newPayment.payment_methods[index].valid = isValid
      },        
     
      async paymentWithCustomerBalance(){
  
        let validate = await this.touchCustomField()
  
        this.$v.customer.$touch()
        this.$v.formData.$touch()
  
        if (this.formData.amount == 0)
        {
          window.toastr['error'](
            this.$t('general.invalid_form_amount')
          )
          return false
        }
  
        if (this.$v.$invalid) {
          return false
        }
  
        this.isLoading = true
  
        swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('payments.transaction_status_message'),
          icon: 'warning',
          buttons: true,
        }).then(async (result) => {
  
        if(result)
        {
          this.formData.payment_number =
            this.paymentPrefix + '-' + this.newPayment.paymentNumAttribute
  
          this.formData.transaction_status = this.formData.status.value
  
          if (this.creditv && (this.formData.amount / 100 > this.customer.balance))
          {       
              this.formData.amount = this.customer.balance * 100
          }
  
          let data = {
            ...this.formData,
            payment_method_id: this.formData.payment_method
              ? this.formData.payment_method.id
              : null,
            payment_date: moment(this.newPayment.payment_date).format(
              'YYYY-MM-DD'
            ),
          }
  
          let response = await this.addPayment(data)
  
          if (response.data.success)
          {
            if(this.invoice != null)
            {
              if( this.invoice.is_invoice_pos == '1'){
              this.$router.push(`/admin/invoices/${this.invoice.id}/view`)
              window.toastr['success'](this.$t('payments.created_message'))
              this.isLoading = false
              return true
              }else {          
                this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
                window.toastr['success'](this.$t('payments.created_message'))
                this.isLoading = false
                return true
              }
            }else{
              this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
              window.toastr['success'](this.$t('payments.created_message'))
              this.isLoading = false
              return true
            }        
          }
  
          if (response.data.error === 'invalid_amount') {
            window.toastr['error'](this.$t('invalid_amount_message'))
            return false
          }
  
          window.toastr['error'](response.data.error)
  
          /*
          if(response.data.success && response.data.customer.balance > 0)
          {
            this.$router.go(0)
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = true
            return
          }
  
          if (response.data.success) {
            this.$router.push(
              `/customer/payments/${response.data.payment.id}/view`
            )
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = false
            return true
          }
  
          if (response.data.error === 'invalid_amount') {
            window.toastr['error'](this.$t('invalid_amount_message'))
            return false
          }*/
        }else{
          this.isLoading = false
        }
  
      })
      },
  
      invoiceWithAmount({ invoice_number, due_amount }) {
        if (invoice_number) {
          return `${invoice_number} (${this.$utils.formatGraphMoney(
            due_amount,
            this.customer.currency
          )})`
        } else {
          return '- Seleccione -'
        }
      },
  
      setpaymentModes(id) {
        this.paymentModes = this.paymentModes.find((c) => {
          return c.id == id
        })
      },
  
      async countrySelected(country, type) {
        const vm = this
        vm.isLoading = true
        if (type == 'billing') {
          vm.billing_states = []
        }
        let res = await window.axios.get('/api/v1/states/' + country.code)
        if (res) {
          if (type == 'billing') {
            vm.billing_states = res.data.states
          }
        }
        vm.isLoading = false
      },
  
      async loadCustomerData(params) {
        //console.log(params)
        let response = await this.fetchCustomer(params)
        this.authorize.payer_email = response.data.customer.email
  
        this.authorize.name = null
        this.authorize.country_id = null
        this.authorize.state_id = null
        this.authorize.city = null
        this.authorize.phone = null
        this.authorize.zip = null
        this.authorize.address_street_1 = null
        this.authorize.address_street_2 = null
        this.authorize.country = null
        this.authorize.state = null
        this.authorize.first_name = null
        this.authorize.last_name = null
        this.authorize.company_name = null
        this.billing_country = null
        this.billing_state = null
  
        this.authorize.first_name = response.data.customer.first_name
        this.authorize.last_name = response.data.customer.last_name
        this.authorize.company_name = response.data.customer.company.name
        this.authorize.email = response.data.customer.email
  
        if (response.data.customer.billing_address) {
          this.authorize.name = response.data.customer.contact_name
          this.authorize.country_id =
            response.data.customer.billing_address.country_id
          this.authorize.state_id =
            response.data.customer.billing_address.state_id
          this.authorize.city = response.data.customer.billing_address.city
          this.authorize.phone = response.data.customer.billing_address.phone
          this.authorize.zip = response.data.customer.billing_address.zip
          this.authorize.address_street_1 =
            response.data.customer.billing_address.address_street_1
          this.authorize.address_street_2 =
            response.data.customer.billing_address.address_street_2
          this.authorize.country =
            response.data.customer.billing_address.country.name
          this.authorize.state = response.data.customer.billing_address.state.name
  
          if (response.data.customer.billing_address.country_id) {
            this.billing_country = response.data.customer.billing_address.country
            this.countrySelected(this.billing_country, 'billing')
          }
          if (response.data.customer.billing_address.state_id) {
            this.billing_state = response.data.customer.billing_address.state
          }
        }
      },
      async addPaymentMode() {
        this.openModal({
          title: this.$t('settings.customization.payments.add_payment_mode'),
          componentName: 'PaymentMode',
        })
      },
      async checkAutoGenerate() {

        let response = await this.fetchCompanySettings(['payment_auto_generate'])
  
        let response1 = await axios.get('/api/v1/next-number?key=payment')
  
        if (response.data && response.data.payment_auto_generate === 'YES')
        {
          if (response1.data)
          {
            this.newPayment.paymentNumAttribute = response1.data.nextNumber
            this.newPayment.paymentPrefix = response1.data.prefix
            return true
          }
        } else {
          this.paymentPrefix = response1.data.prefix
        }
      },
      async loadData() {
        
        if (this.isEdit) {

          this.isRequestOnGoing = true
          
          let response = await this.fetchMultiplePayment(this.$route.params.id)          

          this.newPayment = { ...this.newPayment, ...response.data.payment }

          //this.amount =
          this.newPayment.paymentPrefix = response.data.payment_prefix
          this.newPayment.paymentNumAttribute = response.data.nextPaymentNumber

          if (response.data.payment.user_id)
          {
            await this.fetchCustomers({ limit: 'all' })
          }
          this.selectedCurrency = this.defaultCurrency
          this.setCustomer(parseInt(response.data.payment.user_id))
          this.setInvoice(parseInt(response.data.payment.invoice_id))     

          this.isRequestOnGoing = false

          /*
          // Payment Authorize
          this.isPaymentTypeAuthorize =
            response.data.payment.authorize_id != null ? true : false
  
          this.formData = { ...this.formData, ...response.data.payment }
  
          if (this.formData.payment_method_id) {
            // console.log(this.formData)
            this.fetchingPaymentMethod = true
            let payments = await this.fetchPaymentModes({ limit: 'all' })   
            
            
            this.formData.payment_method =
              payments.data.paymentMethods.data.filter(
                (payment) => payment.id == this.formData.payment_method_id
              )
            
            //this.PaymentModeSelected(this.formData.payment_method[0])
  
            this.PaymentModeSelected(this.formData.payment_method)
            this.fetchingPaymentMethod = false
          }
          //return false;
          if (this.formData.user_id) {
            await this.fetchCustomers({ limit: 'all' })
          }
          this.customer = response.data.payment.user
          this.newPayment.payment_date = moment(
            response.data.payment.payment_date
          ).format('YYYY-MM-DD')
          this.formData.credit_cards = response.data.payment.credit_card
          this.formData.amount = parseFloat(response.data.payment.amount)
          this.paymentPrefix = response.data.payment_prefix
          this.newPayment.paymentNumAttribute = response.data.nextPaymentNumber
          // this.formData.payment_method = response.data.payment.payment_method
          this.formData.payment_method_id =
            response.data.payment.payment_method_id
          // console.log(this.formData);
  
          // console.log(this.formData.payment_method)
  
          if (response.data.payment.invoice !== null) {
            this.maxPayableAmount =
              parseInt(response.data.payment.amount) +
              parseInt(response.data.payment.invoice.due_amount)
            this.invoice = response.data.payment.invoice
          }
  
          if (response.data.payment.credit_card) {
            let type_name = ''
  
            switch (this.formData.credit_cards) {
              case 'AMERICAN EXPRESS':
                type_name = 'AMERICAN EXPRESS'
                break
              case 'VISA':
                type_name = 'VISA'
                break
              case 'MASTERCARD':
                type_name = 'MASTERCARD'
                break
              case 'DISCOVER':
                type_name = 'DISCOVER'
                break
              default:
                break
            }
            this.formData.credit_cards = {
              name: type_name,
              value: this.formData.credit_cards,
            }
          }
  
          const findIndex = this.status.findIndex(
            (item) => item.value == this.formData.transaction_status
          )
  
          if (findIndex !== -1) {
            this.formData.status = this.status[findIndex]
          }
  
          if (
            this.invoice != null &&
            this.formData.payment_method &&
            this.formData.transaction_status == 'Approved'
          ) {
            // console.log('entrow con mmetodo 1 invoice')
            // console.log(this.formData.payment_method)
            let gateway = 0
            let typegateway = 'N'
            let payments = this.formData.payment_method
            Array.prototype.forEach.call(payments, (user) => {
              // ...
              // console.log(user)
  
              if (user.add_payment_gateway == 1 && user.account_accepted != 'N') {
                this.status = [
                  { value: 'Approved', text: 'Approved' },
                  { value: 'Void', text: 'Void' },
                  { value: 'Refunded', text: 'Refunded' },
                  { value: 'Unapply', text: 'Unapply' },
                ]
              }
  
              if (
                user.add_payment_gateway == null ||
                user.add_payment_gateway == 0 ||
                user.account_accepted == 'N'
              ) {
                this.status = [
                  { value: 'Approved', text: 'Approved' },
  
                  { value: 'Unapply', text: 'Unapply' },
                ]
              }
            })
            // console.log(this.formData.payment_method)
          }
  
          if (
            this.invoice != null &&
            this.formData.payment_method == null &&
            this.formData.transaction_status == 'Approved'
          ) {
            //console.log('entro sin metodo')
            this.status = [
              {
                value: 'Approved',
                text: 'Approved',
              },
              {
                value: 'Unapply',
                text: 'Unapply',
              },
            ]
          }
  
          if (
            this.invoice == null &&
            this.formData.payment_method &&
            this.formData.transaction_status == 'Approved'
          ) {
            //console.log('entrow con mmetodo sin invoice')
            //console.log(this.formData.payment_method)
            this.status = [
              { value: 'Approved', text: 'Approved' },
              { value: 'Void', text: 'Void' },
            ]
          }
  
          if (this.formData.transaction_status != 'Approved') {
            this.notEditable = true
            this.isedtiablefalse = true
            this.status = [
              {
                value: this.formData.transaction_status,
                text: this.formData.transaction_status,
              },
              {
                value: this.formData.transaction_status,
                text: this.formData.transaction_status,
              },
            ]
          }
  
          let res = await this.fetchCustomFields({
            type: 'Payment',
            limit: 'all',
          })
  
          this.setEditCustomFields(
            response.data.payment.fields,
            res.data.customFields.data
          )
          //console.log('Console final')
          //console.log(this.formData.payment_method)
          this.isRequestOnGoing = false
          */
        } else {
          this.isRequestOnGoing = true
          this.checkAutoGenerate()
          this.setInitialCustomFields('Payment')
          this.newPayment.payment_date = moment().format('YYYY-MM-DD')
          this.fetchPaymentModes({ limit: 'all' })
          await this.fetchCustomers({ limit: 'all' })          
          if (this.$route.params)
          {
            this.setCustomer(parseInt(this.$route.params.customer_id))
            this.setInvoice(parseInt(this.$route.params.invoice_id))
          }
          this.selectedCurrency = this.defaultCurrency
          this.isRequestOnGoing = false
        }
        return true
      },
      async fetchInitData() {
        this.initLoad = true
        let res = await window.axios.get('/api/v1/countries')
        if (res) {
          this.countries = res.data.countries
        }
        this.initLoad = false

        //
        let response = await this.fetchCompanySettings([
          'allow_partial_pay'
        ])

        if(response.data.hasOwnProperty('allow_partial_pay'))
        {
          this.allowPartialPay = response.data.allow_partial_pay == "1" ? true : false         
        }else{
          this.allowPartialPay = false
        }
        //
      },

      // Set Params
      async setCustomer(id) {
        this.customer = this.customers.find((c) => {
          return c.id === id
        })
      },
      async setInvoice(id) {
        let invoice = await this.fetchInvoice(id)

        if(invoice){
            this.invoice = { ...invoice.data.invoice }
            this.maxPayableAmount = invoice.data.invoice.due_amount            
        }

      },
      //
      /*
      async setPaymentAmountByInvoiceData(id) {
        let data = await this.fetchInvoice(id)      
        if(this.creditv && (data.data.invoice.due_amount / 100 > this.customer.balance))
        {  
          this.formData.amount = this.customer.balance * 100
          this.maxPayableAmount = this.customer.balance * 100
          this.maxAmountIsNotCustomerCreditBalance = data.data.invoice.due_amount
        }else{       
          this.formData.amount = data.data.invoice.due_amount
          this.maxPayableAmount = data.data.invoice.due_amount
          this.maxAmountIsNotCustomerCreditBalance = data.data.invoice.due_amount
        }    
      },
      */
      async fetchCustomerInvoices(userId) {
        let data = {
          customer_id: userId,
          status: 'UNPAID',
        }
        let response = await this.fetchInvoicespayments(data)
        /* this.invoiceList = [{
          id: null,
          invoice_number: null,
          due_amount: null
        }] */
        response.data.invoices.data.forEach((element) => {
          this.invoiceList.push(element)
        })
  
        // console.log('invoice list: ', this.invoiceList);
      },
  
      async fetchCustomerAccounts(userId) {
        let data = {
          customer_id: userId,
          status: 'UNPAID',
        }
        let response = await this.fetchPaymentAccounts(data)
        this.accountList = response.data.payment_accounts.data
          .filter((el) => {
            return el.payment_account_type == 'ACH'
          })
          .map((el) => {
            const auxAccountNumber = el.account_number.toString().split('')
            let showAccountNumber = ''
            const limit = auxAccountNumber.length - 4
            auxAccountNumber.forEach((el, i) => {
              if (i < limit) showAccountNumber = showAccountNumber + '*'
              else showAccountNumber = showAccountNumber + el
            })
            return {
              ...el,
              name_account_number: el.bank_name + ' - ' + showAccountNumber,
            }
          })
        this.cardList = response.data.payment_accounts.data
          .filter((el) => {
            return el.payment_account_type == 'CC'
          })
          .map((el) => {
            const auxCardNumber = el.card_number.toString().split('')
            let showCardNumber = ''
            const limit = auxCardNumber.length - 4
            auxCardNumber.forEach((el, i) => {
              if (i < limit) showCardNumber = showCardNumber + '*'
              else showCardNumber = showCardNumber + el
            })
            return {
              ...el,
              card_number_cvv: el.first_name + ' - ' + showCardNumber,
            }
          })
        this.isLoadingPayments = false
      },
  
      async PaymentModeSelected(val) {
  
        this.creditv = false
        if(this.invoice != null){
          this.formData.amount = this.maxAmountIsNotCustomerCreditBalance
          this.maxPayableAmount = this.maxAmountIsNotCustomerCreditBalance
        }
        
        
        this.$v.customer.$touch()
        /*
        if(val.paypal_button == 1) this.$v.invoice.$touch()
        if(val.account_accepted === 'A') this.$v.invoice.$touch()
        if(val.account_accepted === 'C') this.$v.invoice.$touch()
        */
              
        if (val.account_accepted === 'A') {
          this.type_ach = true
          this.type_cc = false
          let params = {}
          if (this.customer) {
            params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
          }
        } else if (val.account_accepted === 'C') {
          this.type_cc = true
          this.type_ach = false
        } else {
          this.type_cc = false
          this.type_ach = false
        }
        let band = false
        if (val.add_payment_gateway === 1) {
          let res = await this.fetchPaymentGateways()
          if (res) {
            this.payment_gateways = res.data.payment_gateways
            this.payment_gateways.forEach((element) => {
              if (
                element.id === val.payment_gateways_id &&
                val.payment_gateways_id === 1
              ) {
                this.formData.payment_gateways = element
              }
  
              if (element.default == 1) {
                this.formData.payment_gateways = element
              }
            })
            this.add_payment_gateway_select = true
            //console.log('paso2')
            //console.log(this.add_payment_gateway_select)
            band = true
          }
        } else {
          this.add_payment_gateway_select = false
          this.formData.payment_gateways = null
          this.is_authorize = false
          //return false
        }
        if (this.type_ach && this.account) {
          this.account = null
          this.formData.ACH_type = null
          this.formData.account_number = null
          this.formData.routing_number = null
          this.formData.num_check = null
          this.formData.bank_name = null
          this.authorize.name = null
          this.billing_country = null
          this.billing_state = null
          this.authorize.city = null
          this.authorize.address_street_1 = null
          this.authorize.address_street_2 = null
          this.authorize.zip = null
          this.billing_states = []
        }
        if (
          this.card &&
          ((this.is_authorize && this.isEdit && !this.type_ach && this.type_cc) ||
            (this.is_paypal && this.isEdit && !this.type_ach && this.type_cc) ||
            (this.is_authorize &&
              !this.isEdit &&
              !this.type_ach &&
              this.type_cc) ||
            (this.is_paypal && !this.isEdit && !this.type_ach && this.type_cc))
        ) {
          this.card = null
          //this.formData.payment_gateways = null
          this.authorize.card_number = null
          this.formData.credit_cards = null
          this.authorize.payer_email = null
          this.authorize.cvv = null
          this.authorize.date = null
          this.authorize.name = null
          this.billing_country = null
          this.billing_state = null
          this.authorize.city = null
          this.authorize.address_street_1 = null
          this.authorize.address_street_2 = null
          this.authorize.zip = null
          this.billing_states = []
        }
        return band
        // if (val.name === 'Credit Card') {
        //   let res = await this.fetchPaymentGateways()
        //   if (res) {
        //     this.payment_gateways = res.data.payment_gateways
        //     this.payment_gateways.forEach(element => {
        //       if (element.default) {
        //         this.formData.payment_gateways = element
        //       }
        //     });
        //     this.add_payment_gateway_select = true
        //     return this.add_payment_gateway_select;
        //   }
        // } else if (this.formData.payment_method != 'Credit Card') {
        //   this.add_payment_gateway_select = false
        //   this.formData.payment_gateways = null
        //   this.is_authorize = false
        //   return this.add_payment_gateway_select;
        // }
      },
  
      async transactionStatusSelected(val) {
        //console.log('entroaqui')
        if (val.text === 'Void' || val.text === 'Refunded') {
          this.formData.applied_credit_customer = false
          this.formData.status_with_authorize = true
          this.transactionStatusCheck = true
          this.transactionStatusCheckUnapply = false
        } else if (val.text === 'Unapply') {
          this.formData.applied_credit_customer = true
          this.formData.status_with_authorize = false
          this.transactionStatusCheck = false
          this.transactionStatusCheckUnapply = true
        } else {
          this.formData.applied_credit_customer = false
          this.formData.status_with_authorize = false
          this.transactionStatusCheckUnapply = false
          this.transactionStatusCheck = false
        }
      },
  
      async submitPaymentMultiple() {

        if (this.amount > this.maxPayableAmount)
        {
          window.toastr['error']('The payment amount should not be greater than due amount.')
          window.hub.$emit('checkItems')
          return false
        }

        if(!this.allowPartialPay && (this.amount != this.maxPayableAmount))
        {
          window.toastr['error']('The configuration does not allow partial payments, you have to complete the amount of the invoice.')
          return false
        }     

        let validate = await this.touchCustomField()

        if (!this.checkValid() || validate.error)
        {
          return false
        }
        
        swal({
          title: this.$t('general.are_you_sure'),
          text: 'The payment will be created',
          icon: '/assets/icon/file-alt-solid.svg',
          buttons: true,
          dangerMode: true,
        }).then(async (result) => {
          if (result) {
            

            //if(this.isEdit)
            //{

            //}else{
              // creando Payment Multiple
              let data = {
                ...this.newPayment,
                is_multiple: true,
                amount: this.amount,
                payment_number: this.newPayment.paymentPrefix + '-' + this.newPayment.paymentNumAttribute,
                user_id: this.customer.id,
                invoice_id: this.invoice.id            
              }

              //aqui el action para guardar
            
              try {

                let response = await this.addMultiplePayment(data)

                if(response.data.success)
                {
                  if(this.go_to_invoice)
                  {
                    this.$router.push(`/admin/invoices/${response.data.payment.invoice_id}/view`)
                    window.toastr['success'](this.$t('payments.created_message'))
                  }else{
                    this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
                    window.toastr['success'](this.$t('payments.created_message'))
                  }                
                }else{
                    window.toastr['error']('Error')
                }
                
              } catch (error) {
                if(error.response.data.errors.hasOwnProperty('payment_number'))
                {
                  if(error.response.data.errors.payment_number[0] == 'Invalid number passed.')
                  {                    
                    this.alertPaymentNumberAlreadyExists()                   
                  } 
                }                
              }
          }
        })

      },
      // Payment Number Exists
      alertPaymentNumberAlreadyExists()
      {        
        this.$swal({
          title: this.$t('general.payment_number_exists_title'),          
          text:  this.$t('general.payment_number_exists_text'),          
          icon: 'warning',
          showCancelButton: true,          
          confirmButtonText: this.$t('general.automatic'), 
          confirmButtonColor: "#5851D8",
          cancelButtonText: this.$t('general.manual'),
          //cancelButtonColor: "#efefef", 
          //showCloseButton: true,
          showLoaderOnConfirm: true
          }).then((result) => {
            if(result.value)
            {
              this.generateAutomaticPaymentNumber()              
            } 
        })        
      },

      async generateAutomaticPaymentNumber()
      {    
        let response_next_number = await axios.get('/api/v1/next-number?key=payment')

        this.newPayment.paymentNumAttribute = response_next_number.data.nextNumber

        let data = {
          ...this.newPayment,
          is_multiple: true,
          amount: this.amount,
          payment_number: this.newPayment.paymentPrefix + '-' + this.newPayment.paymentNumAttribute,
          user_id: this.customer.id,
          invoice_id: this.invoice.id            
        }     

        let response = await axios.post('/api/v1/payments/multiple/create', data)

        if(response.data.success)
        {
            if(this.go_to_invoice)
            {
              this.$router.push(`/admin/invoices/${response.data.payment.invoice_id}/view`)
              window.toastr['success'](this.$t('payments.created_message'))
            }else{
              this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
              window.toastr['success'](this.$t('payments.created_message'))
            }                
          }else{
              window.toastr['error']('Error')
        }        
      },
      //

      checkValid() {

      this.$v.newPayment.$touch()
      this.$v.customer.$touch()
      this.$v.invoice.$touch()

      window.hub.$emit('checkItems')
      let isValid = true

      
      if (this.newPayment.payment_methods.length > 0)
      {
        this.newPayment.payment_methods.forEach((payment_method) => {
          if (!payment_method.valid) {
            isValid = false
          }
        })
      }

      /*
      if(this.newPayment.payment_methods.length == 1 && !isValid){
        window.toastr['error']('You must select at least 1 payment method')
        return
      }*/

      if (
        this.$v.newPayment.$invalid === false &&
        isValid === true
      ) {
        return true
      }
      return false
    },

      onSelectNote(data) {
        this.formData.notes = '' + data.notes
        this.$refs.notePopup.close()
      },
      Updateoptionchace(val) {
        this.updatebillinginformation = val
        //console.log(this.updatebillinginformation)
        this.formData.updatebillinginformation = val ? true : false
        //console.log(this.formData)
      },
  
      Createoptionchace(val) {
        this.createaccount = val
        //console.log(this.createaccount)
        this.formData.createaccount = val ? true : false
        //console.log(this.formData)
      },
  
      async selectItemAccount(item) {
        this.formData.ACH_type = this.bank_account_type.find((el) => {
          return (
            el.value.toString().toLowerCase() ==
            item.ACH_type.toString().toLowerCase()
          )
        })
        this.formData.account_number = item.account_number
        this.formData.routing_number = item.routing_number
        this.formData.num_check = item.num_check
        this.formData.bank_name = item.bank_name
        this.authorize.name = item.first_name
        this.billing_country = this.countries.find((el) => {
          return el.id == item.country_id
        })
        if (this.billing_country) {
          await this.countrySelected(this.billing_country, 'billing')
        }
        this.billing_state = this.billing_states.find((el) => {
          return el.id == item.state_id
        })
        this.authorize.city = item.city
        this.authorize.address_street_1 = item.address_1
        this.authorize.address_street_2 = item.address_2
        this.authorize.zip = item.zip
      },
  
      async selectItemCard(item) {
        //console.log('item', item)
        //this.formData.payment_gateways = null
        this.authorize.card_number = item.card_number
        //console.log(this.authorize.card_number)
        this.formData.credit_cards = { name: item.credit_card }
        //this.authorize.payer_email = null
        this.authorize.cvv = item.cvv
        if (item.expiration_date) {
          this.authorize.date = item.expiration_date
        }
        this.authorize.name = item.first_name
        this.billing_country = this.countries.find((el) => {
          return el.id == item.country_id
        })
        if (this.billing_country) {
          await this.countrySelected(this.billing_country, 'billing')
        }
        this.billing_state = this.billing_states.find((el) => {
          return el.id == item.state_id
        })
        this.authorize.city = item.city
        this.authorize.address_street_1 = item.address_1
        this.authorize.address_street_2 = item.address_2
        this.authorize.zip = item.zip
        //console.log('date', this.authorize.date)
      },
      async paypalSuccess(payment_paypal_id) {
        this.isRequestOnGoing = true
        this.paymentPaypalProccess = true
        this.formData.payment_number = this.codePayment
        let data = {
          ...this.formData,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.newPayment.payment_date).format('YYYY-MM-DD'),
          payment_paypal_id,
        }
  
        this.isLoading = true
        let response = await this.addPayment(data)
        this.isRequestOnGoing = false
  
        if (response.data.success)
        {
          if(this.invoice != null)
          {
            if( this.invoice.is_invoice_pos == '1'){
            this.$router.push(`/admin/invoices/${this.invoice.id}/view`)
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = false
            return true
          }else {          
            this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = false
            return true
            }
          }else{
            this.$router.push(`/admin/payments/${response.data.payment.id}/view`)
            window.toastr['success'](this.$t('payments.created_message'))
            this.isLoading = false
            return true
          }        
        }
        if (response.data.error === 'invalid_amount') {
          window.toastr['error'](this.$t('invalid_amount_message'))
          this.isLoading = false
          return false
        }
        window.toastr['error'](response.data.error)
      },
    },
  }
  </script>
  