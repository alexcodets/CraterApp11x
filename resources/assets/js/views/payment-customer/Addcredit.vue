<style>
/* Estilos personalizados para el componente sw-select y sus elementos */
.custom-sw-select {
  font-size: 14px; /* Tamaño de letra más grande */
}

.custom-option {
  padding: 7px; /* Espaciado interno */
}

.custom-option:nth-child(even) {
  background-color: #f4f4f4; /* Alternar colores de fondo */
}

.custom-option:hover,
.selected-option {
  background-color: #e2e8f0; /* Color de fondo al pasar el mouse o seleccionar */
}

.custom-option span {
  margin-left: 0.5rem;
  font-size: 1.25rem; /* Tamaño del texto de las opciones */
}

.custom-checkbox {
  accent-color: #4299e1; /* Color del checkbox */
}

.button-group {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-bottom: 10px;
}

.button-all {
  color: #ffffff;
  background-color: #008631;
  /* Resto de estilos específicos para este botón */
}

.button-sent {
  color: #ffffff;
  background-color: #008631;
  /* Resto de estilos específicos para este botón */
}

.button-due {
  color: #744210;
  background-color: #f8edcb;
  /* Resto de estilos específicos para este botón */
}

.button-overdue {
  color: #c53030;
  background-color: #fed7d7;
  /* Resto de estilos específicos para este botón */
}

.sw-button {
  border: none;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 4px;
  font-weight: bold;
}

.sent-status {
  color: #ffffff !important;
  background-color: #008631 !important;
}

.due-status {
  color: #744210 !important;
  background-color: #f8edcb !important;
}

.overdue-status {
  color: #c53030 !important;
  background-color: #fed7d7 !important;
}

.multiselect .multiselect__option--highlight.multiselect__option--selected {
  /*padding: 1px !important;;*/
}

.multiselect .multiselect__option--highlight {
  /*padding: 1px !important;;*/
}

.multiselect .multiselect__option {
  /* padding: 1px !important;;*/
}

.button-active {
  font-size: 1.2em; /* Texto más grande */
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); /* Sombreado */
  /* Otros estilos para resaltar el botón activo */
}
</style>


<style scoped>
/* Estilos para elementos seleccionados con estado "SENT" */
.multiselect__tag.status-SENT {
  color: #ffffff !important;
  background-color: #008631 !important;
}

/* Estilos para elementos seleccionados con estado "DUE" */
.multiselect__tag.status-DUE {
  color: #744210 !important;
  background-color: #f8edcb !important;
}

/* Estilos para elementos seleccionados con estado "OVERDUE" */
.multiselect__tag.status-OVERDUE {
  color: #c53030 !important;
  background-color: #fed7d7 !important;
}

.multiselect__option .multiselect__tag.status-SENT {
  background-color: #008631 !important;
}

.multiselect__option .multiselect__tag.status-SENT > span {
  background-color: inherit; /* Hereda el color de fondo de la etiqueta multiselect__tag */
}

.multiselect__option .multiselect__tag.status-DUE {
  background-color: #f8edcb !important;
}

.multiselect__option .multiselect__tag.status-DUE > span {
  background-color: inherit; /* Hereda el color de fondo de la etiqueta multiselect__tag */
}
.multiselect__option .multiselect__tag.status-OVERDUE {
  background-color: #fed7d7 !important;
}

.multiselect .multiselect__option--highlight.multiselect__option--selected {
  padding: 1px !important;
}
</style>


<template>
  <base-page class="relative payment-create">
    <form action="" @submit.prevent="submitPaymentData" id="payment-form">
      <sw-page-header :title="pageTitle" class="mb-5">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/customer/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('payments.payment', 2)"
            to="/customer/payments"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'payments.edit'"
            :title="$t('payments.edit_payment')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('customers.add_credit')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions"> </template>
      </sw-page-header>

      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <div v-if="!isShowIdentificationVerification">
        <sw-card  v-if="!isRequestOnGoing">
        <div class="grid-cols-12 gap-8 mt-1 mb-2 lg:grid">
          <!-- First Section -->
          <div
            class="grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-3"
          >
            <!-- date -->
            <sw-input-group
              :label="$t('payments.date')"
              :error="DateError"
              required
            >
              <base-date-picker
                v-model="formData.payment_date"
                :invalid="$v.formData.payment_date.$error"
                :calendar-button="true"
                class="mt-1"
                calendar-button-icon="calendar"
                :disabled="true"
                @input="$v.formData.payment_date.$touch()"
              />
            </sw-input-group>

            <!-- invoice -->
            <!--      <sw-input-group :label="$t('payments.invoice_title')">
<div class="button-group">
  <sw-button type="button" class="button-all" :class="{ 'button-active': activeButton === 'ALL' }" @click="filtrarYSeleccionarInvoices('ALL')">{{ $t('general.all') }}</sw-button>
<sw-button type="button" class="button-sent" :class="{ 'button-active': activeButton === 'SENT' }" @click="filtrarYSeleccionarInvoices('SENT')">{{ $t('general.sent') }}</sw-button>
<sw-button type="button" class="button-due" :class="{ 'button-active': activeButton === 'VIEWED' }" @click="filtrarYSeleccionarInvoices('VIEWED')">{{ $t('general.due') }}</sw-button>
<sw-button type="button" class="button-overdue" :class="{ 'button-active': activeButton === 'OVERDUE' }" @click="filtrarYSeleccionarInvoices('OVERDUE')">{{ $t('general.overdue') }}</sw-button>
</div>
<sw-select
v-model="selectedInvoices"
:options="invoiceList"
:multiple="true"
:searchable="true"
:show-labels="false"
:allow-empty="true"
:placeholder="$t('invoices.select_invoice')"
:custom-label="invoiceWithAmount"
class="custom-sw-select mt-1"
track-by="invoice_number"
label="invoice_number"
:search-keys="['invoice_number']"
>
<template #option="{ option, selected, select }">
  <div
    :class="['custom-option', 'multiselect__tag', getStatusClass(option.status), selected ? 'selected' : '']"
  >
    <input
      type="checkbox"
      :value="option"
      v-model="selectedInvoices"
      @change="select(option)"
      class="custom-checkbox"
    />
    <span>{{ invoiceWithAmount(option) }}</span>
  </div>
</template>
</sw-select>
</sw-input-group> -->

            <!-- amount -->
            <sw-input-group
              :label="$t('payments.credit_amount')"
              :error="amountError"
              required
            >
              <div class="relative w-full mt-1">
                <sw-money
                  v-model="amount"
                  :currency="customerCurrency"
                  :invalid="$v.formData.amount.$error"
                  :disabled="isEdit"
                  class="relative w-full focus:border focus:border-solid focus:border-primary-500"
                  @input="$v.formData.amount.$touch()"
                />
              </div>
            </sw-input-group>

            <sw-input-group >
              <div class="">
                <span class="flex flex-wrap justify-start"
                  >{{ $t('payments.account_avalable_credit') }}:

                </span>

                <div
                    class="text-success text-xl ml-2"
                    v-html="
                      $utils.formatMoney(
                        customer.balance * 100,
                        customer.currency
                      )
                    "
                  />
                <!-- boton Apply Credit -->

               <!--  <sw-button
                  class="flex flex-wrap justify-end"
                  :loading="isLoading"
                  :disabled="isLoading"
                  variant="primary"
                  type="button"
                  @click="paymentWithCustomerBalance"
                >
                  {{ $t('payments.apply_credit') }}
                </sw-button>-->
              </div>
            </sw-input-group>
          </div>

          <sw-divider class="my-0 col-span-12 opacity-1" />

          <!-- Second Section -->
          <div
            v-if="customerHasPaymentMethods"
            class="pt-2 pb-4 grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-3"
          >
            <div>
              <sw-input-group :label="$t('payments.payment_mode')">
                <sw-select
                  v-model="formData.payment_method"
                  :options="options_payment_methods"
                  :searchable="true"
                  :show-labels="false"
                  :placeholder="$t('payments.select_payment_mode')"
                  :max-height="150"
                  label="name"
                  class="mt-1"
                  :error="paymentMethodError"
                  @select="PaymentModeSelected"
                  required
                >
                </sw-select>
              </sw-input-group>
            </div>

            <!-- Paypal -->
            <div
              class="mt-5"
              v-if="
                formData.payment_method &&
                formData.payment_method.paypal_button &&
                customer != null
              "
            >
              <paypal
                :formData="formData"
                :codePayment="codePayment"
                :invoice_number="invoice_number"
                :customer="customer"
                @paypalSuccess="paypalSuccess"
              ></paypal>
            </div>

            <!-- CC and ACH-->
            <div class="mt-1 ml-12">
              <!-- type_cc and billing -->
              <div
                v-if="
                  this.add_payment_gateway_select &&
                  !this.isEdit &&
                  !this.type_ach &&
                  this.type_cc &&
                  !isLoadingThePaymentAccount &&
                  paymentAccountPreview != null
                "
              >
                <div
                  style="
                    display: flex;
                    justify-content: start;
                    align-items: center;
                  "
                >
                  <img
                    :src="paymentAccountPreview.src"
                    :width="paymentAccountPreview.width"
                  />
                  <p style="padding: 10px">
                    <b>
                      {{ paymentAccountPreview.credit_card }}
                    </b>
                    {{ $t('payment_accounts.ending_in') }}
                    {{ paymentAccountPreview.card_number }}
                  </p>
                </div>

                <div
                  style="
                    display: flex;
                    justify-content: start;
                    align-items: center;
                  "
                >
                  <p style="padding-left: 0; padding-right: 0px">
                    <b style="color: #3939ff"
                      >{{ $t('customers.billing_address') }}:</b
                    >
                    {{ billingAddressPreview }}
                  </p>
                </div>
              </div>

              <!-- type_cc and billing -->
              <div
                v-if="
                  this.add_payment_gateway_select &&
                  !this.isEdit &&
                  this.type_ach &&
                  !this.type_cc &&
                  !isLoadingThePaymentAccount &&
                  paymentAccountPreview != null
                "
              >
                <div
                  style="
                    display: flex;
                    justify-content: start;
                    align-items: center;
                  "
                >
                  <img
                    :src="paymentAccountPreview.src"
                    :width="paymentAccountPreview.width"
                  />
                  <p style="padding: 10px">
                    <b>
                      {{ paymentAccountPreview.account_number_name }}
                    </b>
                    {{ $t('payment_accounts.ending_in') }}
                    {{ paymentAccountPreview.account_number_value }}
                  </p>
                </div>

                <div
                  style="
                    display: flex;
                    justify-content: start;
                    align-items: center;
                  "
                >
                  <p style="padding-left: 0; padding-right: 0px">
                    <b style="color: #3939ff"
                      >{{ $t('customers.billing_address') }}:</b
                    >
                    {{ billingAddressPreview }}
                  </p>
                </div>
              </div>
            </div>

            <div class="mx-0 mt-0 flex justify-end">
              <sw-dropdown position="bottom-end">
                <sw-button
                  :disabled="isLoading"
                  :loading="isLoading"
                  slot="activator"
                  class="ml-4"
                  variant="primary"
                  type="button"
                >
                  <credit-card-icon class="h-6 mr-1 -ml-2 font-bold" />
                  {{ $t('payment_accounts.add_or_change') }}
                </sw-button>
                <sw-dropdown-item @click="addAccountModal">
                  <plus-icon class="h-6 mr-3 text-gray-600" />
                  <label style="margin-top: 1%">
                    {{ $t('payment_accounts.add_account') }}
                  </label>
                </sw-dropdown-item>
                <sw-dropdown-item
                  v-if="isPaymentAccounts"
                  @click="changeAccountModal"
                >
                  <switch-horizontal-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('payment_accounts.change_account') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </div>
          </div>

          <div
            v-else
            class="pt-2 pb-4 grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-1 text-center"
          >
            <div class="mt-12 mb-6">
              <sw-badge
                class="no_document"
                :bg-color="$utils.getBadgeStatusColor('I').bgColor"
                :color="$utils.getBadgeStatusColor('I').color"
              >
                {{ $t('payment_accounts.no_has_payment_methods') }}
              </sw-badge>
            </div>
          </div>

          <!-- -->
        </div>
