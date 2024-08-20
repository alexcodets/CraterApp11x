<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <form action="" @submit.prevent="submitProvider">
      <sw-page-header class="mb-3" :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="/admin/dashboard"
            :title="$t('general.home')"
          />
          <sw-breadcrumb-item
            to="/admin/providers"
            :title="$tc('providers.provider', 2)"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'providers.edit'"
            to="#"
            :title="$t('providers.edit_provider')"
            active
          />
          <sw-breadcrumb-item
            v-else
            to="#"
            :title="$t('providers.new_provider')"
            active
          />
        </sw-breadcrumb>
        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary-outline"
            type="button"
            size="lg"
            tabindex="18"
            class="mr-3 text-sm hidden sm:flex"
            @click="cancelForm()"
          >
            <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('general.cancel') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex"
            tabindex="18"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('providers.update_provider')
                : $t('providers.save_provider')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <div class="grid grid-cols-12">
        <div class="col-span-12 md:col-span-12">
          <sw-card>
            <div class="grid-cols-12 gap-8 mt-6 mb-8 lg:grid">
              <!-- First Section-->
              <div
                class="grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-3"
              >
                <!-- COMPANY NAME-->
                <sw-input-group
                  :label="$t('providers.titl')"
                  :error="titleError"
                  class="mb-4"
                  required
                >
                  <sw-input
                    v-model.trim="formData.title"
                    :invalid="$v.formData.title.$error"
                    class="mt-2"
                    focus
                    type="text"
                    name="title"
                    tabindex="18"
                    @input="$v.formData.title.$touch()"
                  />
                </sw-input-group>

                <!-- STATUS -->
                <sw-input-group
                  :label="$t('providers.status')"
                  class="mb-4"
                  required
                >
                  <sw-select
                    v-model="formData.status"
                    :options="status"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="18"
                    :allow-empty="false"
                    class="mt-2"
                    :placeholder="$t('providers.status')"
                    label="text"
                    track-by="value"
                  />
                </sw-input-group>

                <!-- EMAIL -->
                <sw-input-group
                  :label="$t('providers.email')"
                  class="mb-4"
                  :error="emailError"
                  required
                >
                  <sw-input
                    :invalid="$v.formData.email.$error"
                    v-model.trim="formData.email"
                    class="mt-2"
                    type="text"
                    name="email"
                    tabindex="18"
                    @input="$v.formData.email.$touch()"
                  />
                </sw-input-group>

                <!-- PHONE -->
                <sw-input-group
                  :label="$t('providers.phone')"
                  class="mb-4"
                  :error="phoneError"
                  required
                >
                  <sw-input
                    v-model.trim="formData.phone"
                    :invalid="$v.formData.phone.$error"
                    class="mt-2"
                    focus
                    type="tel"
                    name="phone"
                    pattern="^[0-9]+$"
                    :tabindex="18"
                    @input="$v.formData.phone.$touch()"
                  />
                </sw-input-group>

                <!-- FIRST NAME -->
                <sw-input-group
                  :label="$t('providers.first_name')"
                  class="mb-4"
                >
                  <sw-input
                    v-model.trim="formData.first_name"
                    class="mt-2"
                    focus
                    type="text"
                    name="first_name"
                    :tabindex="18"
                  />
                </sw-input-group>

                <!-- LAST NAME -->
                <sw-input-group :label="$t('providers.last_name')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.last_name"
                    class="mt-2"
                    focus
                    type="text"
                    name="last_name"
                    :tabindex="18"
                  />
                </sw-input-group>

                <!-- MOBILE -->
                <sw-input-group :label="$t('providers.mobile')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.mobile"
                    class="mt-2"
                    focus
                    type="text"
                    name="mobile"
                    pattern="^[0-9]+$"
                    tabindex="18"
                  />
                </sw-input-group>

                <!-- WEBSITE -->
                <sw-input-group
                  :label="$t('providers.webside')"
                  :error="urlError"
                  class="mb-4"
                >
                  <sw-input
                    v-model="formData.webside"
                    :invalid="$v.formData.webside.$error"
                    class="mt-2"
                    type="url"
                    tabindex="18"
                  />
                </sw-input-group>

                <!-- ACCOUNT N° -->
                <sw-input-group
                  :label="$t('providers.account_no')"
                  class="mb-4"
                >
                  <sw-input
                    v-model.trim="formData.account_no"
                    class="mt-2"
                    focus
                    type="text"
                    name="account_no"
                    tabindex="18"
                  />
                </sw-input-group>

                <!-- BUSINESS ID -->
                <sw-input-group
                  :label="$t('providers.business_no')"
                  class="mb-4"
                >
                  <sw-input
                    v-model.trim="formData.business_no"
                    class="mt-2"
                    focus
                    type="text"
                    name="business_no"
                    tabindex="18"
                  />
                </sw-input-group>

                <!-- TRACK PAYMENTS ID -->
                <sw-input-group
                  :label="$t('providers.track_payments')"
                  class="mb-4"
                >
                  <div class="flex my-5 mb-4">
                    <div class="relative w-12">
                      <sw-switch
                        v-model="formData.track_payments"
                        class="absolute mt-2"
                        style="top: -30px"
                        tabindex="18"
                      />
                    </div>

                    <div class="ml-4">
                      <p
                        class="p-0 mb-1 text-base leading-snug text-black box-title"
                      >
                        {{ $t('providers.track_payments') }}
                      </p>
                    </div>
                  </div>
                </sw-input-group>

                <!-- TERMS -->
                <sw-input-group :label="$t('providers.terms')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.terms"
                    class="mt-2"
                    focus
                    type="text"
                    name="terms"
                    tabindex="18"
                  />
                </sw-input-group>
              </div>

              <sw-divider class="my-0 col-span-12 opacity-1" />

              <!-- Second Section-->
              <div
                class="grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-3"
              >
                <!-- STREET -->
                <sw-input-group :label="$t('providers.street')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.street"
                    class="mt-2"
                    focus
                    type="text"
                    name="street"
                    :tabindex="18"
                  />
                </sw-input-group>

                <!-- CITY -->
                <sw-input-group :label="$t('providers.city')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.city"
                    class="mt-2"
                    focus
                    type="text"
                    name="city"
                    :tabindex="18"
                  />
                </sw-input-group>

                <!-- COUNTY -->
                <sw-input-group :label="'County'" class="mb-4">
                  <sw-input
                    v-model.trim="formData.county"
                    class="mt-2"
                    focus
                    type="text"
                    name="county"
                    :tabindex="18"
                  />
                </sw-input-group>
                <!-- ZIP CODE -->
                <sw-input-group :label="$t('providers.zip_code')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.zip_code"
                    class="mt-2"
                    focus
                    type="text"
                    name="zip_code"
                    :tabindex="18"
                  />
                </sw-input-group>

                <!-- STATE -->
                <sw-input-group :label="$t('providers.state')" class="mb-4">
                  <sw-select
                    v-model="formData.states"
                    :options="states"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :tabindex="18"
                    class="mt-2"
                    :placeholder="$t('general.select_state')"
                    label="name"
                    track-by="id"
                    select="stateSeleted"
                  />
                </sw-input-group>

                <!-- COUNTRY -->
                <sw-input-group :label="$t('providers.country')" class="mb-4">
                  <sw-select
                    v-model="formData.countries"
                    :options="countries"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :tabindex="18"
                    class="mt-2"
                    :placeholder="$t('general.select_country')"
                    label="name"
                    track-by="id"
                    @select="countrySeleted"
                  />
                </sw-input-group>
              </div>

              <sw-divider class="my-0 col-span-12 opacity-1" />

              <!-- Third Section-->
              <div
                class="grid grid-cols-1 col-span-12 gap-4 mt-8 lg:gap-4 lg:mt-0 lg:grid-cols-1"
              >
                <sw-input-group :label="$t('groups.description')" class="mb-4">
                  <base-custom-input
                    v-model.trim="formData.description"
                    class="mb-4"
                    tabindex="18"
                  />
                </sw-input-group>
              </div>
            </div>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                :disabled="isLoading"
                class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
                type="button"
                size="lg"
                tabindex="18"
                @click="cancelForm()"
              >
                <x-circle-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ $t('general.cancel') }}
              </sw-button>

              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                tabindex="18"
                class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
              >
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{
                  isEdit
                    ? $t('providers.update_provider')
                    : $t('providers.save_provider')
                }}
              </sw-button>
            </div>
          </sw-card>
        </div>
      </div>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import CustomFieldsMixin from '../../mixins/customFields'
