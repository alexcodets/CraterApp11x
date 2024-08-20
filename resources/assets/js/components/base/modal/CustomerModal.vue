<template>
  <div class="customer-modal">
    <form action="" @submit.prevent="submitCustomerData">
      <div class="flex-1 p-5 sm:p-6">
        <sw-tabs class="text-center">
          <!-- tab1 -->
          <sw-tab-item :title="$t('customers.basic_info')" class="mt-5">

            <sw-input-group
              :label="$t('customers.customer_number')"
              :error="customerNumError"
              variant="horizontal"
              required
            >
              <sw-input
                :prefix="`${customerPrefix} - `"
                v-model="customerNumAttribute"
                :invalid="$v.customerNumAttribute.$error"
                class="mt-1"
                @input="$v.customerNumAttribute.$touch()"
                :disabled="isEdit"
              >
                <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
              </sw-input>
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.display_name')"
              :error="nameError"
              variant="horizontal"
              required
              class="mt-4"
            >
              <sw-input
                ref="name"
                :invalid="$v.formData.name.$error"
                v-model.trim="formData.name"
                type="text"
                name="name"
                class="mt-1 md:mt-0"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.primary_display_name')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model="formData.contact_name"
                type="text"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('corePbx.packages.type')"
              :error="statusPaymentError"
              class="mt-4"
              required
              variant="horizontal"
            >
              <sw-select
                v-model.trim="formData.status_payment"
                :options="status_payment"
                :invalid="$v.formData.status_payment.$error"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('general.select_status')"
                label="text"
                track-by="value"
                @input="$v.formData.status_payment.$touch()"
                :disabled="isEdit"
              />              
            </sw-input-group>

            <sw-input-group
              :label="$t('login.email')"
              :error="emailError"
              class="mt-4"
              variant="horizontal"
              required
            >
              <sw-input
                :invalid="$v.formData.email.$error"
                v-model.trim="formData.email"
                type="text"
                name="email"
                class="mt-1 md:mt-0"
                @input="$v.formData.email.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$tc('settings.currencies.currency')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-select
                v-model="currency"
                :options="currencies"
                :searchable="true"
                :allow-empty="false"
                :show-labels="false"
                :placeholder="$t('customers.select_currency')"
                :maxHeight="200"
                label="name"
                class="mt-1 md:mt-0"
                track-by="id"
                :disabled="true"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.phone')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model.trim="formData.phone"
                type="text"
                name="phone"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.website')"
              :error="websiteError"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model="formData.website"
                :invalid="$v.formData.website.$error"
                type="url"
                class="mt-1 md:mt-0"
                @input="$v.formData.website.$touch()"
              />
            </sw-input-group>
          </sw-tab-item>

          <!-- tab2 -->
          <sw-tab-item :title="$t('customers.billing_address')" class="mt-5">           

            <sw-input-group
              :label="$t('customers.country')"
              class="mt-4"
              variant="horizontal"
              required
              :error="countryIdError"
            >
              <sw-select
                v-model="billingCountry"
                :options="countries"
                :invalid="$v.billingCountry.$error"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('general.select_country')"
                :allow-empty="false"
                track-by="id"
                label="name"
                class="mt-1 md:mt-0"
                @select="countrySelected($event, 'billing')" 
              />
            </sw-input-group>

            <sw-input-group            
              :error="stateIdError"
              :label="$t('customers.state')"
              class="mt-4"
              variant="horizontal"
              required
            >
              <sw-select
                v-model="billing.state"
                :invalid="$v.billing.state.$error"
                :options="billing_states"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('general.select_state')"
                label="name"
                track-by="id"
              />
            </sw-input-group>          

            <sw-input-group
              :label="$t('customers.city')"
              class="mt-4"
              variant="horizontal"
              required
              :error="cityError"
            >
              <sw-input
                v-model="billing.city"
                :invalid="$v.billing.city.$error"
                type="text"
                name="billingCity"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.county')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model="billing.county"
                type="text"
                name="billingCity"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.address')"             
              class="mt-4"
              variant="horizontal"
              required
              :error="billAddress1Error"
            >
              <sw-textarea
                v-model="billing.address_street_1"
                :invalid="$v.billing.address_street_1.$error"
                :placeholder="$t('general.street_1')"
                rows="2"
                cols="50"
                class="mt-1 md:mt-0"
                @input="$v.billing.address_street_1.$touch()"
              />
              <br />
            </sw-input-group>

            <sw-input-group :error="bill2Error" variant="horizontal">
              <sw-textarea
                v-model="billing.address_street_2"
                :placeholder="$t('general.street_2')"
                class="mt-1"
                rows="2"
                cols="50"
                @input="$v.billing.address_street_2.$touch()"
              />
              <br />
            </sw-input-group>           

            <sw-input-group
              :label="$t('customers.zip_code')"
              class="mt-4"
              variant="horizontal"
              required
              :error="zipError"
            >
              <sw-input
                v-model="billing.zip"
                :invalid="$v.billing.zip.$error"
                type="text"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>
          </sw-tab-item>

          <!-- tab3 -->
          <sw-tab-item :title="$t('customers.shipping_address')" class="mt-5">
            <div class="grid md:grid-cols-12">
              <div class="flex justify-end col-span-12">
                <sw-button
                  ref="sameAddress"
                  variant="primary"
                  type="button"
                  @click="copyAddress(true)"
                >
                  {{ $t('customers.copy_billing_address') }}
                </sw-button>
              </div>
            </div>

            <sw-input-group
              :label="$t('customers.name')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model="shipping.name"
                type="text"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.phone')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model.trim="shipping.phone"
                type="text"
                name="phone"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.address')"
              :error="ship1Error"
              class="mt-4"
              variant="horizontal"
            >
              <sw-textarea
                v-model="shipping.address_street_1"
                :placeholder="$t('general.street_1')"
                rows="2"
                cols="50"
                class="mt-1 md:mt-0"
                @input="$v.shipping.address_street_1.$touch()"
              />
              <br />
            </sw-input-group>

            <sw-input-group :error="ship2Error" variant="horizontal">
              <sw-textarea
                v-model="shipping.address_street_2"
                :placeholder="$t('general.street_2')"
                rows="2"
                cols="50"
                @input="$v.shipping.address_street_2.$touch()"
              />
              <br />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.country')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-select
                v-model="shippingCountry"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$t('general.select_country')"
                track-by="id"
                label="name"
                class="mt-1 md:mt-0"
                @select="countrySelected($event, 'shipping')"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.state')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-select
                v-model="shipping.state"
                :options="shipping_states"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$t('general.select_state')"
                track-by="id"
                label="name"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.city')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model="shipping.city"
                type="text"
                name="shippingCity"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.zip_code')"
              class="mt-4"
              variant="horizontal"
            >
              <sw-input
                v-model="shipping.zip"
                type="text"
                class="mt-1 md:mt-0"
              />
            </sw-input-group>
          </sw-tab-item>
        </sw-tabs>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          class="mr-3 text-sm"
          type="button"
          variant="primary-outline"
          @click="cancelCustomer"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button variant="primary" type="submit" :loading="isLoading">
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ $t('general.save') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import AddressStub from '../../../stub/address'
const {
  required,
  minLength,
  email,
  numeric,
  url,
  maxLength,
} = require('vuelidate/lib/validators')
import {
  DocumentDuplicateIcon,
  EyeOffIcon,
  EyeIcon,
  CheckIcon,
  HashtagIcon,
  XCircleIcon
} from '@vue-hero-icons/solid'
export default {
  components: {
    DocumentDuplicateIcon,
    EyeOffIcon,
    EyeIcon,
    CheckIcon,
    HashtagIcon,
    XCircleIcon
  },
  data() {
    return {
      customerNumAttribute: null,
      customerPrefix: null,
      isEdit: false,
      isLoading: false,
      billingCountry: null,
      shippingCountry: null,
      isCopyFromBilling: false,
      currency: '',
      isDisabledBillingState: true,
      isDisabledBillingCity: true,
      isDisabledShippingState: true,
      isDisabledShippingCity: true,
      formData: {
        id: null,
        name: null,
        status_payment: null,
        currency_id: null,
        phone: null,
        website: null,
        contact_name: null,
        addresses: [],
        lead_id: null
      },
      billing: {
        country_id: null,
        state_id: null,
        state: null,
        city: null,
        county: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'billing',
      },
      shipping: {
        country_id: null,
        state: null,
        city: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'shipping',
      },
      status_payment: [
          { value: 'prepaid', text: 'Prepaid' },
          { value: 'postpaid', text: 'Postpaid' },
      ],
      billing_states: [],
      shipping_state: null,
      shipping_states: [],
    }    
  },
  validations: {
    customerNumAttribute: {
      required,
      numeric,
    },
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        required,
        email,
      },
      website: {
        url,
      },
      status_payment: {
        required
      }
    },
    billing: {
      state: {
        required
      },
      city: {
        required
      },      
      address_street_1: {
        required,
        maxLength: maxLength(255),
      },
      zip: {
        required
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
    shipping: {
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
    billingCountry: {
      required
    }
  },
  computed: {
    ...mapGetters(['currencies', 'countries']),
    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),

    customerNumError() {
      if (!this.$v.customerNumAttribute.$error) {
        return ''
      }

      if (!this.$v.customerNumAttribute.required) {
        return this.$tc('estimates.errors.required')
      }

      if (!this.$v.customerNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
      if (!this.$v.formData.name.alpha) {
        return this.$tc('validation.characters_only')
      }
    },
    statusPaymentError() {
      if (!this.$v.formData.status_payment.$error) {
        return ''
      }
      if (!this.$v.formData.status_payment.required) {
        return this.$t('validation.required')
      }
    },
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.email.email) {
        return this.$t('validation.email_incorrect')
      }
    },
    websiteError() {
      if (!this.$v.formData.website.$error) {
        return ''
      }
      if (!this.$v.formData.website.url) {
        return this.$tc('validation.invalid_url')
      }
    },
    // billing
    countryIdError() {
      if (!this.$v.billingCountry.$error) {
        return ''
      }
      if (!this.$v.billingCountry.required) {
        return this.$tc('validation.required')
      }
    },
    stateIdError() {
      if (!this.$v.billing.state.$error) {
        return ''
      }
      if (!this.$v.billing.state.required) {
        return this.$tc('validation.required')
      }
    },
    cityError() {
      if (!this.$v.billing.city.$error) {
        return ''
      }
      if (!this.$v.billing.city.required) {
        return this.$tc('validation.required')
      }
    },
    zipError() {
      if (!this.$v.billing.zip.$error) {
        return ''
      }
      if (!this.$v.billing.zip.required) {
        return this.$tc('validation.required')
      }
    },
    billAddress1Error() {
      if (!this.$v.billing.address_street_1.$error) {
        return ''
      }
      if (!this.$v.billing.address_street_1.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.billing.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    //
    bill1Error() {
      if (!this.$v.billing.address_street_1.$error) {
        return ''
      }
      if (!this.$v.billing.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    bill2Error() {
      if (!this.$v.billing.address_street_2.$error) {
        return ''
      }
      if (!this.$v.billing.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    ship1Error() {
      if (!this.$v.shipping.address_street_1.$error) {
        return ''
      }
      if (!this.$v.shipping.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    ship2Error() {
      if (!this.$v.shipping.address_street_2.$error) {
        return ''
      }
      if (!this.$v.shipping.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },

    hasBillingAdd() {
      let billing = this.billing
      if (
        billing.country_id ||
        billing.state ||
        billing.city ||
        billing.zip ||
        billing.address_street_1 ||
        billing.address_street_2
      ) {
        return true
      }
      return false
    },
    hasShippingAdd() {
      let shipping = this.shipping
      if (
        shipping.country_id ||
        shipping.state ||
        shipping.city ||
        shipping.zip ||
        shipping.address_street_1 ||
        shipping.address_street_2
      ) {
        return true
      }
      return false
    },
  },
  watch: {
    modalDataID(val) {
      if (val) {
        this.isEdit = true
        this.setData()
      } else {
        this.isEdit = false
      }
    },
    billingCountry() {
      if (this.billingCountry) {
        this.billing.country_id = this.billingCountry.id
        return true
      }
    },
    shippingCountry() {
      if (this.shippingCountry) {
        this.shipping.country_id = this.shippingCountry.id
        return true
      }
    },
  },
  async created(){
    //  
    //this.$refs.name.focus = true
    this.currency = this.defaultCurrency
    // edit customer
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }else{
      // create customer
      let response = await this.getCustomerNumber()
      if(response.status == 200)
      {
        this.customerPrefix = response.data.prefix
        this.customerNumAttribute = response.data.nextNumber      
      }
    }
    //    
  },
  mounted() {    
  },
  methods: {
    ...mapActions('invoice', {
      setInvoiceCustomer: 'selectCustomer',
    }),
    ...mapActions('estimate', {
      setEstimateCustomer: 'selectCustomer',
    }),
    ...mapActions('customer', [
      'fetchCustomer',
      'addCustomer',
      'updateCustomer',
      'getCustomerNumber'
    ]),
    ...mapActions('modal', ['closeModal']),
    resetData() {
      this.formData = {
        name: null,
        currency_id: null,
        phone: null,
        website: null,
        contact_name: null,
        addresses: [],
      }

      this.billingCountry = null
      this.shippingCountry = null

      this.billing = { ...AddressStub }
      this.shipping = { ...AddressStub }
      this.$v.formData.$reset()
    },
    cancelCustomer() {
      this.resetData()
      this.closeModal()
    },
    copyAddress(val) {
      if (val === true) {
        this.isCopyFromBilling = true
        this.shipping = { ...this.billing, type: 'shipping' }
        this.shippingCountry = this.billingCountry
      } else {
        this.shipping = { ...AddressStub, type: 'shipping' }
        this.shippingCountry = null
      }
    },
    async loadData() {
      let response = await this.fetchCustomer()
      this.formData.currency_id = response.data.currency.id
      return true
    },
    checkAddress() {
      const isBillingEmpty = Object.values(this.billing).every(
        (val) => val === null || val === ''
      )
      const isShippingEmpty = Object.values(this.shipping).every(
        (val) => val === null || val === ''
      )
      if (isBillingEmpty === true && isBillingEmpty === true) {
        this.formData.addresses = []
        return true
      }

      if (isBillingEmpty === false && isShippingEmpty === false) {
        this.formData.addresses = [
          { ...this.billing, type: 'billing' },
          { ...this.shipping, type: 'shipping' },
        ]
        return true
      }

      if (isBillingEmpty === false) {
        this.formData.addresses.push({ ...this.billing, type: 'billing' })
        return true
      }

      this.formData.addresses = [{ ...this.shipping, type: 'shipping' }]
      return true
    },
    async setData() {
      //Customer Number
      let params = {
        id: this.modalData.id,
      }
      let response = await this.fetchCustomer(params)      
      this.customerPrefix = response.data.customerPrefix
      this.customerNumAttribute = response.data.customerNumber
      //
          
      this.formData.id = this.modalData.id
      this.formData.name = this.modalData.name
      this.formData.status_payment = 
        this.status_payment.find(({value}) => value == this.modalData.status_payment)
      this.formData.email = this.modalData.email
      this.formData.contact_name = this.modalData.contact_name
      this.formData.phone = this.modalData.phone
      this.formData.website = this.modalData.website
      this.currency = this.modalData.currency

      if (this.modalData.billing_address) {
        this.billing = this.modalData.billing_address
        this.billingCountry = this.modalData.billing_address.country
      }
      if (this.modalData.shipping_address) {
        this.shipping = this.modalData.shipping_address
        this.shippingCountry = this.modalData.shipping_address.country
      }
    },
    async countrySelected(country, type) {
      const vm = this
      vm.isLoading = true
      if (type == 'billing') {
        vm.billing.state = null
        vm.billing_states = []
      } else {
        vm.shipping.state = null
        vm.shipping_states = []
      }
      let res = await window.axios.get('/api/v1/states/' + country.code)
      if (res) {
        if (type == 'billing') {
          vm.billing_states = res.data.states
        } else {
          vm.shipping_states = res.data.states
        }
      }
      vm.isLoading = false
    },
    async submitCustomerData() {

      this.$v.formData.$touch()
      this.$v.billing.$touch()
      this.$v.billingCountry.$touch()

      if (this.$v.$invalid) {
        return true
      }

      // this.checkAddress()
      if (this.hasBillingAdd && this.hasShippingAdd) {
        this.formData.addresses = [{ ...this.billing }, { ...this.shipping }]
      } else if (this.hasBillingAdd) {
        this.formData.addresses = [{ ...this.billing }]
      } else if (this.hasShippingAdd) {
        this.formData.addresses = [{ ...this.shipping }]
      }

      this.isLoading = true

      if (this.currency) {
        this.formData.currency_id = this.currency.id
      } else {
        this.formData.currency_id = this.defaultCurrency.id
      }

      this.formData.customcode =
        this.customerPrefix + '-' + this.customerNumAttribute

      this.formData.status_payment = this.formData.status_payment?.value

      // state billing
      this.formData.addresses[0].state_id = this.billing.state?.id     

      try {
        let response = null
        if (this.modalDataID) {
          response = await this.updateCustomer(this.formData)
        } else {
          response = await this.addCustomer(this.formData)
        }
        if (response.data) {
          if (this.modalDataID) {
            window.toastr['success'](this.$tc('customers.updated_message'))
          } else {
            window.toastr['success'](this.$tc('customers.created_message'))
          }

          this.isLoading = false
          if (
            this.$route.name === 'invoices.create' ||
            this.$route.name === 'invoices.edit'
          ) {
            this.setInvoiceCustomer(response.data.customer.id)
          }
          if (
            this.$route.name === 'estimates.create' ||
            this.$route.name === 'estimates.edit'
          ) {
            this.setEstimateCustomer(response.data.customer.id)
          }
          this.resetData()
          this.closeModal()
          return true
        }
      } catch (err) {
        this.isLoading = false
        // if (err.response.data.errors.email) {
        //   window.toastr['error'](this.$t('validation.email_already_taken'))
        // }
      }
    },
  },
}
</script>
