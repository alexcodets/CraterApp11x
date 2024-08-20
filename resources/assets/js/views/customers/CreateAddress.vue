<template>
  <!-- Base  -->
  <base-page v-if="isSuperAdmin" class="option-group-create">
    <!--------- Form ---------->
    <form action="" @submit.prevent="submitCustomerAddressData">
      <!-- Header  -->
      <sw-page-header class="mb-3" :title="pageTitle">
        <template slot="actions">
          <sw-button
            class="mr-3 text-sm hidden sm:flex"
            variant="primary-outline"
            type="button"
            @click="closeAddressForm"
          >
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="hidden sm:flex"
          >
            <save-icon class="mr-2" v-if="!isLoading" />
            {{ !isEdit ? $t('general.save') : $t('general.update') }}
          </sw-button>
        </template>
      </sw-page-header>
      <sw-card class="mb-8">
        <div class="grid md:grid-cols-2 lg:p-8 sm:p-1 col-span-5">
          <!-- <div class="col-span-12"> -->

            <sw-input-group
            :label="$t('customers.address')"
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

          <sw-input-group
            :label="$t('customers.address_2')"
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

          <sw-input-group
            :label="$t('customers.city')"
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

          <sw-input-group
            :label="$t('customers.county')"
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


          <sw-input-group
            :label="$t('customers.state')"
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

     

          <sw-input-group
            :label="$t('customers.zip_code')"
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

          


          <sw-input-group
            :label="$t('customers.country')"
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

          <!-- billing validation button -->
          <div v-if="isAvalaraAvailable">
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

          <div v-if="isAvalaraValidation">
            <sw-input-group
              :label="$t('avalara.pcode')"
              variant="horizontal"
              class="mt-5"
            >
              <sw-input
                v-model.trim="formData.pcode"
                type="text"
                name="pcode"
                :disabled="true"
              />
            </sw-input-group>
          </div>

          <!-- </div> -->
        </div>

        <sw-button
          class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
          variant="primary-outline"
          type="button"
          @click="closeAddressForm"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          variant="primary"
          type="submit"
          class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
        >
          <save-icon class="mr-2" v-if="!isLoading" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon, CheckIcon } from '@vue-hero-icons/solid'
