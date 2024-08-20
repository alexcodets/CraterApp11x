<template>
  <sw-card variant="setting-card">
    <base-page class="item-create">
      <form action="" @submit.prevent="submitPaypal">
        <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
        <sw-page-header :title="pageTitle">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              :title="$tc('settings.payment_gateways.title', 2)"
              to="/admin/settings/payment-gateways"
            />
            <sw-breadcrumb-item
              :title="$tc('auxpay.name')"
              to="/admin/settings/paypal"
            />
            <sw-breadcrumb-item
              v-if="$route.name === 'auxpay.edit'"
              :title="$t('auxpay.edit')"
              to="#"
              active
            />
            <sw-breadcrumb-item
              v-else
              :title="$t('auxpay.create')"
              to="#"
              active
            />
          </sw-breadcrumb>

          <template slot="actions">
            <sw-button
              variant="primary-outline"
              type="button"
              size="lg"
              class="mr-4"
              @click="cancelForm"
            >
              <x-circle-icon class="w-6 h-6 mr-1 -ml-2" v-if="!isLoading" />
              {{ $t('general.cancel') }}
            </sw-button>

            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ isEdit ? $t('auxpay.update') : $t('auxpay.save') }}
            </sw-button>
          </template>
        </sw-page-header>
        <!-- Basic  -->

        <div class="grid grid-cols-12">
          <div class="col-span-12">
            <sw-card class="mb-8">
              <h6 class="col-span-5 sw-section-title lg:col-span-1">
                {{ $t('paypal.gateway_options') }}
              </h6>
              <sw-divider class="col-span-12" />
              <br />


              <sw-input-group
              :label="$t('authorize.name')"
             
            class="mb-4"
              required
            >
              <sw-input
                v-model="formData.name"
                
                focus
                type="text"
                name="name"
                
              />
            </sw-input-group>

              <sw-input-group
                :label="$t('auxpay.endpoint')"
                :error="endpointError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.endpoint"
                  :invalid="$v.formData.endpoint.$error"
                  class="mt-2"
                  focus
                  type="text"
                  name="endpoint"
                  @input="$v.formData.endpoint.$touch()"
                  autocomplete="off"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('settings.customization.modules.api_key')"
                :error="apikeyError"
                class="mb-4"
                required
              >
                <sw-input
                  v-model.trim="formData.api_key"
                  class="mt-2"
                  focus
                  :type="showApiKey ? 'text' : 'password'"
                  name="api_key"
                  :invalid="$v.formData.api_key.$error"
                  @input="$v.formData.api_key.$touch()"
                  autocomplete="off"
                >
                  <template v-slot:rightIcon>
                    <eye-off-icon
                      v-if="showApiKey"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showApiKey = !showApiKey"
                    />
                    <eye-icon
                      v-else
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="showApiKey = !showApiKey"
                    />
                  </template>
                </sw-input>
              </sw-input-group>

              <sw-input-group
                :label="$t('auxpay.merchantid')"
                class="md:col-span-4"
              >
                <sw-input
                  v-model="formData.merchant_id"
                  focus
                  type="text"
                  name="merchant_id"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('paypal.currency')"
                class="md:col-span-4"
              >
                <sw-select
                  v-model="formData.currency"
                  :options="currency"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="16"
                  :allow-empty="true"
                  :placeholder="$t('general.select_currency')"
                  label="text"
                  track-by="value"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('auxpay.default')"
                class="md:col-span-4"
              >
                <sw-select
                  v-model="formData.default"
                  :options="status"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="16"
                  :allow-empty="true"
                  class="mt-2"
                  :placeholder="$t('auxpay.default')"
                  label="text"
                  track-by="value"
                />
              </sw-input-group>

              <sw-input-group
                :label="$t('auxpay.production')"
                class="md:col-span-4"
              >
                <sw-select
                  v-model="formData.production"
                  :options="statusp"
                  :searchable="true"
                  :show-labels="false"
                  :tabindex="16"
                  :allow-empty="true"
                  class="mt-2"
                  :placeholder="$t('auxpay.production')"
                  label="text"
                  track-by="value"
                />
              </sw-input-group>

              <sw-input-group
              :label="$t('authorize.enable_identification_verification')"
              class="col-span-12 md:col-span-12"
              variant="horizontal"
            >
              <sw-switch
                v-model="formData.enable_identification_verification"
                class="-mt-3"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('authorize.enable_fee_charges')"
              class="col-span-12 md:col-span-12"
              variant="horizontal"
            >
              <sw-switch v-model="formData.enable_fee_charges" class="-mt-3" />
            </sw-input-group>
            </sw-card>
          </div>
        </div>
      </form>
    </base-page>
  </sw-card>
</template>

<script>
import RightArrow from '@/components/icon/RightArrow'
import MoreIcon from '@/components/icon/MoreIcon'
import LeftArrow from '@/components/icon/LeftArrow'
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  ChevronDownIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
  EyeIcon,
  EyeOffIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  minLength,
  between,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')