import {
  CloudUploadIcon,
  ShoppingCartIcon,
  TrashIcon,
  EyeIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  email,
  url,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    XCircleIcon,
  },
  mixins: [CustomFieldsMixin],
  data() {
    return {
      isLoading: false,
      title: 'Add Provider',
      countries: [],
      states: [],
      status: [
        {
          value: 'A',
          text: this.$t('general.active'),
        },
        {
          value: 'T',
          text: this.$t('general.inactive'),
        },
      ],

      formData: {
        title: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        suffix: '',
        company: '',
        display_name: '',
        country: '',
        state: '',
        city: '',
        county: '',
        street: '',
        zip_code: '',
        description: '',
        phone: '',
        mobile: '',
        fax: '',
        other: '',
        webside: '',
        terms: '',
        opening_balance: '',
        as_of: null,
        account_no: '',
        business_no: '',
        track_payments: '',
        default_expense_account: '',
        countries: [],
        states: [],
        status: {
          value: 'A',
          text: this.$t('general.active'),
        },
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    pageTitle() {
      if (this.$route.name === 'providers.edit') {
        return this.$t('providers.edit_provider')
      }
      return this.$t('providers.new_provider')
    },
    isEdit() {
      if (this.$route.name === 'providers.edit') {
        return true
      }
      return false
    },
    titleError() {
      if (!this.$v.formData.title.$error) {
        return ''
      }
      if (!this.$v.formData.title.required) {
        return this.$t('validation.required')
      }
    },
    /*
    firstNameError() {
      if (!this.$v.formData.first_name.$error) {
        return ''
      }
      if (!this.$v.formData.first_name.required) {
        return this.$t('validation.required')
      }
    },
    lastNameError() {
      if (!this.$v.formData.last_name.$error) {
        return ''
      }
      if (!this.$v.formData.last_name.required) {
        return this.$t('validation.required')
      }
    },
    */
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    phoneError() {
      if (!this.$v.formData.phone.$error) {
        return ''
      }
      if (!this.$v.formData.phone.required) {
        return this.$t('validation.required')
      }
    },
    urlError() {
      if (!this.$v.formData.webside.$error) {
        return ''
      }

      if (!this.$v.formData.webside.url) {
        return this.$tc('validation.invalid_url')
      }
    },
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    this.fetchInitDataCountry()
    if (this.isEdit) {
      this.loadEditData()
    }
  },
  mounted() {
    this.$v.formData.$reset()
  },
  validations: {
    formData: {
      title: {
        required,
      },
      /*
      first_name: {
        required,
      },
      last_name: {
        required,
      },
      */
      email: {
        required,
        email,
      },

      phone: {
        required,
      },
      webside: {
        url,
      },
    },
  },
  methods: {
    ...mapActions('providers', [
      'addProvider',
      'fetchProvider',
      'updateProvider',
    ]),
    ...mapActions('user', ['fetchCurrentUser']),

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: 'You may lose unsaved information',
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          if (this.isEdit) {
            this.$router.push(`/admin/providers/${this.$route.params.id}/view`)
          } else {
            this.$router.go(-1)
          }
        }
      })
    },

    async fetchInitDataCountry() {
      this.initLoad = true
      
     
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      let response = await this.fetchCurrentUser()
      if (response.data.user) { 
        let userCompany = response.data.user.company
        this.formData.countries = userCompany.address ? userCompany.address.country : ''
        if(this.formData.countries != ''){
           this.countrySeleted(this.formData.countries)
        }
      }
      //console.log(this.formData)
      this.initLoad = false
    },

    async countrySeleted(val) {
      let res = await window.axios.get('/api/v1/states/' + val.code)
      if (res) {
        this.states = res.data.states
      }
      this.formData.countries = val
    },

    async stateSeleted(val) {
      this.formData.states = val
    },

    async loadEditData() {
      if (this.isEdit) {
        let response = await this.fetchProvider(this.$route.params.id)
        this.formData = { ...this.formData, ...response.data.providers }

        if (response.data.providers.countries) {
          let res = await window.axios.get(
            '/api/v1/states/' + response.data.providers.countries.code
          )
          if (res) {
            this.states = res.data.states
          }
        }

        this.formData.as_of = moment(response.data.providers.as_of).format(
          'YYYY-MM-DD'
        )
      } else {
        this.formData.as_of = moment().format('YYYY-MM-DD')
      }
    },

    async submitProvider() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.formData.status = this.formData.status.value

      if (this.formData.countries) {
        this.formData.country_id = this.formData.countries.id
      }

      if (this.formData.states) {
        this.formData.state_id = this.formData.states.id
      }

      this.formData.suffix = 'V' + Math.random()
      this.formData.display_name = 'V' + Math.random()
      //  this.formData.title= "V"+Math.random();

      try {
        let response
        this.isLoading = true
        if (this.isEdit) {
          response = await this.updateProvider(this.formData)

          if (response.status === 200) {
            window.toastr['success'](this.$tc('providers.updated_message'))
            this.$router.push(
              `/admin/providers/${response.data.providers.id}/view`
            )
            this.isLoading = false
          }
        } else {
          response = await this.addProvider(this.formData)

          if (response.status === 200) {
            this.isLoading = false
            if (!this.isEdit) {
              window.toastr['success'](this.$tc('providers.created_message'))
              this.$router.push(
                `/admin/providers/${response.data.providers.id}/view`
              )
              return true
            }
          }
        }
      } catch (err) {}
    },
  },
}
</script>