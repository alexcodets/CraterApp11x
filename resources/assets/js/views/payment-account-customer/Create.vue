<template>
  <base-page class="customer-create">
    <form v-if="!initLoad" @submit.prevent="submitPaymentAccountData">
      <sw-page-header class="mb-5" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/customer/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            :to="`/customer/payment-accounts`"
            :title="$tc('payment_accounts.title', 2)"
          />
          <sw-breadcrumb-item
            v-if="
              $route.name === 'paymentAccountCustomer.view.CC' ||
              $route.name === 'paymentAccountCustomer.create.ACH'
            "
            to="#"
            :title="$t('payment_accounts.new_payment_account')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('payment_accounts.edit_payment_account')"
            active
          />
        </sw-breadcrumb>
        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden md:relative md:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />

            {{
              isEditButton
                ? $t('payment_accounts.update_payment_account')
                : $t('payment_accounts.save_payment_account')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <sw-card variant="customer-card">
        <!-- Contact Info  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('payment_accounts.contact_info') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input
              id="client_id"
              v-model="formData.client_id"
              focus
              type="hidden"
              name="client_id"
              class="hidden"
              :value="getUserId"
              autocomplete="off"
              tabindex="1"
            />
            <sw-input-group
              v-if="isCC || isEditCC"
              :label="$t('payment_accounts.name_on_card')"
              class="md:col-span-12"
              :error="displayFirstNameError"
              required
            >
              <sw-input
                :invalid="$v.formData.first_name.$error"
                v-model="formData.first_name"
                focus
                type="text"
                name="name"
                tabindex="1"
                @input="$v.formData.first_name.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              v-if="isACH || isEditACH"
              :label="$t('payment_accounts.name_on_account')"
              class="md:col-span-12"
              :error="displayFirstNameError"
              required
            >
              <sw-input
                :invalid="$v.formData.first_name.$error"
                v-model="formData.first_name"
                focus
                type="text"
                name="name"
                tabindex="1"
                @input="$v.formData.first_name.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.address_1')"
              class="md:col-span-4"
              :error="billAddress1Error"
              required
            >
              <sw-input
                :invalid="$v.formData.address_1.$error"
                v-model="formData.address_1"
                focus
                type="text"
                name="address_1"
                tabindex="1"
                @input="$v.formData.address_1.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.address_2')"
              class="md:col-span-8"
              :error="billAddress2Error"
            >
              <sw-input
                :invalid="$v.formData.address_2.$error"
                v-model="formData.address_2"
                focus
                type="text"
                name="address_1"
                tabindex="1"
                @input="$v.formData.address_2.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.city')"
              class="md:col-span-4"
              :error="cityError"
              required
            >
              <sw-input
                v-model="formData.city"
                :invalid="$v.formData.city.$error"
                name="formData.city"
                type="text"
                tabindex="1"
                @input="$v.formData.city.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :error="stateIdError"
              :label="$t('payment_accounts.state')"
              class="md:col-span-8"
              required
            >
              <sw-select
                v-model="billing_state"
                :invalid="$v.formData.state_id.$error"
                :options="billing_states"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :tabindex="1"
                :placeholder="$t('general.select_state')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.zip')"
              class="md:col-span-4"
              :error="zipError"
              required
            >
              <sw-input
                tabindex="1"
                v-model.trim="formData.zip"
                :invalid="$v.formData.zip.$error"
                type="text"
                name="zip"
                @input="$v.formData.zip.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :error="countryIdError"
              :label="$t('payment_accounts.country')"
              class="md:col-span-8"
              required
            >
              <sw-select
                v-model="billing_country"
                :invalid="$v.formData.country_id.$error"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
                :tabindex="1"
                @select="countrySelected($event, 'billing')"
              />
            </sw-input-group>

            <sw-input-group
              v-if="isEditCC || isEditACH"
              :label="$t('tax_groups.status')"
              class="md:col-span-3 mb-4"
              :error="statusError"
              required
            >
              <sw-select
                v-model="formData.status"
                :invalid="$v.formData.status.$error"
                :options="status"
                :searchable="true"
                :show-labels="false"
                :tabindex="1"
                :allow-empty="true"
                :placeholder="$t('tax_groups.status')"
                label="text"
                track-by="value"
              />
            </sw-input-group>

            <!-- Billing Address Copy Button  -->
            <div class="flex items-center justify-start mb-6 md:mb-0">
              <div class="p-1">
                <sw-button
                  ref="sameAddress"
                  variant="primary"
                  type="button"
                  class="h-8 px-3 py-1 mb-4"
                  @click="copyAddress(true)"
                >
                  <document-duplicate-icon class="h-4 mr-1 -ml-2" />
                  <span class="text-xs">
                    {{ $t('customers.copy_billing_address') }}
                  </span>
                </sw-button>
              </div>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Bank Account Information  -->
        <div v-if="isACH || isEditACH" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('payment_accounts.bank_account_info') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group
              :label="$t('payment_accounts.ACH_type')"
              class="md:col-span-3"
              :error="ACHTypeError"
              required
            >
              <sw-select
                v-model="formData.ACH_type"
                :options="bank_account_type"
                :invalid="$v.formData.ACH_type.$error"
                :searchable="true"
                :show-labels="false"
                :tabindex="1"
                :allow-empty="true"
                label="text"
                track-by="value"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.account_number')"
              class="md:col-span-3"
              :error="accountNumberError"
              required
            >
              <sw-input
                :invalid="$v.formData.account_number.$error"
                v-model="formData.account_number"
                focus
                type="password"
                name="account_number"
                tabindex="1"
                @input="$v.formData.account_number.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.routing_number')"
              class="md:col-span-3"
              :error="routingNumberError"
              required
            >
              <sw-input
                :invalid="$v.formData.routing_number.$error"
                v-model="formData.routing_number"
                focus
                type="password"
                name="routing_number"
                tabindex="1"
                @input="$v.formData.routing_number.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.bankname')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.bank_name"
                focus
                type="text"
                name="bank_name"
                tabindex="1"
                autocomplete="off"
              />
            </sw-input-group>
          </div>
        </div>

        <!-- Credit Card Information  -->
        <div v-if="isCC || isEditCC" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('payment_accounts.credit_card_info') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group
              :label="$t('payment_accounts.card_number')"
              class="md:col-span-3"
              :error="cardNumberError"
              required
            >
              <sw-input
                :invalid="$v.formData.card_number.$error"
                v-model="formData.card_number"
                focus
                type="password"
                name="card_number"
                tabindex="1"
                @input="$v.formData.card_number.$touch()"
                autocomplete="off"
              />
            </sw-input-group>
            <sw-input-group
              :label="$t('settings.payment_gateways.credit_cards')"
              class="md:col-span-3"
              required
            >
              <sw-select
                v-model="formData.credit_cards"
                :options="credit_cards"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('items.select_a_type')"
                class="mt-2"
                :tabindex="1"
                label="name"
                autocomplete="off"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('payment_accounts.cvv')"
              class="md:col-span-3"
              :error="CvvError"
              required
            >
              <sw-input
                :invalid="$v.formData.cvv.$error"
                v-model="formData.cvv"
                focus
                type="password"
                name="cvv"
                tabindex="1"
                @input="$v.formData.cvv.$touch()"
                autocomplete="off"
              />
            </sw-input-group>

            <creditCardExpirationDate
              class="md:col-span-3"
              v-model="formData.expiration_date"
            />

            <!-- <sw-input-group
              :label="$t('payment_accounts.expiration_date')"
              class="md:col-span-3"
              :error="DateError"
              required
            >

              <sw-select
                v-model="formData.expiration_month"
                :invalid="$v.formData.expiration_month.$error"
                :options="months"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                :placeholder="$t('items.select_a_month')"
                label="text"
                track-by="value"
              />
              <br>
              <sw-select
                v-model="formData.expiration_year"
                :invalid="$v.formData.expiration_year.$error"
                :options="years"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                :placeholder="$t('items.select_a_year')"
                label="text"
                track-by="value"
              />


              <VueDatePicker
                v-model="formData.expiration_date"
                :invalid="$v.formData.expiration_date.$error"
                min-date="1900-1"
                max-date="2090-12"
                :locale="locale"
                type="month"
                @input="$v.formData.expiration_date.$touch()"
              />
            </sw-input-group> -->
          </div>
        </div>

        <!-- Mobile Submit Button  -->
        <sw-button
          :disabled="isLoading"
          :loading="isLoading"
          variant="primary"
          type="submit"
          size="lg"
          class="flex w-full sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEditButton
              ? $t('payment_accounts.update_payment_account')
              : $t('payment_accounts.save_payment_account')
          }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import _ from 'lodash'
import { DocumentDuplicateIcon } from '@vue-hero-icons/solid'
import { VueDatePicker } from '@mathieustan/vue-datepicker'
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css'
import creditCardExpirationDate from '@/components/payments/creditCardExpirationDate.vue'

const {
  required,
  numeric,
  minValue,
  minLength,
  email,
  url,
  maxLength,
  sameAs,
} = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentDuplicateIcon,
    VueDatePicker,
    creditCardExpirationDate,
  },
  data() {
    return {
      isCopyFromBilling: false,
      isLoading: false,
      initLoad: false,
      isEditButton: false,
      locale: { lang: 'en' },
      formData: {
        first_name: null,
        country_id: null,
        state_id: null,
        city: null,
        address_1: null,
        address_2: null,
        zip: null,
        payment_account_type: null,
        client_id: null,
        status: {
          value: 'A',
          text: 'Active',
        },

        card_number: null,
        cvv: null,
        expiration_date: new Date(),
        credit_cards: null,

        ACH_type: null,
        account_number: null,
        routing_number: null,
        bank_name: null,
      },
      months: [
        {
          value: '1',
          text: 'January',
        },
        {
          value: '2',
          text: 'February',
        },
        {
          value: '3',
          text: 'March',
        },
        {
          value: '4',
          text: 'April ',
        },
        {
          value: '5',
          text: 'May',
        },
        {
          value: '6',
          text: 'June',
        },
        {
          value: '7',
          text: 'July',
        },
        {
          value: '8',
          text: 'August',
        },
        {
          value: '9',
          text: 'September',
        },
        {
          value: '10',
          text: 'October',
        },
        {
          value: '11',
          text: 'November',
        },
        {
          value: '12',
          text: 'December',
        },
      ],
      years: [],
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
      ],

      bank_account_type: [
        {
          value: 'checking',
          text: 'Checking',
        },
        {
          value: 'savings',
          text: 'Savings',
        },
      ],

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
    }
  },
  validations() {
    if (this.isEditACH || this.isACH) {
      return {
        formData: {
          first_name: {
            required,
            minLength: minLength(3),
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
          address_1: {
            required,
            maxLength: maxLength(255),
          },
          address_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
          status: {
            required,
          },
          ACH_type: {
            required,
          },
          account_number: {
            required,
            minLength: minLength(9),
            maxLength: maxLength(16),
          },
          routing_number: {
            required,
            minLength: minLength(8),
            maxLength: maxLength(9),
          },
        },
      }
    } else if (this.isEditCC || this.isCC) {
      return {
        formData: {
          first_name: {
            required,
            minLength: minLength(3),
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
          address_1: {
            required,
            maxLength: maxLength(255),
          },
          address_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
          status: {
            required,
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
          expiration_date: {
            required,
          },
        },
      }
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    getUserId() {
      return this.currentUser.id
    },
    isEditACH() {
      if (this.$route.name === 'paymentAccountCustomer.edit.ACH') {
        this.isEditButton = true
        return true
      }
      return false
    },
    isEditCC() {
      if (this.$route.name === 'paymentAccountCustomer.edit.CC') {
        this.isEditButton = true
        return true
      }
      return false
    },
    isCC() {
      if (this.$route.name === 'paymentAccountCustomer.create.CC') {
        return true
      }
      return false
    },
    isACH() {
      if (this.$route.name === 'paymentAccountCustomer.create.ACH') {
        return true
      }
      return false
    },
    pageTitle() {
      if (
        this.$route.name === 'paymentAccountCustomer.edit.ACH' ||
        this.$route.name === 'paymentAccountCustomer.edit.CC'
      ) {
        return this.$t('payment_accounts.edit_payment_account')
      }
      return this.$t('payment_accounts.new_payment_account')
    },
    hasBillingAdd() {
      let billing = this.formData
      if (
        billing.name ||
        billing.country_id ||
        billing.state ||
        billing.city ||
        billing.phone ||
        billing.zip ||
        billing.address_1 ||
        billing.address_2
      ) {
        return true
      }
      return false
    },
    displayFirstNameError() {
      if (!this.$v.formData.first_name.$error) {
        return ''
      }
      if (!this.$v.formData.first_name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.first_name_min_length',
          this.$v.formData.first_name.$params.minLength.min,
          { count: this.$v.formData.first_name.$params.minLength.min }
        )
      }
    },

    countryIdError() {
      if (!this.$v.formData.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    stateIdError() {
      if (!this.$v.formData.state_id.$error) {
        return ''
      }
      if (!this.$v.formData.state_id.required) {
        return this.$tc('validation.required')
      }
    },
    cityError() {
      if (!this.$v.formData.city.$error) {
        return ''
      }
      if (!this.$v.formData.city.required) {
        return this.$tc('validation.required')
      }
    },
    zipError() {
      if (!this.$v.formData.zip.$error) {
        return ''
      }
      if (!this.$v.formData.zip.required) {
        return this.$tc('validation.required')
      }
    },
    billAddress1Error() {
      if (!this.$v.formData.address_1.$error) {
        return ''
      }
      if (!this.$v.formData.address_1.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.address_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    billAddress2Error() {
      if (!this.$v.formData.address_2.$error) {
        return ''
      }
      if (!this.$v.formData.address_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$tc('validation.required')
      }
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
    cardNumberError() {
      if (!this.$v.formData.card_number.$error) {
        return ''
      }
      if (!this.$v.formData.card_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.card_number.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.card_number.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.card_number.$params.minLength.min,
          { count: this.$v.formData.card_number.$params.minLength.min }
        )
      }
      if (!this.$v.formData.card_number.maxLength) {
        return this.$t('authorize.cc_number_maxLength')
      }
    },
    CvvError() {
      if (!this.$v.formData.cvv.$error) {
        return ''
      }
      if (!this.$v.formData.cvv.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.cvv.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.cvv.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.cvv.$params.minLength.min,
          { count: this.$v.formData.cvv.$params.minLength.min }
        )
      }
      if (!this.$v.formData.cvv.maxLength) {
        return this.$t('authorize.cvv_maxLength')
      }
    },
    DateError() {
      if (!this.$v.formData.expiration_date.$error) {
        return ''
      }
      if (!this.$v.formData.expiration_date.required) {
        return this.$t('validation.required')
      }
    },
  },
  watch: {
    billing_country(newCountry) {
      if (newCountry) {
        this.formData.country_id = newCountry.id
        this.isDisabledBillingState = false
      } else {
        this.formData.country_id = null
      }
    },
    billing_state(newState) {
      if (newState) {
        this.formData.state_id = newState.id
      } else {
        this.formData.state_id = null
      }
    },
  },
  async created() {
    this.fetchInitData()
    this.loadYears()

    if (!this.isEditACH && !this.isEditCC) {
      try {
        const params = {
          id: this.currentUser.id,
        }
        const response = await this.fetchCustomer(params)
        const billingAddress = response.data?.customer?.billing_address
        if (billingAddress) {
          this.billing_state = billingAddress.state
          this.billing_country = billingAddress.country
        }
      } catch (error) {
        console.error('Error fetching customer data:', error)
      }
    }

    if (this.isEditACH || this.isEditCC) {
      this.loadPaymentAccount()
      return true
    }
  },

  mounted() {},
  methods: {
    ...mapActions('customer', ['fetchCustomer']),

    ...mapActions('paymentAccountsCustomer', [
      'addPaymentAccount',
      'fetchPaymentAccount',
      'updatePaymentAccount',
    ]),

    ...mapActions('customFields', ['fetchCustomFields']),

    loadYears() {
      let first = new Date().getFullYear()
      for (let i = 0; i < 10; i++) {
        this.years.push({
          text: first + i,
          value: first + i,
        })
      }
    },

    async countrySelected(country, type) {
      const vm = this
      vm.isLoading = true
      if (type == 'billing') {
        vm.billing_state = null
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

    async loadPaymentAccount() {
      let id = this.$route.params.payment_account_id

      let response = await this.fetchPaymentAccount(id)

      this.formData = { ...this.formData, ...response.data.payment_accounts }
      this.formData.credit_cards = {
        name: response.data.payment_accounts.credit_card,
        value: response.data.payment_accounts.credit_card,
      }

      if (this.formData.ACH_type) {
        this.formData.ACH_type = {
          text: this.formData.ACH_type,
          value: this.formData.ACH_type,
        }
      }

      if (response.data.payment_accounts.country_id) {
        this.billing_country = response.data.payment_accounts.country
        this.countrySelected(this.billing_country, 'billing')
      }

      if (response.data.payment_accounts.state_id) {
        this.billing_state = response.data.payment_accounts.state
      }
    },

    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },

    async copyAddress(val) {
      if (val === true) {
        let params = {
          id: this.currentUser.id,
        }
        let response = await this.fetchCustomer(params)

        console.log(response)
        this.formData.first_name = response.data.customer.first_name
        this.formData.country_id =
          response.data.customer.billing_address.country_id
        this.formData.state_id = response.data.customer.billing_address.state_id
        this.formData.city = response.data.customer.billing_address.city
        this.formData.address_1 =
          response.data.customer.billing_address.address_street_1
        this.formData.address_2 =
          response.data.customer.billing_address.address_street_2
        this.formData.zip = response.data.customer.billing_address.zip

        this.billing_state = response.data.customer.billing_address.state
        this.billing_country = response.data.customer.billing_address.country

        let res = await window.axios.get(
          '/api/v1/states/' +
            response.data.customer.billing_address.country.code
        )
        if (res) {
          this.billing_states = res.data.states
        }
      }
    },

    async submitPaymentAccountData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      if (this.isEditACH || this.isACH) {
        this.formData.ACH_type = this.formData.ACH_type.value
        this.formData.expiration_date = null
        this.formData.payment_account_type = 'ACH'
      } else {
        this.formData.ACH_type = null
        this.formData.payment_account_type = 'CC'
      }

      this.formData.status = this.formData.status.value
      this.formData.client_id = this.currentUser.id
      //console.log('form data',this.formData)
      try {
        let response = null
        this.isLoading = true

        if (this.isEditACH || this.isEditCC) {
          if (this.formData.status != 'A') {
            swal({
              title: this.$t('general.are_you_sure'),
              text: this.$tc('payment_accounts.change_status'),
              icon: 'warning',
              buttons: true,
            }).then(async (willDelete) => {
              if (willDelete) {
                response = await this.updatePaymentAccount(this.formData)
                if (response.status === 200) {
                  this.$router.push(`/customer/payment-accounts`)
                  window.toastr['success'](this.$t('customers.updated_message'))
                }
                if (response.data.error) {
                  window.toastr['error'](
                    this.$t('validation.email_already_taken')
                  )
                }
              }
            })
          } else {
            response = await this.updatePaymentAccount(this.formData)
            if (response.status === 200) {
              this.$router.push(
                `/customer/payment-accounts/${response.data.payment_accounts.id}/view-${response.data.payment_accounts.payment_account_type}`
              )
              window.toastr['success'](this.$t('customers.updated_message'))
            }
            if (response.data.error) {
              window.toastr['error'](this.$t('validation.email_already_taken'))
            }
          }
        } else {
          response = await this.addPaymentAccount(this.formData)
          if (response.status === 200) {
            this.$router.push(
              `/customer/payment-accounts/${response.data.payment_accounts.id}/view-${response.data.payment_accounts.payment_account_type}`
            )
            window.toastr['success'](this.$t('customers.created_message'))
          }
        }

        this.isLoading = false
        return true
      } catch (error) {
        this.isLoading = false
      }
    },
  },
}
</script>