const { required, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    XCircleIcon,
    CheckIcon,
  },
  data() {
    return {
      isAvalaraAvailable: false,
      // isEdit: false,
      isAvalara: false,
      isLoading: false,
      isLoadingCountryState: false,
      isAvalaraLocationValidated: false,
      billing_state: null,
      billing_country: null,
      // billing_delivery_method: { name: 'Email', value: 'Email' },
      formData: {
        id: null,
        name: null,
        country_id: null,
        state_id: null,
        city: null,
        county: null,
        phone: null,
        zip: null,
        pcode: null,
        address_street_1: null,
        address_street_2: null,
        type: '',
      },
      countries: [],
      states: [],
      billing_states: [],
      categories: [],
      user_id: null,
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
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('avalara', ['avalaraLocationToSave']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    isEdit() {
      if (this.$route.name === 'customers.edit-address') {
        return true
      }
      return false
    },
    pageTitle() {
      if (this.$route.name === 'customers.edit-address') {
        return this.$t('customer_address.edit_address')
      }
      return this.$t('customer_address.new_address')
    },
    isAvalaraValidation() {
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
  beforeDestroy() {
    this.unsubscribe()
  },
  created() {
    this.isLoading = true
    this.loadData()
    this.subscribeAvalaraBillingInfo()
    this.getStatusModuleAvalara()
    this.isLoading = false
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
    window.hub.$on('save-address', this.addressValidate)
    this.user_id = this.$route.params.id
    let resCustomer = await this.fetchCustomer(this.$route.params)
    this.isAvalara = resCustomer.data.customer.avalara_bool
    // this.$refs.name.focus = true
  },
  watch: {
    billing_country(newCountry) {
      if (newCountry) {
        this.formData.country_id = newCountry.id
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
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('customerAddress', [
      'addAddress',
      'updateCustomerAddress',
      'fetchCustomerAddress',
    ]),
    ...mapActions('customer', [
      'fetchCustomer',
      'billingValidation',
      /* 'addCustomer',
      'updateCustomer', */
    ]),
    ...mapActions('modal', ['openModal']),
    ...mapActions('avalara', ['saveAvalaraLocation', 'checkStatusAvalara']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('user', ['fetchCurrentUser']),

    async subscribeAvalaraBillingInfo() {
      this.unsubscribe = this.$store.subscribe((mutation, state) => {
        if (mutation.type === 'avalara/SET_AVALARA_LOCATION_DATA') {
          if (this.avalaraLocationToSave) {
            if (this.avalaraLocationToSave.country) {
              /* let country = this.countries.filter(c => c.code === this.avalaraLocationToSave.country)
              this.formData.billing.country_id = country.id
              this.$v.formData.billing.country_id.$touch() */
            }
            if (this.avalaraLocationToSave.state) {
              // this.formData.billing.country_id =
            }

            if (this.avalaraLocationToSave.locality) {
              this.formData.city = this.avalaraLocationToSave.locality
            }
            if (this.avalaraLocationToSave.county) {
              this.formData.county = this.avalaraLocationToSave.county
            }
            if (this.avalaraLocationToSave.zip) {
              this.formData.zip = this.avalaraLocationToSave.zip
            }
          }
        }
      })
    },

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
              if (data.country == 'US') {
                if (
                  (!element[key] || element[key] === '') &&
                  key != 'customerAvalaraLocationId'
                ) {
                  element.valid = false
                  break
                } else {
                  element.valid = true
                }
              } else {
                if (
                  (!element[key] || element[key] === '') &&
                  key != 'customerAvalaraLocationId' &&
                  key != 'County' &&
                  key != 'Locality' &&
                  key != 'State'
                ) {
                  element.valid = false
                  break
                } else {
                  element.valid = true
                }
              }
            }
          })

          // Information that the company currently has

          dataModal[0].company_geo_info = {
            country: this.billing_country,
            state: this.billing_state,
            city: this.formData.city,
            county: this.formData.county,
            zip: this.formData.zip,
            edit: this.isEdit,
            type: this.formData.type,
            address_street_1: this.formData.address_street_1,
            address_street_2: this.formData.address_street_2,
          }

          // de traer direcciones invocar modal
          this.openModal({
            title: this.$t('avalara.billing_location_modal.title'),
            componentName: 'AddressBillingValidationModal',
            id: this.$route.params.id,
            data: dataModal,
            variant: 'lg',
            company: 0,
          })
          this.isLoading = false
          this.isAvalaraLocationValidated = true
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

    addressValidate({ pcode, city, county }) {
      this.formData.pcode = pcode
      this.formData.city = city
      this.formData.county = county
    },

    resetFormData() {
      this.$v.formData.$reset()
    },

    async deleteCountry() {
      this.isLoading = true
      this.formData.country = null
      this.formData.state = null
      this.isLoading = false
    },
    async deleteState() {
      this.isLoading = true
      this.formData.state = null
      this.isLoading = false
    },
    async loadData() {
      const data = {
        module: 'cust_address',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions.create == 0 && this.isEdit == false) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.update == 0 && this.isEdit == true) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      let res = await window.axios.get('/api/v1/countries')

      if (res) {
        this.countries = res.data.countries
      }

      let response = await this.fetchCurrentUser()
      if (response.data.user) { 
        let userCompany = response.data.user.company
        this.billing_country= userCompany.address ? userCompany.address.country : ''
        if(this.billing_country != ''){
          this.countrySelected(this.billing_country)
        }
      }

      if (this.isEdit) {
        // this.isEdit = true
        this.setData()
        // this.$v.formData.$reset()
      }
    },
    async submitCustomerAddressData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      if (
        this.isAvalara &&
        (!this.isAvalaraLocationValidated || this.formData.pcode == null)
      ) {
        window.toastr['error'](
          this.$t('customer_address.location_not_validated_message')
        )
        return true
      }

      this.isLoading = true
      let response
      this.formData.user_id = this.user_id
      this.formData.pcode = this.avalaraLocationToSave.pcd || null
      this.formData.type = !this.isEdit
        ? 'services_address'
        : this.formData.type

      if (!this.isEdit) {
        response = await this.addAddress(this.formData)
      } else {
        this.formData.id = this.$route.params.idAddress
        response = await this.updateCustomerAddress(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          if (this.isAvalara) {
            this.avalaraLocationToSave.addresses_id =
              response.data.customerAddress.id
            let response2 = await this.saveAvalaraLocation(
              this.avalaraLocationToSave
            ) // save avalara location

            if (response2.data) {
              window.toastr['success'](
                this.$t('avalara.billing_location_modal.created_message')
              )
            }
          }

          window.toastr['success'](this.$t('customer_address.created_message'))
        } else {
          window.toastr['success'](this.$t('customer_address.updated_message'))
        }

        this.$router.push(
          '/admin/customers/' + this.$route.params.id + '/address'
        )
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData() {
      this.isLoading = true
      let res = await this.fetchCustomerAddress(this.$route.params.idAddress)

      if (res.data.success) {
        this.formData.country_id = res.data.customerAddress.country_id
        this.formData.state_id = res.data.customerAddress.state_id
        this.formData.city = res.data.customerAddress.city
        this.formData.county = res.data.customerAddress.county
        this.formData.phone = res.data.customerAddress.phone
        this.formData.zip = res.data.customerAddress.zip
        this.formData.pcode = res.data.customerAddress.pcode
        this.formData.address_street_1 =
          res.data.customerAddress.address_street_1
        this.formData.address_street_2 =
          res.data.customerAddress.address_street_2
        this.formData.type = res.data.customerAddress.type

        if (res.data.customerAddress.country_id) {
          this.isLoadingCountryState = true
          this.billing_country = this.countries.filter(
            (c) => c.id == this.formData.country_id
          )
          this.billing_country = this.billing_country[0]
          let res2 = await this.countrySelected(this.billing_country)

          if (res2 && res.data.customerAddress.state_id) {
            //
            this.billing_state = this.states.filter(
              (s) => s.id == this.formData.state_id
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
          pcode: null,
          address_street_1: null,
          address_street_2: null,
          type: 'services_address',
        }
      }
      this.isLoading = false
    },
    async countrySelected(country) {
      this.billing_state = null
      //this.formData.pcode = null
      let res = await window.axios.get('/api/v1/states/' + country.code)
      this.states = res.data.states
      return true
    },
    stateSelected(state) {
      this.isLoadingCountryState = true
      this.formData.state = state
      this.formData.pcode = null
      this.isLoadingCountryState = false
    },

    closeAddressForm() {
      this.$router.push(
        '/admin/customers/' + this.$route.params.id + '/address'
      )
    },
    async getStatusModuleAvalara() {
      const response = await this.checkStatusAvalara()
      this.isAvalaraAvailable = response.data.success
    },
  },
}
</script>

<style scoped></style>