<!--         <div v-if="customerHasPaymentMethods && !isShowIdentificationVerification"
            class="pt-2 pb-4 grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-3">
            <sw-input-group :label="$t('settings.payment_gateways.title')">
              <sw-select v-model="formData.payment_gateways" :options="payment_gateways" :searchable="true"
                :show-labels="false" :allow-empty="true" :placeholder="$t('items.select_a_type')" class="mt-1"
                track-by="id" label="name" select="" @input="checkingPaymentVerification" />
              <div class="p-2" v-if="type_cc">
                <p class="p-0 m-0 text-xs leading-tight text-gray-500"
                  :style="{ color: existSettingsForPaymentGateways.color }" style="font-size: 14px">
                  {{ existSettingsForPaymentGateways.message }}
                </p>
              </div>
            </sw-input-group>
          </div> -->
        <div
          class="pt-2 pb-4 grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-1"
        >
          <div
            
            class="w-full flex justify-end"
          >
            <sw-button
              v-if="!notEditable && !isIdentificationVerification"
              :loading="isLoading"
              :disabled="isLoading || !customerHasPaymentMethods"
              variant="primary"
              type="submit"
              size="lg"
              class="sm:flex"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{
                isEdit ? $t('payments.update_payment') : $t('customers.add_credit')
              }}
            </sw-button>
            <sw-button v-if="isIdentificationVerification" :loading="isLoading" :disabled="isLoading" variant="primary"
            type="submit" size="lg" class="sm:flex md:mt-4">
            <save-icon v-if="!isLoading && !isIdentificationVerification" class="mr-2 -ml-1" />
            <ArrowRightIcon v-if="!isLoading && isIdentificationVerification" class="mr-2 -ml-1" />
            {{ showTextButtonSubmit }}
          </sw-button>
            <sw-button v-else style="display: none"></sw-button>
          </div>
        </div>


        <div
            v-if="
              this.paymentfeesenabled && this.payment_fees.length > 0
            "
            class="w-full md:pr-2"
          >
            <div>
              <br />
              <!-- Título antes de la tabla -->
              <h2 class="text-lg font-bold mb-4">Payment Fees</h2>

              <!-- Tabla de registros -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div
                  v-for="(item, index) in payment_fees"
                  :key="index"
                  class="p-2 border rounded"
                >
                  <div class="flex justify-between">
                    <p>{{ item.name }}</p>
                    -
                    <p>{{ item.type }}</p>
                  </div>
                  <p
                  v-if="item.type == 'fixed'"
                  >
                    <div v-if="item.type == 'fixed'" v-html="$utils.formatMoney(item.amount,  defaultCurrency)" />

                  </p>

                  <p
                  v-if="item.type == 'percentage'"
                  >
                   {{ item.amount/100 }} %

                  </p>
                </div>
              </div>

              <!-- Leyenda después de la tabla -->
              <p class="mt-4 text-sm" style="color: rgb(197, 48, 48);">
                These payment fees will be applied at the time of charging.
              </p>
            </div>
            <br />
          </div>


      </sw-card>
    </div>
      <div v-if="isShowIdentificationVerification">
        <IdentityVerification :customer="customer" :date="formData.payment_date"
          :paymentMethod="formData.payment_method" :paymentGateway="formData.payment_gateways"
          :isVerificationSuccessful="verificationSuccessful" @verificationSuccessful="verificationSuccessful = true"
          @goToPayment="submitPaymentData" @cancelValidateEvent="backToForm" />
      </div>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import moment from 'moment'
import {
  ShoppingCartIcon,
  PlusSmIcon,
  CreditCardIcon,
  RefreshIcon,
  ArrowRightIcon,
  PlusIcon,
  SwitchHorizontalIcon,
} from '@vue-hero-icons/solid'

import CustomFieldsMixin from '../../mixins/customFields'
import { VueDatePicker } from '@mathieustan/vue-datepicker'
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css'
import ItemModalVue from '../../components/base/modal/ItemModal.vue'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
import creditCardExpirationDate from '../../components/payments/creditCardExpirationDate.vue'
import Paypal from '../payments/Paypal.vue'
import IdentityVerification from './IdentityVerification/index.vue'

