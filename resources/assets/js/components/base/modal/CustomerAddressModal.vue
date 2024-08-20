<template>
  <div class="tax-type-modal">
    <form action="" @submit.prevent="submitCustomerAddressData">
      <div class="grid md:grid-cols-2 p-8 sm:p-6 col-span-5" >
          <sw-input-group :label="$t('customers.country')"
            :error="countryIdError"
            class="mt-4"
            required
            variant="horizontal"
          >
            <sw-select
              v-if="!isLoadingCountryState"
              v-model="billing_country"
              :invalid="$v.formData.country_id.$error"
              :options="countries"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('general.select_country')"
              label="name"
              track-by="id"
              @select="countrySelected($event)"

            />
              <!-- @input="$v.formData.billing.country_id.$touch()" -->
          </sw-input-group>

          <sw-input-group :label="$t('customers.state')"
            :error="stateIdError"
            class="mt-4"
            required
            variant="horizontal"
          >
            <sw-select
              v-if="!isLoadingCountryState"
              v-model="billing_state"
              :invalid="$v.formData.state_id.$error"
              :options="states"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :placeholder="$t('general.select_state')"
              label="name"
              track-by="id"
              @select="stateSelected($event)"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.city')"
            class="mt-4"
            :error="cityError"
            required
            variant="horizontal"
          >
            <sw-input
              v-model="formData.city"
              :invalid="$v.formData.city.$error"
              name="formData.city"
              type="text"
              @input="$v.formData.city.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.county')" 
            class="mt-4"
            variant="horizontal"
          >
            <sw-input
              v-model="formData.county"
              name="formData.county"
              type="text"
              :autocomplete="false"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.address')"
            :error="billAddress1Error"
            class="mt-4"
            required
            variant="horizontal"
          >
            <sw-textarea
              v-model.trim="formData.address_street_1"
              :invalid="$v.formData.address_street_1.$error"
              :placeholder="$t('general.street_1')"
              type="text"
              name="billing_street1"
              rows="1"
              @input="$v.formData.address_street_1.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.address')"
            :error="billAddress2Error"
            variant="horizontal"
            class="mt-4"
          >
            <sw-textarea
              v-model.trim="formData.address_street_2"
              :placeholder="$t('general.street_2')"
              type="text"
              name="billing_street2"
              rows="1"
              @input="$v.formData.address_street_2.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.zip_code')"
            :error="zipError"
            required
            variant="horizontal"
            class="mt-4"
          >
            <sw-input
              v-model.trim="formData.zip"
              :invalid="$v.formData.zip.$error"
              type="text"
              name="zip"
              @input="$v.formData.zip.$touch()"
            />
          </sw-input-group>

          <!-- billing validation button -->
          <div v-if="isAvalaraValidation" class="w-full mt-4 text-right">
            <sw-button
              variant="primary-outline"
              size="lg"
              type="button"
              @click="checkBilling"
              :loading="isLoading"
            >
              <check-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ $t('customers.billing_validation') }}
            </sw-button>
          </div>
      </div>

      <div class="z-0 flex justify-end p-4 border-t border-solid border--200 border-modal-bg mt-8" >
        <sw-button
          class="mr-3 text-sm"
          variant="primary-outline"
          type="button"
          @click="closeAddressModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button :loading="isLoading" variant="primary" type="submit">
          <save-icon class="mr-2" v-if="!isLoading" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>

    </form>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon, CheckIcon } from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  between,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    XCircleIcon, CheckIcon
  },
  data() {
    return {
      isEdit: false,
      isAvalara: false,
      isLoading: false,
      isLoadingCountryState: false,
      isAvalaraLocationValidated: false,
      billing_state: null,
      billing_country: null,
      billing_delivery_method: { name: 'Email', value: 'Email' },
      formData: {
        id: null,
        name: null,
        country_id: null,
        state_id: null,
        city: null,
        county: null,
        phone: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'services_address'      
      },
      countries: [],
      states: [],
      billing_states: [],
      categories: [],
      user_id: null
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      /*'refreshData',*/
      'props',
    ]),
    // ...mapGetters('taxGroups', ['taxGroups']),

    isAvalaraValidation(){
      return this.isAvalara > 0 ? true : false
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
      if (!this.$v.formData.address_street_1.$error) {
        return ''
      }
      if (!this.$v.formData.address_street_1.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    billAddress2Error() {
      if (!this.$v.formData.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },

  },
  created() {
    this.loadData()
  },
  validations: {
    formData: {
      country_id: {
        required,
      },
      city: {
        required,
      },
      zip: {
        required,
      },
      state_id: {
        required,
      },
      address_street_1: {
        required,
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
  },
  async mounted() {
    // console.log(this.$route.params.id);
    this.user_id = this.$route.params.id;
    let resCustomer = await this.fetchCustomer(this.$route.params);
    //console.log('customer: ', resCustomer);
    this.isAvalara = resCustomer.data.customer.avalara_bool;
    // this.$refs.name.focus = true
  },
  watch: {
    billing_country(newCountry) {
      if (newCountry) {
        // console.log('aaa');
        //console.log(newCountry);
        this.formData.country_id = newCountry.id
        // if (this.isEdit) { this.$v.formData.billing.country_id.$reset() }
      } 
      else {
        this.formData.country_id = null
      }
    },
    billing_state(newState) {
      if (newState) {
        this.formData.state_id = newState.id
        // if (this.isEdit) { this.$v.formData.billing.state_id.$reset() }
      } 
      else {
        this.formData.state_id = null
      }
    },
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('customerAddress', [
        'addAddress',
        'updateCustomerAddress',
        /* 'fetchTaxType' */
      ]),
    ...mapActions('customer', [
      'fetchCustomer',
      'billingValidation',
      /* 'addCustomer',
      'updateCustomer', */
    ]),
     ...mapActions('modal', ['openModal']),

    async checkBilling() {
      this.isLoading = true
      this.$v.formData.country_id.$touch()
      this.$v.formData.state_id.$touch()
      this.$v.formData.city.$touch()
      this.$v.formData.zip.$touch()

      if (
        this.$v.formData.country_id.$invalid ||
        this.$v.formData.state_id.$invalid ||
        this.$v.formData.city.$invalid ||
        this.$v.formData.zip.$invalid
      ) {
        this.isLoading = false
        return true
      }

      let data = {
        country: this.billing_country.code,
        state: this.billing_state.code,
        city: this.formData.city,
        zip_code: this.formData.zip,
      }

      let response = await this.billingValidation(data)      

      if (response.data.check.success) {
        let dataModal = [...response.data.check.data]
        if (dataModal.length > 0) {
          dataModal.forEach((element) => {
            element.customerAvalaraLocationId =
              this.customer_avalara_location_id
            for (const key in element) {
              // validar si hay un campo en null (nulo)
              if (
                (!element[key] || element[key] === '') &&
                key != 'customerAvalaraLocationId'
              ) {
                element.valid = false
                break
              } else {
                element.valid = true
              }
            }
          })

          // Information that the company currently has
          //console.log(this.formData)
          
          dataModal[0].company_geo_info = {
            country: this.billing_country,
            state: this.billing_state,
            city: this.formData.city,
            zip: this.formData.zip,
            edit: this.isEdit,
            type:this.formData.type,
            address_street_1:this.formData.address_street_1,
            address_street_2:this.formData.address_street_2
          }

          // de traer direcciones invocar modal
          this.openModal({
            title: this.$t('avalara.billing_location_modal.title'),
            componentName: 'AvalaraBillingLocationModal',
            id: this.$route.params.id,
            data: dataModal,
            variant: 'lg',
            company: 0,
          })
          this.isLoading = false
          this.isAvalaraLocationValidated = true;
          return true
        }

        window.toastr['error'](this.$t('avalara.billing_location_error'))
        this.isLoading = false
        return true
      }

      window.toastr['error'](response.data.check.message)
      this.isLoading = false
      return true
    },

    resetFormData() {
      this.$v.formData.$reset()
    },

    async deleteCountry() {
      this.isLoading = true
      this.formData.country = null
      this.formData.state = null
      // console.log('country form data: ', this.formData.country);
      this.isLoading = false
    },
    async deleteState() {
      this.isLoading = true
      this.formData.state = null
      // console.log('state form data: ', this.formData.state);
      this.isLoading = false
    },
    async loadData() {
      let res = await window.axios.get('/api/v1/countries')

      if (res) {
        this.countries = res.data.countries
      }

      if (this.modalDataID) {
        this.isEdit = true
        this.setData()
        // this.$v.formData.$reset()
      }
    },

    async submitCustomerAddressData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      if (this.isAvalara && !this.isAvalaraLocationValidated) {
        window.toastr['error'](this.$t('customer_address.location_not_validated_message')) 
        return true;
      }

      this.isLoading = true
      let response
      this.formData.user_id = this.user_id;

      /* visa: 3.39
    master: 2.86 */

      /* console.log('form data: ', this.formData)
      console.log('edit: ', this.isEdit) */

      if (!this.isEdit) {
        response = await this.addAddress(this.formData)
      } else {
        this.formData.id = this.modalData.id;
        response = await this.updateCustomerAddress(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](
            this.$t('customer_address.created_message')
          )
        } else {
          window.toastr['success'](
            this.$t('customer_address.updated_message')
          )
        }

        /* if (this.props) {
          if (this.props.emitName) {
            window.hub.$emit(this.props.emitName, response.data.taxType)
          } else {
            window.hub.$emit('newTax', response.data.taxType)
          }
        } */

        // this.refreshData()
        // this.refreshData ?  : ''
        this.closeAddressModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData() {
      // this.isLoading = true

      if (this.modalData) {
        // console.log('modal data: ', this.modalData);
        this.formData.country_id = this.modalData.country_id;
        this.formData.state_id = this.modalData.state_id;
        this.formData.city = this.modalData.city;
        this.formData.county = this.modalData.county;
        this.formData.phone = this.modalData.phone;
        this.formData.zip = this.modalData.zip;
        this.formData.address_street_1 = this.modalData.address_street_1;
        this.formData.address_street_2 = this.modalData.address_street_2;
        // this.formData.type = this.modalData.address.type;
        
        if (this.modalData.country_id) {
          this.isLoadingCountryState = true
          this.billing_country = this.countries.filter(
            (c) => c.id == this.modalData.country_id
          )
          this.billing_country = this.billing_country[0]
          let res = await this.countrySelected(this.billing_country)
          
          if (res && this.modalData.state_id) {
            //
            this.billing_state = this.states.filter(
              (s) => s.id == this.modalData.state_id
            )
            this.billing_state = this.billing_state[0]
          }
          // this.$v.formData.$reset()
          this.isLoadingCountryState = false
        }

      } else {
        this.formData = {
          name: null,
          country_id: null,
          state_id: null,
          city: null,
          county: null,
          phone: null,
          zip: null,
          address_street_1: null,
          address_street_2: null,
          type: 'services_address'
        }
      }

      
      // console.log('form data billing: ', this.formData.billing);
      
    },
    async countrySelected(country) {
      let res = await window.axios.get('/api/v1/states/' + country.code)
      this.states = res.data.states
      return true
    },
    stateSelected(state) {
      this.isLoadingCountryState = true
      this.formData.state = state
      //console.log(this.formData.state)
      this.isLoadingCountryState = false
    },
    /* categorySelected(category) {
      this.isLoadingCategory = true
      this.formData.category = category
      this.isLoadingCategory = false
    }, */
    closeAddressModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()

      setTimeout(() => {
        window.location.reload();
      }, 1500);
    },
  },
}
</script>
