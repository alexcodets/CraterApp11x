<template>
  <div class="tax-type-modal">
    <form action="" @submit.prevent="submitTaxAgencyData">
      <div class="grid md:grid-cols-2 p-8 sm:p-6 col-span-5" >

        <!-- <div class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-2" > -->

          <sw-input-group :label="$t('settings.tax_agency.name')"
            :error="nameError"
            class="mt-4"
            variant="horizontal"
            required
          >
            <sw-input
              ref="name"
              :invalid="$v.formData.name.$error"
              v-model="formData.name"
              type="text"
              @input="$v.formData.name.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('settings.tax_agency.number')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-input
              ref="number"
              v-model="formData.number"
              type="text"
            />
          </sw-input-group>

          <sw-input-group :label="$t('settings.tax_agency.email')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-input
              ref="email"
              v-model="formData.email"
              type="text"
            />
          </sw-input-group>

          <sw-input-group :label="$t('settings.tax_agency.phone')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-input ref="phone" v-model="formData.phone" type="text" />
          </sw-input-group>

          <sw-input-group :label="$t('settings.tax_agency.website')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-input ref="website" v-model="formData.website" type="text" />
          </sw-input-group>

          <sw-input-group :label="$t('settings.tax_agency.note')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-textarea
              v-model.trim="formData.note"
              type="text"
              name="note"
              rows="3"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.country')"
            :error="countryIdError"
            class="mt-4"
            required
            variant="horizontal"
          >
            <sw-select
              v-if="!isLoadingCountryState"
              v-model="billing_country"
              :invalid="$v.formData.billing.country_id.$error"
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
              :invalid="$v.formData.billing.state_id.$error"
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
              v-model="formData.billing.city"
              :invalid="$v.formData.billing.city.$error"
              name="formData.billing.city"
              type="text"
              @input="$v.formData.billing.city.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.county')" 
            class="mt-4"
            variant="horizontal"
          >
            <sw-input
              v-model="formData.billing.county"
              name="formData.billing.county"
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
              v-model.trim="formData.billing.address_street_1"
              :invalid="$v.formData.billing.address_street_1.$error"
              :placeholder="$t('general.street_1')"
              type="text"
              name="billing_street1"
              rows="1"
              @input="$v.formData.billing.address_street_1.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.address')"
            :error="billAddress2Error"
            variant="horizontal"
            class="mt-4"
          >
            <sw-textarea
              v-model.trim="formData.billing.address_street_2"
              :placeholder="$t('general.street_2')"
              type="text"
              name="billing_street2"
              rows="1"
              @input="$v.formData.billing.address_street_2.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('customers.zip_code')"
            :error="zipError"
            required
            variant="horizontal"
            class="mt-4"
          >
            <sw-input
              v-model.trim="formData.billing.zip"
              :invalid="$v.formData.billing.zip.$error"
              type="text"
              name="zip"
              @input="$v.formData.billing.zip.$touch()"
            />
          </sw-input-group>

          <!-- <sw-input-group :label="$t('customers.tax_exempt')"
            :error="zipError"
            required
            variant="horizontal"
            class="mt-4"
          >
            <sw-switch
                v-model="formData.billing.tax_exempt"
                style="position: relative;top: -12px;"
              />
          </sw-input-group> -->

          <!-- <sw-input-group :label="$t('customers.tax_id_vatin')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-input
              v-model="formData.billing.tax_id_vatin"
              type="text"
              name="tax_id_vatin"
            />
          </sw-input-group>

          <sw-input-group :label="$tc('customers.delivery_methods')"
            class="mt-4"
            variant="horizontal"
          >
            <sw-select
              v-model="billing_delivery_method"
              :options="delivery_methods"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$tc('customers.select_method')"
              class="mt-2"
              label="name"
              track-by="id"
            />
          </sw-input-group> -->

          <!-- <sw-input-group :label="$t('customers.payment_notices')"
            :error="zipError"
            required
            variant="horizontal"
            class="mt-4"
          >
            <sw-switch
                v-model="formData.billing.payment_notices"
                style="position: relative;top: -12px;"
              />
          </sw-input-group> -->


        <!-- </div> -->
        
      </div>

      <div class="z-0 flex justify-end p-4 border-t border-solid border--200 border-modal-bg mt-8" >
        <sw-button
          class="mr-3 text-sm"
          variant="primary-outline"
          type="button"
          @click="closeTaxModal"
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
import { XCircleIcon } from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  between,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    XCircleIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      isLoadingCountryState: false,
      billing_state: null,
      billing_country: null,
      billing_delivery_method: { name: 'Email', value: 'Email' },
      formData: {
        id: null,
        name: null,
        number: '',
        email: null,
        phone: null,
        website: null,
        note: null,
        billing: {
          name: null,
          country_id: null,
          state_id: null,
          city: null,
          county: null,
          phone: null,
          zip: null,
          address_street_1: null,
          address_street_2: null,
          type: 'billing',
          // tax_exempt: false,
          // tax_id_vatin: null,
          // delivery_method: null,
          // payment_notices: false,
        },
      },
      countries: [],
      states: [],
      billing_states: [],
      categories: [],
      delivery_methods: [
        { id: 1, name: 'Email', value: 'Email' },
        { id: 2, name: 'Paper', value: 'Paper' },
        { id: 3, name: 'InterFax', value: 'InterFax' },
        { id: 4, name: 'PostalMethods', value: 'PostalMethods' },
      ],
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
      'props',
    ]),
    // ...mapGetters('taxGroups', ['taxGroups']),

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    countryIdError() {
      if (!this.$v.formData.billing.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.billing.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    stateIdError() {
      if (!this.$v.formData.billing.state_id.$error) {
        return ''
      }
      if (!this.$v.formData.billing.state_id.required) {
        return this.$tc('validation.required')
      }
    },
    cityError() {
      if (!this.$v.formData.billing.city.$error) {
        return ''
      }
      if (!this.$v.formData.billing.city.required) {
        return this.$tc('validation.required')
      }
    },
    zipError() {
      if (!this.$v.formData.billing.zip.$error) {
        return ''
      }
      if (!this.$v.formData.billing.zip.required) {
        return this.$tc('validation.required')
      }
    },
    billAddress1Error() {
      if (!this.$v.formData.billing.address_street_1.$error) {
        return ''
      }
      if (!this.$v.formData.billing.address_street_1.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.billing.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    billAddress2Error() {
      if (!this.$v.formData.billing.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.billing.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },

  },
  created() {
    this.loadData()
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      billing: {
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
  },
  async mounted() {
    this.$refs.name.focus = true
  },
  watch: {
    billing_country(newCountry) {
      if (newCountry) {
        //console.log('aaa');
        //console.log(newCountry);
        this.formData.billing.country_id = newCountry.id
        // if (this.isEdit) { this.$v.formData.billing.country_id.$reset() }
      } 
      else {
        this.formData.billing.country_id = null
      }
      

    },
    billing_state(newState) {
      if (newState) {
        this.formData.billing.state_id = newState.id
        // if (this.isEdit) { this.$v.formData.billing.state_id.$reset() }
      } 
      else {
        this.formData.billing.state_id = null
      }
    },
    
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('taxAgency', [
        'addTaxAgency', 
        'updateTaxAgency', 
        /* 'fetchTaxType' */
      ]),

    resetFormData() {
      /* this.formData = {
        id: null,
        name: null,
      } */
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

      /* if (!this.isEdit) {
        this.fetchTaxGroups({ limit: 'all' })
      } */
    },
    async submitTaxAgencyData() {
      this.$v.formData.$touch()
      //console.log('form data: ', this.$v.formData.billing.country_id);
      // this.$v.formData.$reset()
      //console.log('error: ', this.$v.formData.billing.country_id.$error);
      if (this.$v.$invalid) {
        // this.$v.formData.$reset()
        return true
      }
      this.isLoading = true
      let response

      /* if (this.formData.country) {
        this.formData.country_id = this.formData.country.id
      }

      if (this.formData.state) {
        this.formData.state_id = this.formData.state.id
      }

      if (this.formData.category) {
        this.formData.tax_category_id = this.formData.category.id
      } */

      /* visa: 3.39
    master: 2.86 */
      //console.log(this.formData)

      if (!this.isEdit) {
        response = await this.addTaxAgency(this.formData)
      } else {
        response = await this.updateTaxAgency(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](
            this.$t('settings.tax_types.created_message')
          )
        } else {
          window.toastr['success'](
            this.$t('settings.tax_types.updated_message')
          )
        }

        if (this.props) {
          if (this.props.emitName) {
            window.hub.$emit(this.props.emitName, response.data.taxType)
          } else {
            window.hub.$emit('newTax', response.data.taxType)
          }
        }

        this.refreshData ? this.refreshData() : ''
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData() {
      // this.isLoading = true
      // console.log('modal data: ', this.modalData);

      this.formData.id = this.modalData.id
      this.formData.name = this.modalData.name
      this.formData.number = this.modalData.number
      this.formData.email = this.modalData.email
      this.formData.phone = this.modalData.phone
      this.formData.website = this.modalData.website
      this.formData.note = this.modalData.note

      if (this.modalData.address) {
        this.formData.billing.name = this.modalData.address.name;
        this.formData.billing.country_id = this.modalData.address.country_id;
        this.formData.billing.state_id = this.modalData.address.state_id;
        this.formData.billing.city = this.modalData.address.city;
        this.formData.billing.county = this.modalData.address.county;
        this.formData.billing.phone = this.modalData.address.phone;
        this.formData.billing.zip = this.modalData.address.zip;
        this.formData.billing.address_street_1 = this.modalData.address.address_street_1;
        this.formData.billing.address_street_2 = this.modalData.address.address_street_2;
        this.formData.billing.type = this.modalData.address.type;
        
        if (this.modalData.address.country_id) {
          this.isLoadingCountryState = true
          this.billing_country = this.countries.filter(
            (c) => c.id == this.modalData.address.country_id
          )
          this.billing_country = this.billing_country[0]
          let res = await this.countrySelected(this.billing_country)
          
          if (res && this.modalData.address.state_id) {
            //
            this.billing_state = this.states.filter(
              (s) => s.id == this.modalData.address.state_id
            )
            this.billing_state = this.billing_state[0]
          }
          // this.$v.formData.$reset()
          this.isLoadingCountryState = false
        }

      } else {
        this.formData.billing = {
          name: null,
          country_id: null,
          state_id: null,
          city: null,
          county: null,
          phone: null,
          zip: null,
          address_street_1: null,
          address_street_2: null,
          type: 'billing'
        }
      }

      
      // console.log('form data billing: ', this.formData.billing);
      
    },
    async countrySelected(country) {
      // console.log('country: ', country);
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
    categorySelected(category) {
      this.isLoadingCategory = true
      this.formData.category = category
      this.isLoadingCategory = false
    },
    closeTaxModal() {

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.resetModalData()
          this.resetFormData()
          this.closeModal()
        }
      })
    },
  },
}
</script>