export default {
  components: {
    MoreIcon,
    draggable,
    ChevronDownIcon,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
    XCircleIcon,
    EyeIcon,
    EyeOffIcon,
  },
  data() {
    return {
      isRequestOnGoing: false,
      isLoading: false,
      showApiKey: false,
      // developer_mode: false,
      formData: {
        name: '',
        api_key: '',
        endpoint: '',
        merchant_id: '',
        enable_identification_verification: false,
        enable_fee_charges: false,
        currency: {
          value: 'USD',
          text: 'USD',
        },

        default: {
          value: 0,
          text: this.$t('auxpay.default_not'),
        },
        production: {
          value: 0,
          text: this.$t('auxpay.development'),
        },
      },
      formData2: {
        name: '',
        api_key: '',
        endpoint: '',
        merchant_id: '',
        enable_identification_verification: false,
        enable_fee_charges: false,
        currency: {
          value: 'USD',
          text: 'USD',
        },

        default: {
          value: 0,
          text: this.$t('auxpay.default_not'),
        },
        production: {
          value: 0,
          text: this.$t('auxpay.development'),
        },
      },
      currency: [
        {
          value: 'USD',
          text: 'USD',
        },
        {
          value: 'EUR',
          text: 'EUR',
        },
        {
          value: 'CAD',
          text: 'CAD',
        },
        {
          value: 'GBP',
          text: 'GBP',
        },
      ],
      status: [
        {
          value: 1,
          text: this.$t('auxpay.default'),
        },
        {
          value: 0,
          text: this.$t('auxpay.default_not'),
        },
      ],

      statusp: [
        {
          value: 1,
          text: this.$t('auxpay.production'),
        },
        {
          value: 0,
          text: this.$t('auxpay.development'),
        },
      ],
    }
  },
  computed: {
    pageTitle() {
      if (this.isEdit) {
        return this.$t('auxpax.edit')
      }
      return this.$t('auxpay.create')
    },
    isEdit() {
      if (this.$route.name === 'AuxVault.edit') {
        return true
      }
      return false
    },

    apikeyError() {
      if (!this.$v.formData.api_key.$error) {
        return ''
      }

      if (!this.$v.formData.api_key.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.api_key.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.api_key.$params.minLength.min,
          { count: this.$v.formData.api_key.$params.minLength.min }
        )
      }

      if (!this.$v.formData.api_key.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },

    endpointError() {
      if (!this.$v.formData.endpoint.$error) {
        return ''
      }

      if (!this.$v.formData.endpoint.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.endpoint.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.endpoint.$params.minLength.min,
          { count: this.$v.formData.endpoint.$params.minLength.min }
        )
      }

      if (!this.$v.formData.endpoint.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },
  methods: {
    ...mapActions('auxvault', [
      'addAuxVault',
      'fetchAuxvault',
      'updateAuxvault',
    ]),

    async loadPaypal() {
    //  console.log(this.$route.params.id)

      let res = await this.fetchAuxvault(this.$route.params.id)

      //validar si viene null
     // console.log(res.data.data)

      this.formData.endpoint = res.data.data.endpoint
      this.formData.api_key = res.data.data.ApiKeyDecrypted
      this.formData.merchant_id = res.data.data.MerchantIdDecrypted
      this.formData.currency = res.data.data.currency
      this.formData.production = res.data.data.production
      this.formData.default = res.data.data.default
      this.formData.name = res.data.data.name

      this.formData.currency = this.currency.find(
        (item) => item.value === this.formData.currency
      )

      this.formData.production = this.statusp.find(
        (item) => item.value === this.formData.production
      )

      this.formData.default = this.status.find(
        (item) => item.value === this.formData.default
      )


      this.formData.enable_identification_verification =res.data.data.enable_identification_verification
      this.formData.enable_fee_charges = res.data.data.enable_fee_charges
     // console.log(this.formData)

      this.isRequestOnGoing = false
    },

    async submitPaypal() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.formData2.name = this.formData.name
      this.formData2.api_key = this.formData.api_key
      this.formData2.currency = this.formData.currency
        ? this.formData.currency.value
        : 'USD'
      this.formData2.default = this.formData.default
        ? this.formData.default.value
        : 0
      this.formData2.production = this.formData.production
        ? this.formData.production.value
        : 0
      this.formData2.endpoint = this.formData.endpoint
      this.formData2.merchant_id = this.formData.merchant_id

      this.formData2.enable_identification_verification = this.formData
        .enable_identification_verification
        ? 1
        : 0
      this.formData2.enable_fee_charges = this.formData.enable_fee_charges
        ? 1
        : 0

      try {
        let res
        this.isLoading = true

        if (this.isEdit) {
          this.formData2.id = this.$route.params.id
          res = await this.updateAuxvault(this.formData2)

          window.toastr['success'](this.$tc('authorize.updated_message'))
          this.$router.push('/admin/settings/AuxVault')

          this.isLoading = false
        } else {
          res = await this.addAuxVault(this.formData2)
          this.isLoading = false

          if (!this.isEdit) {
            window.toastr['success'](this.$tc('authorize.created_message'))
            this.$router.push('/admin/settings/AuxVault')
            return true
          }
        }
      } catch (error) {
        console.log(error)
        window.toastr['error'](error.response)
        this.isLoading = false
        return false
      }
    },

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.lose_unsaved_information'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.$router.go(-1)
        }
      })
    },
  },
  mounted() {
    if (this.isEdit) {
      this.isRequestOnGoing = true
      this.loadPaypal()
    }
  },

  validations: {
    formData: {
      api_key: {
        required,
        minLength: minLength(5),
        maxLength: maxLength(255),
      },
      endpoint: {
        required,
        minLength: minLength(5),
        maxLength: maxLength(255),
      },
    },
  },
}
</script>

<style lang="scss">
.package-create-page {
  .package-foot {
    .package-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .package-foot {
      .package-total {
        min-width: 384px;
      }
    }
  }
}
// Dropdown
.tab {
  overflow: hidden;
}
.tab-content {
  max-height: 0;
  transition: all 0.5s;
}
input:checked + .tab-label .test {
  background-color: #000;
}
input:checked + .tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}
input:checked + .tab-label::after {
  transform: rotate(90deg);
}
input:checked ~ .tab-content {
  max-height: 100vh;
}
</style>