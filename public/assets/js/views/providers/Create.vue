<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <sw-page-header class="mb-3" :title="pageTitle">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
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
      <template slot="actions"></template>
    </sw-page-header>

    <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-12">
        <form action="" @submit.prevent="submitProvider">
          <sw-card>
            <div class="grid-cols-12 gap-8 mt-6 mb-8 lg:grid">
              <div
                class="
                  grid grid-cols-1
                  col-span-12
                  gap-4
                  mt-8
                  lg:gap-4 lg:mt-0 lg:grid-cols-3
                "
              >
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
                    :tabindex="1"
                    @input="$v.formData.title.$touch()"
                  />
                </sw-input-group>

                <sw-input-group
                  :label="$t('providers.first_name')"
                  :error="firstNameError"
                  class="mb-4"
                  required
                >
                  <sw-input
                    v-model.trim="formData.first_name"
                    :invalid="$v.formData.first_name.$error"
                    class="mt-2"
                    focus
                    type="text"
                    name="first_name"
                    :tabindex="2"
                    @input="$v.formData.first_name.$touch()"
                  />
                </sw-input-group>

                <sw-input-group
                  :label="$t('providers.last_name')"
                  :error="lastNameError"
                  class="mb-4"
                  required
                >
                  <sw-input
                    v-model.trim="formData.last_name"
                    :invalid="$v.formData.last_name.$error"
                    class="mt-2"
                    focus
                    type="text"
                    name="last_name"
                    :tabindex="4"
                    @input="$v.formData.last_name.$touch()"
                  />
                </sw-input-group>

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
                    tabindex="6"
                    @input="$v.formData.email.$touch()"
                  />
                </sw-input-group>

               
              </div>

              <div
                class="
                  grid grid-cols-1
                  col-span-12
                  gap-4
                  mt-8
                  lg:gap-4 lg:mt-0 lg:grid-cols-3
                "
              >
                <sw-input-group :label="$t('providers.country')" class="mb-4">
                  <sw-select
                    v-model="formData.countries"
                    :options="countries"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :tabindex="10"
                    class="mt-2"
                    :placeholder="$t('general.select_country')"
                    label="name"
                    track-by="id"
                    @select="countrySeleted"
                  />
                </sw-input-group>

                <sw-input-group :label="$t('providers.state')" class="mb-4">
                  <sw-select
                    v-model="formData.states"
                    :options="states"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="true"
                    :tabindex="11"
                    class="mt-2"
                    :placeholder="$t('general.select_state')"
                    label="name"
                    track-by="id"
                    select="stateSeleted"
                  />
                </sw-input-group>

                <sw-input-group :label="$t('providers.city')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.city"
                    class="mt-2"
                    focus
                    type="text"
                    name="city"
                    :tabindex="12"
                  />
                </sw-input-group>

                <sw-input-group :label="$t('providers.street')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.street"
                    class="mt-2"
                    focus
                    type="text"
                    name="street"
                    :tabindex="13"
                  />
                </sw-input-group>

                <sw-input-group :label="$t('providers.zip_code')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.zip_code"
                    class="mt-2"
                    focus
                    type="text"
                    name="zip_code"
                    :tabindex="14"
                  />
                </sw-input-group>

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
                    :tabindex="14"
                    @input="$v.formData.phone.$touch()"
                  />
                </sw-input-group>

                <sw-input-group :label="$t('providers.mobile')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.mobile"
                    class="mt-2"
                    focus
                    type="text"
                    name="mobile"
                    tabindex="15"
                  />
                </sw-input-group>


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
                    tabindex="21"
                  />
                </sw-input-group>

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
                    tabindex="22"
                  />
                </sw-input-group>

                <sw-input-group
                  :label="$t('providers.track_payments')"
                  class="mb-4"
                >
                  <div class="flex my-8 mb-4">
                    <div class="relative w-12">
                      <sw-switch
                        v-model="formData.track_payments"
                        class="absolute mt-2"
                        style="top: -30px"
                        tabindex="23"
                      />
                    </div>

                    <div class="ml-4">
                      <p
                        class="
                          p-0
                          mb-1
                          text-base
                          leading-snug
                          text-black
                          box-title
                        "
                      >
                        {{ $t('providers.track_payments') }}
                      </p>
                    </div>
                  </div>
                </sw-input-group>


                <sw-input-group :label="$t('providers.terms')" class="mb-4">
                  <sw-input
                    v-model.trim="formData.terms"
                    class="mt-2"
                    focus
                    type="text"
                    name="terms"
                    tabindex="25"
                  />
                </sw-input-group>

                <sw-input-group :label="$t('providers.status')" class="mb-4">
                  <sw-select
                    v-model="formData.status"
                    :options="status"
                    :searchable="true"
                    :show-labels="false"
                    :tabindex="26"
                    :allow-empty="true"
                    class="mt-2"
                    :placeholder="$t('providers.status')"
                    label="text"
                    track-by="value"
                  />
                </sw-input-group>
              </div>

              <div
                class="
                  grid grid-cols-1
                  col-span-12
                  gap-4
                  mt-8
                  lg:gap-4 lg:mt-0 lg:grid-cols-1
                "
              >
                <sw-input-group :label="$t('groups.description')" class="mb-4">
                  <base-custom-input
                    v-model.trim="formData.description"
                    class="mb-4"
                    tabindex="27"
                  />
                </sw-input-group>
              </div>
            </div>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                tabindex="28"
                class="flex justify-center w-full md:w-auto"
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
        </form>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import CustomFieldsMixin from '../../mixins/customFields'
const {
  required,
  minLength,
  email,
  url,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
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
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
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
          text: 'Active',
        },
        status: '',
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
      first_name: {
        required,
      },
      last_name: {
        required,
      },

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

    async fetchInitDataCountry() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
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
        console.log('carga FETCH PROVIDERS: ', response)
        this.formData = { ...this.formData, ...response.data.providers }

        if (response.data.providers.countries) {
          let res = await window.axios.get(
            '/api/v1/states/' + response.data.providers.countries.code
          )
          if (res) {
            this.states = res.data.states
          }
        }

        this.formData.as_of = moment(
          console.log(response.data.providers.as_of),
          response.data.providers.as_of,
          'YYYY-MM-DD'
        ).toString()
      } else {
        this.formData.as_of = moment().toString()
      }
    },

    async submitProvider() {
      console.log("Submit");
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

      this.formData.suffix= "V"+Math.random();
      this.formData.display_name= "V"+Math.random();
    //  this.formData.title= "V"+Math.random();

      try {
        let response
        this.isLoading = true
        if (this.isEdit) {
          response = await this.updateProvider(this.formData)
          let data
          if (response.status === 200) {
            window.toastr['success'](this.$tc('providers.updated_message'))
            this.$router.push('/admin/providers')
            this.isLoading = false
          }
        } else {
          response = await this.addProvider(this.formData)
          console.log(response);
          let data
          if (response.status === 200) {
            this.isLoading = false
            if (!this.isEdit) {
              window.toastr['success'](this.$tc('providers.created_message'))
              this.$router.push('/admin/providers')
              return true
            }
          }
        }
      } catch (err) {}
    },
  },
}
</script>