const {
  required,
  between,
  numeric,
  email,
  minLength,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ShoppingCartIcon,
    VueDatePicker,
    EyeIcon,
    EyeOffIcon,
    creditCardExpirationDate,
    Paypal,
    ArrowRightIcon,
    PlusSmIcon,
    CreditCardIcon,
    RefreshIcon,
    PlusIcon,
    SwitchHorizontalIcon,
    IdentityVerification
  },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      isShowIdentificationVerification: false,
      isIdentificationVerification: false,
      verificationSuccessful: false,
      activeButton: null,
      isUserAction: false,
      options_payment_methods: [],
      showPassword: false,
      creditv: false,
      isdisableed: true,
      isShowPassword: false,
      isShowPassword1: false,
      isShowPassword2: false,
      payment_fees: [],
      paymentfeesenabled: false,
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
        payment_date: new Date(),
        amount: 100,
        customer_credit: false,
        payment_method: {
          name: null,
        },
        invoice_id: null,
        notes: null,
        payment_method_id: null,
        payment_gateways: [],
        authorize: null,
        authorize_id: null,
        credit_cards: [],
        invoice_list: [],
        credit_card: null,
        updatebillinginformation: false,
        createaccount: false,
        transaction_status: null,
        isTransactionStatus: false,
        status_with_authorize: true,
        add_payment_gateway: 0,
        payment_method_nonce: null,
        nonce: null,
        invoices: [],
        status: {
          value: 'Approved',
          text: 'Approved',
        },
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
        invoice_list: [],
        invoices: [],
        fees: [],
        has_fees:0,
      },
      paypal: {
        payer_email: '',
        date: null,
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
      customer: null,
      account: null,
      card: null,
      invoice: null,
      invoiceList: [],
      accountList: [],
      cardList: [],
      isLoading: false,
      isRequestOnGoing: false,
      maxPayableAmount: Number.MAX_SAFE_INTEGER,
      maxAmountIsNotCustomerCreditBalance: 0,
      isSettingInitialData: true,
      paymentNumAttribute: null,
      paymentPrefix: '',
      PaymentFields: [
        'customer',
        'company',
        'customerCustom',
        'payment',
        'paymentCustom',
      ],
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
      status: [],

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
      showCvvFieldHide: false,
      showAccountFieldHide: true,
      showRoutingFieldHide: true,

      //
      paymentAccountPreview: null,
      billingAddressPreview: null,
      isPaymentAccounts: false,
      customer_information: null,
      payment_accounts: [],
      isLoadingThePaymentAccount: false,
      customerHasPaymentMethods: false,
      invoiceList: [], // Tu lista de facturas
      invoiceList2: [], // Tu lista de facturas copias
      selectedInvoices: [], // Inicializar el array vacío
    }
  },
  validations() {
    if (this.type_ach) {
      return {
        customer: {
          required,
        },
        formData: {
          name_on_account: {},
          payment_date: {
            required,
          },
          ACH_type: {
            required,
          },
          account_number: {
            required,
            minLength: minLength(9),
            maxLength: maxLength(20),
          },
          routing_number: {
            required,
            minLength: minLength(8),
            maxLength: maxLength(9),
          },
          num_check: {
            maxLength: maxLength(4),
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount),
          },
        },
        authorize: {
          country_id: {
            required,
          },
          state_id: {
            required,
          },
          city: {
            required,
          },
          address_street_1: {
            required,
            maxLength: maxLength(255),
          },
          address_street_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    } else if (this.isEdit) {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount),
          },
          status: {
            required,
          },
          payment_method: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    } else if (this.isAuthorize && !this.type_ach) {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount),
          },
          status: {
            required,
          },
          credit_cards: {
            required,
          },
        },
        authorize: {
          payer_email: {
            required,
            email,
          },
          card_number: {
            required,
            numeric,
            minLength: minLength(13),
            maxLength: maxLength(19),
          },
          cvv: {
            required,
            numeric,
            minLength: minLength(3),
            maxLength: maxLength(4),
          },
          date: {
            required,
          },
          country_id: {
            required,
          },
          state_id: {
            required,
          },
          city: {
            required,
          },
          address_street_1: {
            required,
            maxLength: maxLength(255),
          },
          address_street_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    } else {
      return {
        customer: {
          required,
        },
        formData: {
          payment_date: {
            required,
          },
          amount: {
            required,
            between: between(1, this.maxPayableAmount),
          },
          status: {
            required,
          },
          credit_cards: requiredIf(this.isAuthorize),
        },
        paymentNumAttribute: {
          required,
          numeric,
        },
      }
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput','defaultCurrency']),
    // ...mapGetters('payment', ['paymentModes', 'selectedNote']),
    ...mapGetters('payment', ['selectedNote']),
    ...mapGetters('customer', ['customers']),
    ...mapState('user', ['currentUser', 'settingsCompany']),

    dateExpirationYear: {
      get() {
        return this.authorize.date.split('-')[0]
      },
      set(year) {
        let isdateExpirationMonth =
          this.dateExpirationMonth != undefined ? true : false
        this.authorize.date = year + '-' + this.authorize.date.split('-')[1]
        this.authorize.expiration_date =
          year + '-' + this.authorize.date.split('-')[1]
        const currentYear = new Date().getFullYear()
        if (year == currentYear) {
          let month = this.monthsOptions[0]
          if (isdateExpirationMonth) {
            if (this.dateExpirationMonth >= this.monthsOptions[0]) {
              month = this.dateExpirationMonth
            }
          }
          this.authorize.date = year + '-' + month
          this.authorize.expiration_date = year + '-' + this.monthsOptions[0]
        }
      },
    },
    dateExpirationMonth: {
      get() {
        return this.authorize.date.split('-')[1]
      },
      set(month) {
        this.authorize.date = this.authorize.date.split('-')[0] + '-' + month
        this.authorize.expiration_date =
          this.authorize.date.split('-')[0] + '-' + month
      },
    },
    getTagClass() {
      return (invoice) => {
        if (!invoice || !invoice.status) return 'bg-primary-500' // Clase por defecto
        switch (invoice.status.toUpperCase()) {
          case 'SENT':
            return 'status-sent'
          case 'VIEWED':
            return 'status-due'
          case 'OVERDUE':
            return 'status-overdue'
          default:
            return 'bg-primary-500' // Clase por defecto
        }
      }
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
    existSettingsForPaymentGateways() {
      if (
        this.isAuthorize &&
        this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando los tres son verdaderos
        return {
          message: this.$t(
            'payments.payment_gateways_settings.authorize_and_paypal_auxpay'
          ),
          color: 'green',
        }
      } else if (
        !this.isAuthorize &&
        !this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando los tres son falsos
        return {
          message: this.$t('payments.payment_gateways_settings.none'),
          color: 'red',
        }
      } else if (
        this.isAuthorize &&
        this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando Authorize y Paypal son verdaderos, AuxVault es falso
        return {
          message: this.$t(
            'payments.payment_gateways_settings.authorize_and_paypal'
          ),
          color: 'green',
        }
      } else if (
        this.isAuthorize &&
        !this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando Authorize y AuxVault son verdaderos, Paypal es falso
        return {
          message: this.$t(
            'payments.payment_gateways_settings.authorize_and_auxpay'
          ),
          color: 'green',
        }
      } else if (
        !this.isAuthorize &&
        this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando Paypal y AuxVault son verdaderos, Authorize es falso
        return {
          message: this.$t(
            'payments.payment_gateways_settings.auxpay_and_paypal'
          ),
          color: 'green',
        }
      } else if (
        this.isAuthorize &&
        !this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando solo Authorize es verdadero

        return {
          message: this.$t('payments.payment_gateways_settings.authorize'),
          color: 'green',
        }
      } else if (
        !this.isAuthorize &&
        this.isPaypalSetting &&
        !this.isAuxVaultSetting
      ) {
        // Código cuando solo Paypal es verdadero
        return {
          message: this.$t('payments.payment_gateways_settings.paypal'),
          color: 'green',
        }
      } else if (
        !this.isAuthorize &&
        !this.isPaypalSetting &&
        this.isAuxVaultSetting
      ) {
        // Código cuando solo AuxVault es verdadero
        return {
          message: this.$t('payments.payment_gateways_settings.auxpay'),
          color: 'green',
        }
      }
    },
    showIconsModeVerification() {
      // si isIdentificationVerification es true y isShowIdentificationVerification es false y isLoading es false entonces se muestra el icono de verificacion
      if (
        this.isIdentificationVerification &&
        !this.isShowIdentificationVerification
      ) {
        return true
      }
      return false
    },
    showTextButtonSubmit() {
      if (this.showIconsModeVerification) {
        return this.$t('payments.verify_identification')
      } else if (this.isEdit) {
        return this.$t('payments.update_payment')
      } else {
        return this.$t('payments.save_payment')
      }
    },
    // generador de los 12 meses del año formato MM
    monthsOptions() {
      const months = []
      const yearSelect = this.authorize.date.split('-')[0]
      const currentMonth =
        yearSelect == new Date().getFullYear() ? new Date().getMonth() + 1 : 1
      for (let i = 1; i <= 12; i++) {
        months.push(i < 10 ? `0${i}` : `${i}`)
      }
      return months
    },

    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },
    pageTitle() {
      if (this.$route.name === 'payments.edit') {
        return this.$t('payments.edit_payment')
      }
      return this.$t('customers.add_credit')
    },
    isEdit() {
      if (this.$route.name === 'payments.edit') {
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
    DateError() {
      if (!this.$v.formData.payment_date.$error) {
        return ''
      }
      if (!this.$v.formData.payment_date.required) {
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

      if (
        this.creditv &&
        this.amount > this.customer.balance &&
        !this.$v.formData.amount.between &&
        this.amount > 0
      ) {
        return 'The payment amount is greater than the customer credit balance'
      }

      if (!this.$v.formData.amount.between && this.amount > 0) {
        return this.$t('validation.payment_greater_than_due_amount')
      }
    },
    paymentNumError() {
      if (!this.$v.paymentNumAttribute.$error) {
        return ''
      }

      if (!this.$v.paymentNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.paymentNumAttribute.numeric) {
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
        if (this.$v.formData.status && !this.$v.formData.status.$error) {
          return ''
        }
        if (this.$v.formData.status && !this.$v.formData.status.required) {
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
    isAuthorize() {
      this.checkingPaymentVerification()
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
            //console.log('si carga authorize')
          }
          this.is_authorize = true
          this.is_paypal = false
          return true
        } else if (this.formData.payment_gateways.name === 'Paypal') {
          //console.log('paypal')
          // this.paypalToken()
          if (this.customer) {
            let params = {
              id: this.customer.id,
            }
            this.loadCustomerData(params)
          }
          this.is_paypal = true
          this.is_authorize = false
          return true
        } else {
          //console.log('athorize error')
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
    numCheckError() {
      if (!this.$v.formData.num_check.$error) {
        return ''
      }
      if (!this.$v.formData.num_check.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.num_check.maxLength) {
        return this.$t('validation.num_check_maxLength')
      }
    },
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
      return this.paymentPrefix + '-' + this.paymentNumAttribute
    },
    invoice_number() {
      return this.invoice ? this.invoice.invoice_number : ''
    },
  },
  watch: {
    // Observar cambios en el array selectedInvoices
    selectedInvoices: {
      handler(newVal, oldVal) {
        // Verificar si los valores han cambiado y si el cambio fue hecho por el usuario
        if (
          JSON.stringify(newVal) !== JSON.stringify(oldVal) &&
          this.isUserAction
        ) {
          // console.log('Cambio en selectedInvoices realizado por el usuario')
          // Calcular la sumatoria de due_amount en selectedInvoices
          const sum = newVal.reduce(
            (acc, invoice) => acc + invoice.due_amount,
            0
          )
          // console.log('Suma de due_amount en selectedInvoices:', sum)
          // Actualizar formData.amount y authorize.amount
          this.formData.amount = sum
          this.amount = sum / 100
          this.authorize.amount = sum
          // Actualizar formData.invoice_list y authorize.invoice_list
          this.formData.invoice_list = newVal
          this.authorize.invoice_list = newVal
          // Llamar a la función de actualización
          this.updateProperties()

          // Usar this.$nextTick para esperar a que el DOM se actualice
          this.$nextTick(() => {
            // Luego de la actualización del DOM, llamar a actualizarEstadoEtiquetas
            this.actualizarEstadoEtiquetas()
          })
        }
      },
      deep: true, // Observar cambios profundos en el array
    },
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
    async customer(newValue) {
      this.isLoadingPayments = true
      this.formData.user_id = newValue.id
      this.creditv = false
      if (!this.isEdit) {
        if (this.isSettingInitialData) {
          this.isSettingInitialData = false
        } else {
          this.invoice = null
          this.formData.invoice_id = null
        }
        this.formData.amount = 0
        this.invoiceList = []
        this.invoiceList2 = []
        await this.fetchCustomerInvoices(newValue.id)
        this.accountList = []
        await this.fetchCustomerAccounts(newValue.id)
        this.selectFirstInvoices()
      }
    },
    selectedNote() {
      if (this.selectedNote) {
        this.formData.notes = this.selectedNote
      }
    },
    invoice(newValue) {
      if (newValue) {
        this.formData.invoice_id = newValue.id
        if (!this.isEdit) {
          this.setPaymentAmountByInvoiceData(newValue.id)
        }
        // en caso de que el cliente posea en su cuenta (Creditos / Customer Balance Credit > 0.00$)
        if (this.customer.balance > 0) {
          this.creditv = true
          //this.formData.payment_method = {}
          //this.type_ach = false
          //this.type_cc = false
          //this.formData.payment_gateways = []
        } else {
          this.creditv = false
          this.maxPayableAmount = Number.MAX_SAFE_INTEGER
        }
        this.actualizarEstadoEtiquetas()
      }
    },
  },
  created() {
    if ( this.settingsCompany.enable_credit_customer === "0") {
      this.$router.push('./views/errors/404.vue')
    }
    this.fetchInitData()
    window.hub.$on('addPaymentAccount', (val) => {
      this.addPaymentAccount(val)
    })
    window.hub.$on('changePaymentAccount', (val) => {
      this.setPaymentAccount(val)
    })
  },
  async mounted() {
    this.$v.formData.$reset()
    this.resetSelectedNote()
    this.$nextTick(() => {
      this.loadData()

      // console.log("loadata")
      // console.log(this.$route.params.id)
      if (this.$route.params.id && !this.isEdit) {
        this.setInvoicePaymentData()
      }
    })

    this.isUserAction = true
    this.actualizarEstadoEtiquetas()
  },
  methods: {
    ...mapActions('invoice', [
      'fetchInvoice',
      'fetchInvoices',
      'fetchInvoicesCustomerPayments',
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
      'processPayment',
      'paymentsMethodActiveCustomerCredit',
      'paymentAsociated',
    ]),
    ...mapActions('paymentGateways', ['fetchPaymentGateways']),

    ...mapActions('authorizations', [
      'addAuthorize',
      'saveAuthorizeDB',
      'voidAuthorize',
      'refundedAuthorize',
      'addAuthorizeACH',
      'saveAuthorizeACH',
      'addAuthorizePaypal',
      'savePaypalDB',
      'chargePaypalPro',
    ]),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('customer', ['fetchCustomers', 'fetchCustomer']),

    ...mapActions('failedPaymentHistory', ['addFailedPaymentHistory']),

    ...mapActions('user', ['fetchCurrentUser']),

    addAccountModal() {
      let data = {
        client_id: this.customer_information.id,
        billing_address: this.customer_information.billing_address,
        type: 'ADD',
      }
      this.openModal({
        title: this.$t('payment_accounts.add_payment_account'),
        componentName: 'AddOrChangePaymentAccountModal',
        data: data,
      })
    },
    checkingPaymentVerification() {
      if (this.currentUser.verified === 0) {
          if (this.formData.payment_gateways !== null && this.formData.payment_gateways.isidentificationverification === "YES") {
          this.isIdentificationVerification = true;
        } else {
          this.isIdentificationVerification = false;
        }
      }

    },
    getStatusClass(status) {
      // console.log(status)
      return 'status-' + status
    },
    filtrarYSeleccionarInvoices(statusParam) {
      //console.log('Método filtrarYSeleccionarInvoices iniciado.');

      // 6) Verificar si this.invoiceList2 está vacío o es null
      if (!this.invoiceList2 || this.invoiceList2.length === 0) {
        //console.log('this.invoiceList2 está vacío o es null, cancelando operación.');
        return
      } else {
        //console.log('this.invoiceList2 tiene elementos, continuando con la operación.');
      }
      this.activeButton = statusParam
      // 2) Remover todos los elementos de los arrays mencionados
      //console.log('Limpiando arrays selectedInvoices, formData.invoice_list, authorize.invoice_list y invoiceList.');
      this.selectedInvoices = []
      this.formData.invoice_list = []
      this.authorize.invoice_list = []
      this.invoiceList = []

      // 4) Verificar si statusParam es igual a 'ALL'
      if (statusParam === 'ALL') {
        //console.log('statusParam es "ALL", copiando todo el contenido de invoiceList2 a invoiceList.');
        this.invoiceList = [...this.invoiceList2]
        //this.selectedInvoices = this.invoiceList.slice(0, 5);
        //console.log('Los primeros 5 elementos de invoiceList han sido agregados a selectedInvoices.');
      } else {
        // 3) Filtrar this.invoiceList2 por status y actualizar this.invoiceList
        //console.log(`Filtrando invoiceList2 por status: ${statusParam}`);
        //this.invoiceList = this.invoiceList2.filter(invoice => invoice.status === statusParam);
        //console.log(`Elementos con status ${statusParam} han sido agregados a invoiceList.`);
        // Agregar los primeros 5 elementos filtrados a this.selectedInvoices
        //this.selectedInvoices = this.invoiceList.slice(0, 5);
        //console.log('Los primeros 5 elementos filtrados han sido agregados a selectedInvoices.');
      }

      // 5) Llamar a updateProperties al final del método
      this.$nextTick(() => {
        //console.log('Llamando a updateProperties después de la actualización del DOM.');
        this.updateProperties()
      })

      //console.log('Método filtrarYSeleccionarInvoices completado.');
    },

    actualizarEstadoEtiquetas() {
      //console.log('Actualizando estados de las etiquetas...');

      // Obtener todos los elementos span
      const spans = document.querySelectorAll('span')
      //console.log(`Encontrados ${spans.length} spans para verificar.`);

      // Iterar sobre cada span encontrado
      spans.forEach((span, index) => {
        //console.log(`Procesando span ${index + 1} de ${spans.length}.`);

        // Verificar si el span contiene alguna de las palabras clave y aplicar la clase correspondiente
        if (span.textContent.includes('SENT')) {
          // Eliminar otras clases antes de añadir la nueva
          span.classList.remove('due-status', 'overdue-status')
          span.classList.add('sent-status')
          //console.log('Estado SENT encontrado. Clase "sent-status" añadida.');
        } else if (span.textContent.includes('VIEWED')) {
          // Eliminar otras clases antes de añadir la nueva
          span.classList.remove('sent-status', 'overdue-status')
          span.classList.add('due-status')
          //console.log('Estado VIEWED encontrado. Clase "due-status" añadida.');
        } else if (span.textContent.includes('OVERDUE')) {
          // Eliminar otras clases antes de añadir la nueva
          span.classList.remove('sent-status', 'due-status')
          span.classList.add('overdue-status')
          //console.log('Estado OVERDUE encontrado. Clase "overdue-status" añadida.');
        }
      })

      //console.log('Actualización de estados completada.');
    },
    backToForm() {
      this.isShowIdentificationVerification = false
      this.verificationSuccessful = false
    },

    // Método para inicializar las clases para los elementos precargados
    initializeSelectedClasses() {
      this.selectedInvoices.forEach((invoice) => {
        this.$set(invoice, 'statusClass', this.getStatusClass(invoice.status))
      })
    },
    // Método para actualizar las clases cuando se selecciona un elemento
    updateSelectedClass(invoice) {
      this.$set(invoice, 'statusClass', this.getStatusClass(invoice.status))
    },

    ////carga listado de invoices multiples
    async selectFirstInvoices() {
      return false
      // Verificar si invoiceList está vacío
      if (this.invoiceList.length === 0) {
        return // Salir del método si no hay facturas
      }

      // Calcular cuántos elementos seleccionar (máximo 5)
      const count = Math.min(5, this.invoiceList.length)

      // Iterar sobre la lista de facturas
      for (
        let i = 0;
        i < this.invoiceList.length && this.selectedInvoices.length < count;
        i++
      ) {
        const invoice = this.invoiceList[i]

        // Verificar si el id coincide con this.$route.params.id
        if (
          this.$route.params.id &&
          false &&
          invoice.id === this.$route.params.id
        ) {
          // Verificar si el id ya está seleccionado
          if (
            !this.selectedInvoices.some(
              (selected) => selected.id === invoice.id
            )
          ) {
            // Agregar el registro a selectedInvoices
            this.selectedInvoices.push(invoice)
          }
        }
      }

      // Si no se han seleccionado suficientes registros, completar con los primeros registros disponibles
      while (this.selectedInvoices.length < count) {
        const invoice = this.invoiceList[this.selectedInvoices.length]
        this.selectedInvoices.push(invoice)
      }

      // Verificar si this.$route.params.id es numérico y no está vacío
      if (this.$route.params.id && !isNaN(this.$route.params.id)) {
        let data = await this.fetchInvoice(this.$route.params.id)
        let invoiceObject = data.data.invoice

        // Resto del código...
        if (
          invoiceObject !== null &&
          invoiceObject !== undefined &&
          typeof invoiceObject === 'object'
        ) {
          // Verifica si el campo id está en this.invoiceList y this.selectedInvoices
          const isInInvoiceList = this.invoiceList.some(
            (invoice) => invoice.id === invoiceObject.id
          )
          const isInSelectedInvoices = this.selectedInvoices.some(
            (invoice) => invoice.id === invoiceObject.id
          )

          // Si NO está en alguno de los arrays, agregar el objeto a ambos
          if (!isInInvoiceList || !isInSelectedInvoices) {
            if (!isInInvoiceList) {
              this.invoiceList.push(invoiceObject)
            }
            if (!isInSelectedInvoices) {
              this.selectedInvoices.push(invoiceObject)
            }
          }
        } else {
          console.log(
            'invoiceObject no es un objeto válido o es null/undefined'
          )
        }
      } else {
        console.log('El ID proporcionado no es numérico o no existe')
      }

      this.invoiceList2 = this.invoiceList
      this.formData.invoice_list = this.selectedInvoices
      this.authorize.invoice_list = this.selectedInvoices

      this.initializeSelectedClasses()
      this.updateProperties()
    },

    // Método para actualizar las propiedades según las condiciones dadas
    updateProperties() {
      // Si el saldo del cliente es mayor que 0 y no está en modo edición
      //console.log('Customer balance')
      //console.log(this.customer.balance)
      if (this.customer.balance >= 1 && !this.isEdit) {
        // Establecer creditv en true y limpiar las propiedades relacionadas con el método de pago
        this.creditv = true
      } else {
        // Si el saldo del cliente es 0 o menos, o está en modo edición
        // Establecer creditv en false y maxPayableAmount en Number.MAX_SAFE_INTEGER
        this.creditv = false
        this.maxPayableAmount = Number.MAX_SAFE_INTEGER
      }
      // Si no está en modo edición, ejecutar alguna lógica adicional
      if (!this.isEdit) {
        this.setPaymentAmountByInvoiceData()
      }
    },

    changeAccountModal() {
      let data = {
        client_id: this.customer_information.id,
        billing_address: this.customer_information.billing_address,
        current_payment_account: this.paymentAccountPreview,
        type: 'CHANGE',
      }
      this.openModal({
        title: this.$t('payment_accounts.change_payment_account'),
        componentName: 'AddOrChangePaymentAccountModal',
        data: data,
      })
    },

    invoiceWithAmount({ invoice_number, due_amount, status }) {
      return `${invoice_number} (${this.$utils.formatGraphMoney(
        due_amount,
        this.customer.currency
      )}) ${status}`
    },
    async paypalToken() {
      var client_token = ''
      let res = await window.axios.get('/api/v1/paypal-token')
      if (res) {
        client_token = res.data
        var form = document.querySelector('#payment-form')
        var dropin = document.getElementById('bt-dropin')
        if (dropin.innerHTML == '') dropin.innerHTML = ''
        braintree.dropin.create(
          {
            authorization: client_token,
            container: dropin,
            paypal: {
              flow: 'vault',
            },
          },
          function (createErr, instance) {
            if (createErr) {
              //console.log('Create Error', createErr)
              return
            }
            form.addEventListener('submit', function (event) {
              event.preventDefault()

              instance.requestPaymentMethod(function (err, payload) {
                if (err) {
                  //console.log('Request Payment Method Error', err)
                  return
                }

                // Add the nonce to the form and submit
                document.querySelector('#nonce').value = payload.nonce
              })
            })
          }
        )
      }
    },
    async destroyPaypal() {
      var form = document.getElementById('bt-dropin')
      form.innerHTML = '<p></p>'
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
      //console.log('---- loadCustomerData')
      // console.log(params)
      let response = await this.fetchCustomer(params)
      // console.log(response.data.customer)
      //   console.log(this.customer)
      this.customer.balance = response.data.customer.balance
      this.customer_information = response.data.customer
      this.authorize.payer_email = response.data.customer.email
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

      if (response.data && response.data.payment_auto_generate === 'YES') {
        if (response1.data) {
          this.paymentNumAttribute = response1.data.nextNumber
          this.paymentPrefix = response1.data.prefix
          return true
        }
      } else {
        this.paymentPrefix = response1.data.prefix
      }
    },
    async loadData() {
      this.isRequestOnGoing = true
      this.checkAutoGenerate()
      this.setInitialCustomFields('Payment')
      this.formData.payment_date = moment().format('YYYY-MM-DD')
      let resPaymentModes = await this.paymentsMethodActiveCustomerCredit()

      this.options_payment_methods = resPaymentModes.data.payment_methods
      // select customer if user is logged in
      if (this.currentUser) {
        this.customer = this.currentUser
      }
      this.isRequestOnGoing = false
      await this.loadCustomerData({ id: this.customer.id })
      return true
    },
    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }

      if (this.$route.params.invoiceItem) {
        //this.invoice = this.$route.params.invoiceItem
      }
      this.initLoad = false
    },
    async setInvoicePaymentData() {
      let data = await this.fetchInvoice(this.$route.params.id)
      // this.invoice = data.data.invoice
    },
    async setPaymentAmountByInvoiceData(id) {
      // Calculamos la suma de los due_amount del array invoice_list
      let totalDueAmount =
        this.selectedInvoices?.reduce((total, invoice) => {
          return total + invoice.due_amount
        }, 0) || 0

      // Verificamos si invoice_list está vacío, es null o si totalDueAmount es 0
      if (
        !this.selectedInvoices ||
        this.selectedInvoices.length === 0 ||
        totalDueAmount === 0
      ) {
        this.maxPayableAmount = Number.MAX_SAFE_INTEGER
        this.maxAmountIsNotCustomerCreditBalance = Number.MAX_SAFE_INTEGER
      } else {
        // Resto del código existente...
        if (false) {
        } else if (1 > this.customer.balance) {
          // Verificar si el saldo del cliente es menor que 1
          this.formData.amount = totalDueAmount
          this.maxPayableAmount = totalDueAmount
          this.amount = totalDueAmount / 100
        } else {
          // Usamos la suma total de due_amount como formData.amount y maxPayableAmount
          this.formData.amount = totalDueAmount
          this.maxPayableAmount = totalDueAmount
          this.amount = totalDueAmount / 100
        }
      }

      // Establecemos maxAmountIsNotCustomerCreditBalance
      this.maxAmountIsNotCustomerCreditBalance = totalDueAmount

      // console.log('Fin del método setPaymentAmountByInvoiceData')
      this.$nextTick(() => {
        // Luego de la actualización del DOM, llamar a actualizarEstadoEtiquetas
        this.actualizarEstadoEtiquetas()
      })
    },
    async fetchCustomerInvoices(userId) {
      this.isRequestOnGoing = true
      let data = {
        customer_id: userId,
        status: 'UNPAID',
      }
      //console.log(data)
      let response = await this.fetchInvoicesCustomerPayments(data)

      //.log(response)
      this.invoiceList = [...response.data.invoices]
      this.isRequestOnGoing = false
    },

    async fetchCustomerAccounts(userId) {
      this.isRequestOnGoing = true
      let data = {
        customer_id: userId,
        limit: 1000,
      }
      let response = await this.fetchPaymentAccounts(data)
      this.payment_accounts = [...response.data.payment_accounts.data]

      if (this.payment_accounts.length > 0) {
        // Bool button "change/add"
        this.isPaymentAccounts = true
        let main_account = this.payment_accounts.find(
          ({ main_account }) => main_account == 1
        )

        if (main_account == undefined) {
          main_account = this.payment_accounts[0]
        }

        await this.setPaymentAccount(main_account)
      }

      this.customerHasPaymentMethods =
        this.options_payment_methods.length > 0 ? true : false
      this.isLoadingPayments = false
      this.isRequestOnGoing = false
    },

    async setPaymentAccountFromPaymentMethodSelector(payment_method_type) {
      if (this.payment_accounts.length > 0) {
        let payment_account = this.payment_accounts.find(function ({
          payment_account_type,
          main_account,
        }) {
          return (
            payment_account_type == payment_method_type && main_account == 1
          )
        })

        if (payment_account == undefined) {
          payment_account = this.payment_accounts.find(
            ({ payment_account_type }) =>
              payment_account_type == payment_method_type
          )
        }

        if (payment_account != undefined) {
          this.setPaymentAccount(payment_account, 'selector')
        } else {
          this.paymentAccountPreview = null
        }
      }
    },

    addPaymentAccount(payment_account) {
      this.payment_accounts.push(payment_account)
      if (this.payment_accounts.length == 1)
        this.setPaymentAccount(this.payment_accounts[0])
      this.isPaymentAccounts = true
    },

    async setPaymentAccount(payment_account, from = 'default') {
      if (this.options_payment_methods.length > 0) {
        this.customerHasPaymentMethods = true
        this.isLoadingThePaymentAccount = true
        let type = 'N'
        if (payment_account.payment_account_type == 'CC') {
          type = 'C'
          this.type_ach = false
          this.paymentAccountPreview = {
            id: payment_account.id,
            payment_account_type: payment_account.payment_account_type,
            credit_card: payment_account.credit_card,
            card_number: payment_account.card_number.slice(-4),
          }
          this.setImg(payment_account)
          await this.selectItemCard(payment_account)
          this.type_cc = true
        } else {
          type = 'A'
          this.type_cc = false
          this.paymentAccountPreview = {
            id: payment_account.id,
            payment_account_type: payment_account.payment_account_type,
            account_number_name: this.$t('payment_accounts.account_number'),
            account_number_value: payment_account.account_number.slice(-4),
          }
          this.setImg(payment_account)
          await this.selectItemAccount(payment_account)
          this.type_ach = true
        }

        if (from != 'selector') {
          // Find del primer metodo de pago que coincida con el tipo de cuenta del payment account default
          this.formData.payment_method = this.options_payment_methods.find(
            ({ account_accepted }) => account_accepted == type
          )

          let val = this.formData.payment_method
          if (val != undefined) {
            if (val.add_payment_gateway == 1) {
              let res = await this.fetchPaymentGateways()
              if (res) {
                this.payment_gateways = res.data.payment_gateways
                this.payment_gateways.forEach((element) => {
                  if (
                    element.id === val.payment_gateways_id &&
                    val.payment_gateways_id === 1
                  ) {
                    this.formData.payment_gateways = element
                    this.setDefaultPaymentGateway(
                      this.formData.payment_gateways,
                      'A'
                    )
                  }
                  if (element.default == 1) {
                    this.formData.payment_gateways = element
                    this.setDefaultPaymentGateway(
                      this.formData.payment_gateways,
                      'A'
                    )
                  }
                })
                this.add_payment_gateway_select = true
              }
            } else {
              this.add_payment_gateway_select = false
              this.formData.payment_gateways = null
              this.is_authorize = false
            }
          }
        }
        this.isLoadingThePaymentAccount = false
      }
    },

    setImg(payment_account) {
      if (payment_account.payment_account_type == 'CC') {
        if (payment_account.credit_card == 'VISA') {
          this.paymentAccountPreview.src = `/images/visa.png`
          this.paymentAccountPreview.width = '65px'
        } else if (payment_account.credit_card == 'MASTERCARD') {
          this.paymentAccountPreview.src = `/images/mastercard.png`
          this.paymentAccountPreview.width = '80px'
        } else if (payment_account.credit_card == 'AMERICAN EXPRESS') {
          this.paymentAccountPreview.src = `/images/american_express.png`
          this.paymentAccountPreview.width = '90px'
        } else if (payment_account.credit_card == 'DISCOVER') {
          this.paymentAccountPreview.src = `/images/discover.png`
          this.paymentAccountPreview.width = '100px'
        }
      } else {
        this.paymentAccountPreview.src = `/images/bank.png`
        this.paymentAccountPreview.width = '100px'
      }
    },

    async PaymentModeSelected(val) {
      //this.creditv = false
      //  console.log('Inicio del método PaymentModeSelected')
      // console.log('Valor recibido:', val)
      // this.creditv = false

      // Inicializar totalDueAmount
      let totalDueAmount = 0

      // Verificar si invoice_list no es null y tiene registros
      if (this.selectedInvoices && this.selectedInvoices.length > 0) {
        // Recorrer cada registro y sumar los valores de due_amount
        totalDueAmount = this.selectedInvoices.reduce((total, invoice) => {
          return total + invoice.due_amount
        }, 0)

        //   console.log('Sumatoria de due_amount completada:', totalDueAmount)

        // Asignar el valor de totalDueAmount a las variables formData.amount y maxPayableAmount
        this.formData.amount = totalDueAmount
        this.maxPayableAmount = totalDueAmount
        this.amount = totalDueAmount / 100
      } else {
      }

      let payment_method_type = ''
      this.$v.customer.$touch()

      if (val.account_accepted === 'A') {
        payment_method_type = 'ACH'
        this.type_cc = false
        await this.setPaymentAccountFromPaymentMethodSelector(
          payment_method_type
        )
        this.type_ach = true

        let params = {}
        if (this.customer) {
          params = {
            id: this.customer.id,
          }
          this.loadCustomerData(params)
        }
      } else if (val.account_accepted === 'C') {
        payment_method_type = 'CC'
        this.type_ach = false
        await this.setPaymentAccountFromPaymentMethodSelector(
          payment_method_type
        )
        this.type_cc = true
      } else {
        this.type_cc = false
        this.type_ach = false
      }
      let band = false
      //console.log('payment_gateway: ', val.add_payment_gateway)
      if (val.add_payment_gateway === 1) {
        let res = await this.fetchPaymentGateways()
        if (res) {
          this.payment_gateways = res.data.payment_gateways
          this.payment_gateways.forEach((element) => {
            //console.log('element: ', element)
            //console.log('val', val)
            if (
              element.id === val.payment_gateways_id &&
              val.payment_gateways_id === 1
            ) {
              this.formData.payment_gateways = element
              this.setDefaultPaymentGateway(this.formData.payment_gateways, 'A')
            }

            if (element.default == 1) {
              this.formData.payment_gateways = element
              this.setDefaultPaymentGateway(this.formData.payment_gateways, 'A')
            }
          })

          this.add_payment_gateway_select = true
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
    },

    async transactionStatusSelected(val) {
      if (val.text === 'Void' || val.text === 'Refunded') {
        this.transactionStatusCheck = true
      } else {
        this.transactionStatusCheck = false
      }
    },

    /////////////////////// submit payment data inicio
    async submitPaymentData() {
      try {
        // console.log('Submiting process')
        // console.log(this.formData)

        if (!this.isEdit) {
          if (!this.validateNewPayment()) {
            return false
          }
        }
        if (
          this.isIdentificationVerification &&
          !this.isShowIdentificationVerification
        ) {
          this.isShowIdentificationVerification = true
          return false
        }

        if (!(await this.createPayment())) {
          throw new Error('Failed to create payment.')
        }

        return true // Success
      } catch (error) {
        //ºconsole.log(error)
        // console.error('Error:', error.message)
        this.handlePaymentError(error.message)
        return false
      }
    },

    // Valida el formulario
    validateNewPayment() {
      //º console.log('Inicio de la validación del formulario.')

      this.$v.customer.$touch()
      //console.log('Validación de customer tocada.')

      this.$v.formData.$touch()
      //console.log('Validación de formData tocada.')

      if (this.isAuthorize) {
        this.$v.authorize.$touch()
        //console.log('Validación de authorize tocada.')
      }

      if (this.formData.amount === 0) {
        //console.log('Error: El monto del formulario es 0.')
        throw new Error(this.$t('general.invalid_form_amount'))
      }

      if (this.formData.payment_method == null) {
        //console.log('Error: El método de pago del formulario es nulo.')
        throw new Error(this.$t('payments.select_a_payment_method'))
      }

      if (this.$v.$invalid) {
        //console.log('Error: Los datos del formulario son inválidos.')
        throw new Error(this.$t('general.invalid_form_data'))
      }

      if (!this.formData.payment_method.name) {
        throw new Error(this.$t('payments.select_a_payment_method'))
      }

      //  console.log('Validación del formulario exitosa.')
      return true // Validación exitosa
    },

    /// crea nuevo metodos
    async createPayment() {
      if (
        !this.formData.invoice_list ||
        this.formData.invoice_list.length === 0
      ) {
        return this.handlePaymentWithoutInvoice()
      } else {
        return this.handlePaymentWithInvoice()
      }
    },

    /// crea proceso de pago sin factura
    // crea proceso de pago sin factura
    handlePaymentWithoutInvoice() {
      // Retorna una nueva promesa
      return new Promise((resolve, reject) => {
        // Mostrar una ventana de confirmación al usuario para pagos sin factura
        swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('payments.payment_invoice_message'), // Mensaje para pagos sin factura
          icon: 'warning',
          buttons: true,
        }).then((value) => {
          // Si el usuario confirma la acción
          if (value) {
            // Procesar el pago y resolver la promesa
            this.processPaymentvue().then(resolve).catch(reject)
          } else {
            // Rechazar la promesa si el usuario cancela
            return false
          }
        })
      })
    },
    /// crea proceso de pago con factura
    handlePaymentWithInvoice() {
      // Retorna una nueva promesa
      return new Promise((resolve, reject) => {
        // Mostrar una ventana de confirmación al usuario para pagos con factura
        swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('payments.create_payment'), // Mensaje para pagos con factura
          icon: 'warning',
          buttons: true,
        }).then((value) => {
          // Si el usuario confirma la acción
          if (value) {
            // Procesar el pago y resolver la promesa
            this.processPaymentvue().then(resolve).catch(reject)
          } else {
            // Rechazar la promesa si el usuario cancela

            return false
          }
        })
      })
    },

    ///proceso de pago
    async processPaymentvue() {
      // Simula un proceso de pago exitoso
      // Preparar los datos necesarios para el pago con factura
      //console.log('3071')
      this.authorize.user_id = this.formData.user_id
      this.authorize.invoice_id = this.formData.invoice_id
      this.authorize.amount = this.formData.amount

      // agregar el numero de pago
      this.formData.payment_number =
        this.paymentPrefix + '-' + this.paymentNumAttribute

      this.authorize.payment_number = this.formData.payment_number

      // Determinar los nombres y la dirección según el método de pago
      if (this.formData.payment_method.account_accepted !== 'N') {
        this.authorize.first_name =
          this.authorize.first_name != null
            ? this.authorize.first_name
            : this.customer.name
        this.authorize.last_name =
          this.authorize.last_name != null
            ? this.authorize.last_name
            : this.customer.name
      } else {
        this.authorize.company_name =
          this.customer.first_name != null
            ? this.customer.first_name
            : this.customer.contact_name
        this.authorize.name =
          this.customer.last_name != null
            ? this.customer.last_name
            : this.customer.name
      }

      if (this.authorize.name != null) {
        this.authorize.name = this.authorize.name.substring(0, 21)
      }

      if (this.authorize.company_name != null) {
        this.authorize.company_name = this.authorize.company_name.substring(
          0,
          21
        )
      }

      this.authorize.address_1 = this.authorize.address_street_1
      this.authorize.address_2 = this.authorize.address_street_2

      // Determinar el tipo de cuenta para el pago
      if (this.formData.payment_method.account_accepted === 'C') {
        // Si el método de pago es con tarjeta de crédito
        this.authorize.payment_method_id = this.formData.payment_method.id
        this.authorize.payment_account_type =
          this.card == null ? 'CC' : this.card.payment_account_type

        this.authorize.payment_gateway_id = this.formData.payment_gateways.id
        if (this.formData.credit_cards && this.formData.credit_cards.name) {
          this.authorize.credit_card = this.formData.credit_cards.name
        }
      } else {
        // Si el método de pago es con transferencia bancaria (ACH)

        if (this.formData.payment_method.account_accepted === 'A') {
          this.authorize.payment_account_type = 'ACH'

          this.authorize.ACH_type = this.formData.ACH_type.value
          this.authorize.account_number = this.formData.account_number
          this.authorize.routing_number = this.formData.routing_number
          this.authorize.num_check = this.formData.num_check
          this.authorize.bank_name = this.formData.bank_name

          this.authorize.payment_gateway_id = this.formData.payment_gateways.id
        }
      }

      if(this.paymentfeesenabled){
        this.authorize.has_fees = 1
       
       if (this.payment_fees.length > 0) {
           this.authorize.fees = this.payment_fees.map(
             (fee) => fee.id
           )
         }
      }

      //console.log('3122')
      this.authorize.status = 'A'
      this.authorize.company_id = this.customer.company_id
      this.authorize.client_id = this.customer.id
      this.authorize.payment_date = this.formData.payment_date
      this.authorize.date = this.formData.payment_date
      //console.log('3128')

      //console.log('3128')
      //console.log(this.$store.state.user)
      this.authorize.payer_id = this.$store.state.user.currentUser.id
      //  console.log('libea 3124: ' + this.formData)
      try {
        // Procesar el pago según el método de pago seleccionado
        if (this.formData.payment_method.add_payment_gateway === 0) {
          //console.log('Inicio del método pago simple ')
          this.formData.payment_number = this.codePayment
          this.isLoading = true

          //console.log('Datos actuales en formData:', this.formData)

          let originalAmount = this.formData.amount
          // console.log('Monto original a procesar:', originalAmount)

          if (
            this.formData.invoice_list &&
            this.formData.invoice_list.length > 0
          ) {
            const totalDueAmount = this.formData.invoice_list.reduce(
              (total, invoice) => total + invoice.due_amount,
              0
            )

            if (originalAmount > totalDueAmount) {
              window.toastr['error'](
                'The amount to be processed is higher than the amount of the selected invoices'
              )

              this.isLoading = false
              return
            }
          } else {
            this.formData.invoice_id = null

            let data = {
              ...this.formData,
              invoice_id: null,
              payment_method_id: this.formData.payment_method
                ? this.formData.payment_method.id
                : null,
              payment_date: moment(this.formData.payment_date).format(
                'YYYY-MM-DD'
              ),
            }

            let response = await this.addPayment(data)
            this.isRequestOnGoing = false
            if (response.data.success) {
              lastSuccessfulResponse = response
            }

            if (lastSuccessfulResponse) {
              const redirectPath = `/customer/payments/${lastSuccessfulResponse.data.payment.id}/view`
              this.$router.push(redirectPath)
              window.toastr['success'](this.$t('payments.created_message'))
              this.isLoading = false
              return true
            } else {
              if (
                lastSuccessfulResponse &&
                lastSuccessfulResponse.data.error === 'invalid_amount'
              ) {
                window.toastr['error'](this.$t('invalid_amount_message'))
              } else {
                window.toastr['error']('Error, contact administration')
              }
              this.isLoading = false
              return false
            }
          }

          //console.log('Datos de formData después de ajustes:', this.formData)

          let lastSuccessfulResponse = null
          let lastSuccessfulInvoice = null

          for (let invoice of this.formData.invoice_list || [{ id: null }]) {
            //console.log('Procesando factura con ID:', invoice.id)

            if (originalAmount > invoice.due_amount) {
              this.formData.amount = invoice.due_amount
            } else {
              this.formData.amount = originalAmount
            }

            let data = {
              ...this.formData,
              invoice_id: invoice.id,
              payment_method_id: this.formData.payment_method
                ? this.formData.payment_method.id
                : null,
              payment_date: moment(this.formData.payment_date).format(
                'YYYY-MM-DD'
              ),
            }

            //console.log('Datos enviados en la solicitud de pago:', data)

            let response = await this.addPayment(data)
            this.isRequestOnGoing = false

            //console.log('Respuesta de la solicitud de pago:', response)

            if (response.data.success) {
              lastSuccessfulResponse = response
              lastSuccessfulInvoice = invoice
              originalAmount -= invoice.due_amount

              if (originalAmount <= 0) {
                //  console.log('Monto total procesado. Saliendo del bucle.')
                break
              }
            } else {
              break
            }
          }

          if (lastSuccessfulResponse) {
            const redirectPath = `/customer/payments/${lastSuccessfulResponse.data.payment.id}/view`
            this.$router.push(redirectPath)
            window.toastr['success'](this.$t('payments.created_message'))
            //console.log('Final del método pago simple')
            this.isLoading = false
            return true
          } else {
            if (
              lastSuccessfulResponse &&
              lastSuccessfulResponse.data.error === 'invalid_amount'
            ) {
              window.toastr['error'](this.$t('invalid_amount_message'))
            } else {
              window.toastr['error']('Error, contact administration')
            }
            this.$router.push('/admin/payments')
            //  console.log('Final del método pago simple')
            this.isLoading = false
            return false
          }
        } else {
          // Si se necesita procesar el pago con ACH
          this.isLoading = true
          // Establecer detalles para el pago con ACH
          this.authorize.customcode = this.formData.customcode
          // Procesar el pago con ACH y obtener la respuesta

          this.authorize.nameOnAccount = this.authorize.name
          this.authorize.admin = 'cust'
          if (this.authorize.nameOnAccount != null) {
            this.authorize.nameOnAccount =
              this.authorize.nameOnAccount.substring(0, 21)
          }
          // Comprobar si 'invoice_list' existe, no es null y no está vacío
          if (
            this.authorize.invoice_list &&
            Array.isArray(this.authorize.invoice_list) &&
            this.authorize.invoice_list.length > 0
          ) {
            // Inicializar 'invoices' como un array vacío
            this.authorize.invoices = []

            // Recorrer 'invoice_list' y obtener los 'id'
            this.authorize.invoice_list.forEach((invoice) => {
              if (invoice && invoice.id) {
                this.authorize.invoices.push(invoice.id)
              }
            })
          }
         // console.log(this.authorize)
          const response = await this.processPayment(this.authorize)
          // Mostrar mensaje de éxito o error según la respuesta del servidor
          if (response.data.success) {
            window.toastr['success'](this.$t('payments.created_message'))
            this.$router.push(
              `/customer/payments/${response.data.payment_id}/view`
            )
            this.isLoading = false
            return true
          } else {
            window.toastr['error'](response.data.message)
            this.isLoading = false
            return false
          }
        }
      } catch (error) {
        // Manejar errores
        this.isLoading = false

        if (error.response && error.response.status === 422) {
          //  console.log('Error de validación:', error.response)

          // Si el formato del error es el primero
          if (error.response.data.hasOwnProperty('errors')) {
            for (let key in error.response.data.errors) {
              let message = key + ': ' + error.response.data.errors[key][0]
              window.toastr['error'](message)
            }
          }
          // Si el formato del error es el segundo
          else if (error.response.data.hasOwnProperty('data')) {
            // Comprobar si 'data' es un string
            if (typeof error.response.data.data === 'string') {
              let message = error.response.data.data
              window.toastr['error'](message)
            } else if (Array.isArray(error.response.data.data)) {
              // Si 'data' es un array, iterar sobre él
              for (let key in error.response.data.data) {
                let message = key + ': ' + error.response.data.data[key][0]
                window.toastr['error'](message)
              }
            }
          }
        } else {
          // Manejar otros tipos de errores
          //    console.log('Error desconocido:', error)
          return false
        }

        return false
      }
    },
    // mensaje de error
    handlePaymentError(errorMessage) {
      // Manejar errores de pago
      window.toastr['error'](errorMessage)
    },

    handlePaymentSuccess(successMessage) {
      // Manejar éxitos de pago
      window.toastr['success'](successMessage)
    },

    /////////////////////// submit payment data fin
    async paymentWithCustomerBalance() {
      //console.log('Iniciando método paymentWithCustomerBalance')

      if (
        !this.customer ||
        !this.customer.balance ||
        this.customer.balance < 1
      ) {
        // Muestra un mensaje de error usando toastr
        window.toastr['error'](this.$t('general.invoice_list_empty'))

        // Registra en la consola que se ha entrado en la condición del if
        //  console.log('La condición del if se ha cumplido: Error lanzado.')

        // Retorna false para indicar que la validación ha fallado
        return false
      }
      //  console.log(this.customer.balance)

      this.formData.amount = this.customer.balance * 100
      this.maxPayableAmount = this.customer.balance * 100
      this.amount = this.customer.balance / 100

      // Validar si invoice_list está vacío
      if (!this.selectedInvoices || this.selectedInvoices.length === 0) {
        window.toastr['error'](this.$t('general.invoice_list_empty'))
        //  console.log('invoice_list está vacío o no definido')
        return false
      }

      // Validar campos usando $touch
      //console.log('Validando campos personalizados')
      await this.touchCustomField()
      //console.log('Validando campo customer')
      this.$v.customer.$touch()
      //console.log('Validando campo formData')
      this.$v.formData.$touch()

      // Validar que el monto no sea cero
      if (this.formData.amount == 0) {
        window.toastr['error'](this.$t('general.invalid_form_amount'))
        //  console.log('El monto es cero, no se puede proceder')
        return false
      }

      // Validar si hay campos inválidos
      if (this.$v.$invalid) {
        Object.keys(this.$v).forEach((field) => {
          if (this.$v[field].$invalid) {
            // console.log(`Error en el campo ${field}:`, this.$v[field].$error)
          }
        })

        return false
      }

      // console.log('Todos los campos son válidos, continuando con el proceso')

      // Establecer isLoading a true
      this.isLoading = true

      // Mostrar confirmación si el formulario no está desactivado
      if (!this.isFormDisabled) {
        // console.log('Formulario activo, mostrando mensaje de confirmación')
        swal({
          title: this.$t('general.are_you_sure_customer_credit'),
          icon: 'warning',
          buttons: true,
        }).then(async (result) => {
          if (result) {
            this.preFormatPaymentWithCustomerBalance()
          } else {
            this.isLoading = false
          }
        })
      } else {
        // Si el formulario está desactivado, llamar directamente a preFormatPaymentWithCustomerBalance

        this.preFormatPaymentWithCustomerBalance()
      }
    },

    async preFormatPaymentWithCustomerBalance() {
      try {
        // 1) Validar que this.customer exista y su balance sea mayor a cero
        if (!this.customer || this.customer.balance <= 0) {
          window.toastr['error'](this.$t('general.invalid_customer_balance'))
          // console.error('Customer does not exist or has zero balance')
          return false
        }

        // Variable para almacenar los invoice_numbers procesados
        let processedInvoices = []

        // Variable para almacenar el balance restante del cliente
        let remainingBalance = this.customer.balance
        // console.log('Balance inicial ' + remainingBalance)
        // 2) Recorrer selectedInvoices
        for (let i = 0; i < this.selectedInvoices.length; i++) {
          // 3) Establecer el valor de payment_number
          this.formData.payment_number = `${this.paymentPrefix}-${this.paymentNumAttribute}`

          // 4) Establecer el valor de amount y invoice_id desde selectedInvoices
          this.formData.amount = this.selectedInvoices[i].due_amount
          this.formData.invoice_id = this.selectedInvoices[i].id

          // 5) Establecer el valor de transaction_status
          this.formData.transaction_status = this.formData.status.value

          // 6) Si el cliente tiene saldo suficiente, ajustar el amount
          if (this.creditv && this.formData.amount / 100 > remainingBalance) {
            this.formData.amount = remainingBalance * 100
          }

          // 7) Establecer customer_credit como true
          this.formData.customer_credit = true

          // Preparar los datos para enviar
          let data = {
            ...this.formData,
            payment_method_id: null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          }

          // 8) Realizar la operación de pago
          let successResponse = false // Variable para rastrear si la operación de pago fue exitosa
          let response
          do {
            response = await this.addPayment(data)
            successResponse = response.data.success // Verificar si la operación de pago fue exitosa

            // Si la operación de pago falla debido a un error relacionado con payment_number, ajustar paymentNumAttribute y reintentar
            if (
              !successResponse &&
              response.data.errors.hasOwnProperty('payment_number') &&
              response.data.errors.payment_number[0] ===
                'Invalid number passed.'
            ) {
              // Ajustar paymentNumAttribute
              this.paymentNumAttribute = this.incrementNumberString(
                this.paymentNumAttribute
              )

              // Actualizar payment_number con el nuevo paymentNumAttribute
              this.formData.payment_number = `${this.paymentPrefix}-${this.paymentNumAttribute}`

              // Reintentar la operación de pago con el nuevo payment_number
              // console.log('Retrying payment with adjusted payment number...')
              response = await this.addPayment(data)
            }
          } while (!successResponse) // Repetir el bucle hasta que la operación de pago sea exitosa

          // 9) Manejar la respuesta
          if (successResponse) {
            // console.log('Payment successful')
            // Si hay un pago exitoso, agregar el invoice_number a la lista de procesados
            processedInvoices.push(this.selectedInvoices[i].invoice_number)

            // Descuentos del balance del cliente
            remainingBalance -= this.formData.amount / 100
            // console.log('Balance final: ' + remainingBalance)
            this.paymentNumAttribute = this.incrementNumberString(
              this.paymentNumAttribute
            )
            // Si se alcanza el último elemento de selectedInvoices, redirigir al usuario
            if (
              i === this.selectedInvoices.length - 1 ||
              remainingBalance <= 1
            ) {
              // 10) Redirigir al usuario según el tipo de factura
              this.$router.push(
                `/customer/payments/${response.data.payment.id}/view`
              )
              // 14) Mostrar mensaje de éxito con los invoice_numbers procesados
              let successMessage = `${this.$t(
                'payments.created_message'
              )}. ${this.$t(
                'general.processed_invoices'
              )}: ${processedInvoices.join(', ')}`
              window.toastr['success'](successMessage)
              return true
            }
          } else {
            // 11) Manejar errores de amount inválido
            if (response.data.error === 'invalid_amount') {
              window.toastr['error'](this.$t('invalid_amount_message'))
              console.error('Invalid amount error')
              return false
            }

            // 12) Manejar errores generales
            window.toastr['error'](response.data.error)
            console.error('General error:', response.data.error)
            this.isLoading = false
            // Redirigir al usuario a la ruta de pagos
            this.$router.push('/admin/payments')
          }
        }
      } catch (error) {
        // Manejar cualquier error inesperado
        console.error('Unexpected error:', error)
        window.toastr['error'](this.$t('general.unknown_error'))
        this.isLoading = false
        this.$router.push('/admin/payments')
      }
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

      let address = `${this.authorize.address_street_1}, ${this.authorize.city}, ${this.billing_state.name}`
      let format_address = address.substring(0, 35)
      this.billingAddressPreview = format_address
    },

    async selectItemCard(item) {
      //this.formData.payment_gateways = null
      this.authorize.card_number = item.card_number
      this.formData.credit_cards = { name: item.credit_card }
      //this.authorize.payer_email = null
      this.authorize.cvv = item.cvv
      if (item.expiration_date) {
        this.authorize.date = item.expiration_date
        this.authorize.expiration_date = item.expiration_date
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

      let address = `${this.authorize.address_street_1}, ${this.authorize.city}, ${this.billing_state.name}`
      let format_address = address.substring(0, 35)
      this.billingAddressPreview = format_address
    },
    async paypalSuccess(payment_paypal_id) {
      this.isLoading = true
      this.isRequestOnGoing = true
      this.paymentPaypalProccess = true
      this.formData.payment_number = this.codePayment

      let originalAmount = this.formData.amount

      if (this.formData.invoice_list && this.formData.invoice_list.length > 0) {
        const totalDueAmount = this.formData.invoice_list.reduce(
          (total, invoice) => total + invoice.due_amount,
          0
        )

        if (originalAmount > totalDueAmount) {
          window.toastr['error'](
            'El monto a procesar es superior al monto de las facturas seleccionadas'
          )

          this.isLoading = false
          return
        }
      } else {
        this.formData.invoice_id = null
        this.formData.invoice_id = null

        let data = {
          ...this.formData,
          invoice_id: null,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
        }

        let response = await this.addPayment(data)
        this.isRequestOnGoing = false
        if (response.data.success) {
          lastSuccessfulResponse = response
        }

        if (lastSuccessfulResponse) {
          const redirectPath = `/admin/payments/${lastSuccessfulResponse.data.payment.id}/view`
          this.$router.push(redirectPath)
          window.toastr['success'](this.$t('payments.created_message'))
          this.isLoading = false
          return true
        } else {
          if (
            lastSuccessfulResponse &&
            lastSuccessfulResponse.data.error === 'invalid_amount'
          ) {
            window.toastr['error'](this.$t('invalid_amount_message'))
          } else {
            window.toastr['error']('Error, contact administration')
          }
          this.isLoading = false
          return false
        }
      }

      //   console.log('Datos de formData después de ajustes:', this.formData)

      let lastSuccessfulResponse = null
      let lastSuccessfulInvoice = null

      for (let invoice of this.formData.invoice_list || [{ id: null }]) {
        //console.log('Procesando factura con ID:', invoice.id)

        if (originalAmount > invoice.due_amount) {
          this.formData.amount = invoice.due_amount
        } else {
          this.formData.amount = originalAmount
        }

        let data = {
          ...this.formData,
          invoice_id: invoice.id,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
          payment_paypal_id,
        }

        //console.log('Datos enviados en la solicitud de pago:', data)

        let response = await this.addPayment(data)
        this.isRequestOnGoing = false

        //console.log('Respuesta de la solicitud de pago:', response)

        if (response.data.success) {
          lastSuccessfulResponse = response
          lastSuccessfulInvoice = invoice
          originalAmount -= invoice.due_amount

          if (originalAmount <= 0) {
            //   console.log('Monto total procesado. Saliendo del bucle.')
            break
          }
        } else {
          break
        }
      }

      if (lastSuccessfulResponse) {
        const redirectPath = `/customer/payments/${lastSuccessfulResponse.data.payment.id}/view`
        this.$router.push(redirectPath)
        window.toastr['success'](this.$t('payments.created_message'))
      } else {
        if (
          lastSuccessfulResponse &&
          lastSuccessfulResponse.data.error === 'invalid_amount'
        ) {
          window.toastr['error'](this.$t('invalid_amount_message'))
        } else {
          window.toastr['error']('Error, contact administration')
        }
        this.$router.push('/admin/payments')
      }

      //  console.log('Final del método paypalSuccess')
      this.isLoading = false
    },
    incrementNumberString(numberString) {
      // Convertir el número de cadena a un entero
      let number = parseInt(numberString, 10)

      // Incrementar el número en 1
      number++

      // Verificar si el número alcanzó el límite de 999999
      if (number > 999999) {
        // Expandir el número a 7 dígitos
        return number.toString().padStart(7, '0')
      }

      // Convertir el número de nuevo a una cadena con el mismo formato (rellenando con ceros a la izquierda)
      let incrementedString = number
        .toString()
        .padStart(numberString.length, '0')

      return incrementedString
    },

    setDefaultPaymentGateway(paymentgateway, type) {
      // Log the input parameters
     // console.log('setPaymentFees called with:', paymentgateway, type)

      if (paymentgateway.IsPaymentFeeActive == 'YES') {
        this.paymentfeesenabled = true
        this.payment_fees = paymentgateway.registrationdatafees
      }
    },
  },
}
</script>

<style scoped>
.no_document {
  width: 700px;
  padding: 30px;
  font-size: 25px;
  font-family: CerebriSans-Regular, -apple-system, system-ui, Roboto, sans-serif;
  font-style: italic;
}
</style>
