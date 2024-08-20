<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="this.$t('avalara.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard" />
        <sw-breadcrumb-item
          :title="$t('avalara.title')"
          to="/admin/avalara/configs"
        />
        <sw-breadcrumb-item
          v-if="$route.name === 'avalara.edit'"
          :title="$t('general.edit')"
          to="#"
          active
        />
        <sw-breadcrumb-item
          v-else
          :title="$t('avalara.add_new_config')"
          to="#"
          active
        />
      </sw-breadcrumb>
    </sw-page-header>
    <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-8">
        <form action="" @submit.prevent="submitAvalara">
          <sw-card>
            <!-- Status -->
            <div class="flex mb-4 md:grid-cols-2">
              <div class="relative w-full">
                <sw-switch
                  v-model="status"
                  class="absolute"
                  style="top: -18px"
                  @change="setStatus"
                />

                <div class="ml-12">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black"
                    v-text="$t('avalara.item.status')"
                  />
                  <p
                    class="p-0 m-0 text-xs leading-tight text-gray-500"
                    style="max-width: 480px"
                  >
                    <sw-badge
                      :bg-color="
                        $utils.getBadgeStatusColor(
                          formData.status == 'A' ? 'A' : 'I'
                        ).bgColor
                      "
                      :color="
                        $utils.getBadgeStatusColor(
                          formData.status == 'A' ? 'A' : 'I'
                        ).color
                      "
                    >
                      {{ formData.status == 'A' ? 'Active' : 'Inactive' }}
                    </sw-badge>
                  </p>
                </div>
              </div>

              <!-- test connection -->
              <div class="relative w-full" v-if="isEdit">
                <div class="text-right">
                  <sw-button
                    :loading="isLoading"
                    variant="primary"
                    type="button"
                    @click="testConection()"
                    size="lg"
                    class="flex justify-center w-full md:w-auto"
                  >
                    <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                    {{ $t('avalara.test_conection') }}
                  </sw-button>
                </div>
              </div>
            </div>

            <!-- Conexion Type -->
            <div class="flex mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="production"
                  class="absolute"
                  style="top: -18px"
                  @change="setConexion"
                />
              </div>

              <div class="ml-15">
                <p
                  class="p-0 mb-1 text-base leading-snug text-black"
                  v-text="conexion"
                />
                <p
                  class="p-0 m-0 text-xs leading-tight text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('avalara.conexion_description') }}
                </p>
              </div>
            </div>
            <!-- User Name -->
            <sw-input-group
              :label="$t('avalara.user_name')"
              :error="userNameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.user_name"
                :invalid="$v.formData.user_name.$error"
                class="mt-2"
                focus
                type="text"
                @input="$v.formData.user_name.$touch()"
              />
            </sw-input-group>

            <!-- Password -->
            <sw-input-group
              :label="$tc('general.password')"
              :error="passwordError"
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.password"
                :invalid="$v.formData.password.$error"
                type="password"
                class="mt-2"
                @input="$v.formData.password.$touch()"
              />
            </sw-input-group>

            <!-- Client -->
            <sw-input-group
              :label="$t('avalara.client_id')"
              :error="clientError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.client_id"
                :invalid="$v.formData.client_id.$error"
                type="text"
                class="mt-2"
                @input="$v.formData.client_id.$touch()"
              />
            </sw-input-group>

            <!-- Account reference -->
            <sw-input-group
              :label="$t('avalara.account_reference')"
              :error="account_referenceError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.account_reference"
                :invalid="$v.formData.account_reference.$error"
                type="text"
                class="mt-2"
                @input="$v.formData.account_reference.$touch()"
              />
            </sw-input-group>

            <!-- Profile id -->
            <sw-input-group
              :label="$t('avalara.profile_id')"
              :error="profileError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.profile_id"
                :invalid="$v.formData.profile_id.$error"
                type="text"
                class="mt-2"
                @input="$v.formData.profile_id.$touch()"
              />
            </sw-input-group>

            <!-- Company identifier id -->
            <sw-input-group
              :label="$t('settings.company_info.company_identifier')"
              :error="company_identifierError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.company_identifier"
                :invalid="$v.formData.company_identifier.$error"
                type="text"
                class="mt-2"
                @input="$v.formData.company_identifier.$touch()"
              />
            </sw-input-group>

            <!-- Url -->
            <sw-input-group
              label="Url"
              :error="urlError"
              horizontal
              required
              class="mb-4"
            >
              <sw-input
                v-model="formData.url"
                :invalid="$v.formData.url.$error"
                type="url"
                class="mt-2"
                @input="$v.formData.url.$touch()"
              />
            </sw-input-group>

            <!-- Host -->
            <sw-input-group label="Host" horizontal class="mb-4">
              <sw-input
                v-model="formData.host"
                type="text"
                class="mt-2"
                @input="$v.formData.host.$touch()"
              />
            </sw-input-group>

            <!-- nuevo -->
            <div class="flex md:col-span-3 my-8 mb-4">
              <div class="relative w-12">
                <sw-switch
                  v-model="avalara_bool"
                  class="absolute"
                  style="top: -20px"
                  @change="slideChange"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.avalara_field') }}
                </p>
              </div>
            </div>

            <div
              v-if="avalara_bool"
              class="flex md:col-start-1 md:col-span-3 my-8 mb-4"
            >
              <div class="">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('customers.avalara_company') }}
                </p>
              </div>
            </div>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.business_class')"
              class="md:col-start-1 md:col-span-3"
            >
              <sw-select
                v-model="avalara_company_bscl_selected"
                :options="avalara_company_bscl_options"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'bscl')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.service_class')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="avalara_company_svcl_selected"
                :options="avalara_company_svcl_options"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'svcl')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.facilities')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="avalara_company_fclt_selected"
                :options="avalara_company_fclt_options"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'fclt')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.regulated')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="avalara_company_reg_selected"
                :options="avalara_company_reg_options"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'reg')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.franchise')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="avalara_company_frch_selected"
                :options="avalara_company_frch_options"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'frch')"
              >
              </sw-select>
            </sw-input-group>

            <!-- ITEMS -->

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.did_items')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="did_item"
                :options="getAvalaraItems"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'did')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.extension_items')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="extension_item"
                :options="getAvalaraItems"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'ext')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.cdr_items')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="cdr_item"
                :options="getAvalaraItems"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'cdr')"
              >
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="avalara_bool"
              :label="$t('customers.international_items')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="international_item"
                :options="getAvalaraItems"
                :searchable="true"
                :show-labels="false"
                class="mt-2"
                label="name"
                @select="companySelected($event, 'inter')"
              >
              </sw-select>
            </sw-input-group>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="flex justify-center w-full md:w-auto"
              >
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ isEdit ? $t('general.edit') : $t('general.save') }}
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
const {
  required,
  minLength,
  url,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      avalara_type_selected: {},
      avalara_company_bscl_selected: null,
      avalara_company_svcl_selected: null,
      avalara_company_fclt_selected: null,
      avalara_company_reg_selected: null,
      avalara_company_frch_selected: null,
      did_item: null,
      extension_item: null,
      cdr_item: null,
      international_item: null,
      avalara_company_bscl_options: [
        { value: '0', name: 'Incumbent Local Exchange Carrier (ILEC)' },
        { value: '1', name: 'Not an ILEC' },
      ],
      avalara_company_svcl_options: [
        { value: '0', name: 'Primary Local' },
        { value: '1', name: 'Primary Long Distance' },
      ],
      avalara_company_fclt_options: [
        {
          value: 1,
          name: 'Seller is facilities-based (cable operators and telephone companies)',
        },
        {
          value: 0,
          name: 'Seller is not facilities-based (Internet based providers)',
        },
      ],
      avalara_company_reg_options: [
        { value: 1, name: 'Seller is regulated' },
        { value: 0, name: 'Seller is not regulated' },
      ],
      avalara_company_frch_options: [
        { value: 1, name: 'It is franchise' },
        { value: 0, name: 'It is not franchise' },
      ],
      isRequestOnGoing: false,
      production: false, // false is Sandbox
      status: true, // false is Sandbox
      isLoading: false,
      conexion: this.$t('avalara.conexion_sandbox'),
      avalara_bool: false,
      formData: {
        status: 'A',
        conexion: 'sandbox',
        user_name: '',
        password: null,
        client_id: null,
        profile_id: null,
        url: null,
        host: null,
        bscl: null,
        svcl: null,
        fclt: null,
        reg: null,
        frch: null,
        item_did_id: null,
        item_cdr_id: null,
        item_extension_id: null,
        item_international_id: null,
        company_identifier: '',
        account_reference: '',
      },
    }
  },
  watch: {},
  computed: {
    ...mapGetters('avalara', ['avalaraItems']),

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.first_name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    companyBsclError() {
      if (!this.$v.formData.bscl.$error) {
        return ''
      }
      if (!this.$v.formData.bscl.required) {
        return this.$tc('validation.required')
      }
    },
    companySvclError() {
      if (!this.$v.formData.svcl.$error) {
        return ''
      }
      if (!this.$v.formData.svcl.required) {
        return this.$tc('validation.required')
      }
    },
    companyFcltError() {
      if (!this.$v.formData.fclt.$error) {
        return ''
      }
      if (!this.$v.formData.fclt.required) {
        return this.$tc('validation.required')
      }
    },
    companyRegError() {
      if (!this.$v.formData.reg.$error) {
        return ''
      }
      if (!this.$v.formData.reg.required) {
        return this.$tc('validation.required')
      }
    },
    emailLowBalanceNotificationError() {
      if (!this.$v.formData.email_low_balance_notification.$error) {
        return ''
      }
      if (!this.$v.formData.email_low_balance_notification.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.email_low_balance_notification.minValue) {
        return this.$t('validation.min_number')
      }
      if (!this.$v.formData.email_low_balance_notification.required) {
        return this.$t('validation.required')
      }
    },
    autoReplenishAmountError() {
      if (!this.$v.formData.auto_replenish_amount.$error) {
        return ''
      }
      if (!this.$v.formData.auto_replenish_amount.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.auto_replenish_amount.minValue) {
        return this.$t('validation.min_number')
      }
      if (!this.$v.formData.auto_replenish_amount.required) {
        return this.$t('validation.required')
      }
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
    displayLastNameError() {
      if (!this.$v.formData.last_name.$error) {
        return ''
      }
      if (!this.$v.formData.last_name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.last_name_min_length',
          this.$v.formData.last_name.$params.minLength.min,
          { count: this.$v.formData.last_name.$params.minLength.min }
        )
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
    phoneError() {
      if (!this.$v.formData.billing.phone.$error) {
        return ''
      }
      if (!this.$v.formData.billing.phone.required) {
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
    shipAddress1Error() {
      if (!this.$v.formData.shipping.address_street_1.$error) {
        return ''
      }

      if (!this.$v.formData.shipping.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress2Error() {
      if (!this.$v.formData.shipping.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.shipping.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    isAddAvalara() {
      this.add_avalara = this.formData.avalara_bool
      return this.add_avalara
    },
    usernameError() {
      if (!this.$v.formData.customer_username.$error) {
        return ''
      }
      if (!this.$v.formData.customer_username.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.customer_username.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.customer_username.$params.minLength.min,
          { count: this.$v.formData.customer_username.$params.minLength.min }
        )
      }
    },
    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },
    confirmPasswordError() {
      if (!this.$v.formData.confirm_password.$error) {
        return ''
      }
      if (!this.$v.formData.confirm_password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },
    languageError() {
      if (!this.$v.formData.language.$error) {
        return ''
      }
      if (!this.$v.language.required) {
        return this.$tc('validation.required')
      }
    },
    isEdit() {
      if (this.$route.name === 'avalara.edit') {
        return true
      }
      return false
    },
    userNameError() {
      if (!this.$v.formData.user_name.$error) {
        return ''
      }
      if (!this.$v.formData.user_name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.user_name.minLength) {
        return this.$tc(
          'validation.min_length',
          this.$v.formData.user_name.$params.minLength.min,
          { count: this.$v.formData.user_name.$params.minLength.min }
        )
      }
    },
    clientError() {
      if (!this.$v.formData.client_id.$error) {
        return ''
      }
      if (!this.$v.formData.client_id.required) {
        return this.$t('validation.required')
      }
    },
    profileError() {
      if (!this.$v.formData.profile_id.$error) {
        return ''
      }
      if (!this.$v.formData.profile_id.required) {
        return this.$t('validation.required')
      }
    },

    account_referenceError() {
      if (!this.$v.formData.account_reference.$error) {
        return ''
      }
      if (!this.$v.formData.account_reference.required) {
        return this.$t('validation.required')
      }
    },
    company_identifierError() {
      if (!this.$v.formData.company_identifier.$error) {
        return ''
      }
      if (!this.$v.formData.company_identifier.required) {
        return this.$t('validation.required')
      }
    },
    urlError() {
      if (!this.$v.formData.url.$error) {
        return ''
      }
      if (!this.$v.formData.url.url) {
        return this.$t('validation.invalid_url')
      }
      if (!this.$v.formData.url.required) {
        return this.$t('validation.required')
      }
    },
    hostError() {
      if (!this.$v.formData.url.$error) {
        return ''
      }
      if (!this.$v.formData.url.required) {
        return this.$t('validation.required')
      }
    },
    getAvalaraItems() {
      return this.avalaraItems.map((item) => {
        return {
          ...item,
        }
      })
    },
  },
  validations: {
    formData: {
      user_name: {
        required,
        minLength: minLength(3),
      },
      password: {
        required: requiredIf(function () {
          return !this.isEdit
        }),
        minLength: minLength(8),
      },
      client_id: {
        required,
      },
      profile_id: {
        required,
      },
      account_reference: {
        required,
      },

      company_identifier: {
        required,
      },
      url: {
        url,
        required,
      },
    },
  },
  methods: {
    ...mapActions('avalara', [
      'addAvalaraConfig',
      'fetchAvalaraConfig',
      'updateAvalaraConfig',
      'fetchAvalaraItems',
      'checkAvalaraCredentials',
    ]),

    ...mapActions('user', ['fetchCurrentUser']),

    slideChange() {
      this.avalara_type_selected = this.add_avalara
        ? { name: 'Residential', value: 0 }
        : {}
    },
    companySelected(val, type) {
      const vm = this
      switch (type) {
        case 'bscl':
          vm.formData.bscl = val.value
          break
        case 'svcl':
          vm.formData.svcl = val.value
          break
        case 'fclt':
          vm.formData.fclt = val.value
          break
        case 'reg':
          vm.formData.reg = val.value
          break
        case 'frch':
          vm.formData.frch = val.value
          break
        case 'did':
          vm.formData.item_did_id = val.id
          break
        case 'ext':
          vm.formData.item_extension_id = val.id
          break
        case 'cdr':
          vm.formData.item_cdr_id = val.id
          break
        case 'inter':
          vm.formData.item_international_id = val.id
          break
      }
    },

    async testConection() {
      const res = await this.checkAvalaraCredentials(this.$route.params.id)
      if (res.data.success) {
        window.toastr['success'](res.data.message)
      } else {
        console.log(res)
        window.toastr['error'](res.data.message)
      }
    },
    async loadAvalaraConfigs() {
      const vm = this
      const res = await this.fetchAvalaraConfig(this.$route.params.id)

      console.log(res)
      if (res.data.response.company_identifier) {
        console.log('entro')
      }
      if (res.data.response.bscl == '1') {
        this.avalara_company_bscl_selected = {
          value: '1',
          name: 'Not an ILEC',
        }
      } else {
        this.avalara_company_bscl_selected = {
          value: '0',
          name: 'Incumbent Local Exchange Carrier (ILEC)',
        }
      }

      if (res.data.response.svcl == '1') {
        this.avalara_company_svcl_selected = {
          value: '1',
          name: 'Primary Long Distance',
        }
      } else {
        this.avalara_company_svcl_selected = {
          value: '0',
          name: 'Primary Local',
        }
      }

      if (res.data.response.fclt == 1) {
        this.avalara_company_fclt_selected = {
          value: 1,
          name: 'Seller is facilities-based (cable operators and telephone companies)',
        }
      } else {
        this.avalara_company_fclt_selected = {
          value: 0,
          name: 'Seller is not facilities-based (Internet based providers)',
        }
      }

      if (res.data.response.reg == 1) {
        this.avalara_company_reg_selected = {
          value: 1,
          name: 'Seller is regulated',
        }
      } else {
        this.avalara_company_reg_selected = {
          value: 0,
          name: 'Seller is not regulated',
        }
      }

      if (res.data.response.frch == 1) {
        this.avalara_company_frch_selected = {
          value: 1,
          name: 'It is franchise',
        }
      } else if (res.data.response.frch == 0) {
        this.avalara_company_frch_selected = {
          value: 0,
          name: 'It is not franchise',
        }
      }

      this.did_item = this.getAvalaraItems.find(
        (item) => item.id === res.data.response.item_did_id
      )

      this.extension_item = this.getAvalaraItems.find(
        (item) => item.id === res.data.response.item_extension_id
      )

      this.cdr_item = this.getAvalaraItems.find(
        (item) => item.id === res.data.response.item_cdr_id
      )

      this.international_item = this.getAvalaraItems.find(
        (item) => item.id === res.data.response.item_international_id
      )

      vm.formData = res.data.response
      vm.status = vm.formData.status == 'A' ? true : false

      vm.production = vm.formData.conexion == 'production' ? true : false
      vm.conexion =
        vm.formData.conexion == 'production'
          ? this.$t('avalara.conexion_production')
          : this.$t('avalara.conexion_sandbox')

      this.isRequestOnGoing = false
    },
    async loadAvalaraConfigsInitial() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()

      if (response.data.user.company.company_identifier) {
        this.formData.company_identifier =
          response.data.user.company.company_identifier
      }

      this.isRequestOnGoing = false
    },
    async submitAvalara() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      try {
        let res
        this.isLoading = true
        if (!this.isEdit) {
          res = await this.addAvalaraConfig(this.formData)
        } else {
          res = await this.updateAvalaraConfig(this.formData)
        }
        this.isLoading = false
        window.toastr['success'](res.data.response)
        this.$router.push('/admin/avalara/configs')
        return true
      } catch (error) {
        console.log('Error', error)
        window.toastr['error'](error.response.data.response)
        this.isLoading = false
        return false
      }
    },
    setConexion(val) {
      this.production = val
      this.conexion = val
        ? this.$t('avalara.conexion_production')
        : this.$t('avalara.conexion_sandbox')
      this.formData.conexion = val ? 'production' : 'sandbox'
    },
    setStatus(val) {
      this.status = val
      this.formData.status = val ? 'A' : 'I'
    },
  },
  mounted() {
    this.fetchAvalaraItems().then(() => {
      if (this.isEdit || this.isCopy) {
        this.isRequestOnGoing = true
        this.loadAvalaraConfigs()
        this.avalara_bool = 1
      } else {
        console.log('creacion')
        this.loadAvalaraConfigsInitial()
      }
    })
  },
}
</script>

<style lang="scss" scoped>
</style>
