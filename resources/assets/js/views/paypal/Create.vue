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
            :title="$tc('paypal.title')"
            to="/admin/settings/paypal"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'paypal.edit'"
            :title="$t('paypal.edit_paypal')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('paypal.new_paypal')"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>
        <!-- Basic  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('paypal.gateway_options') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-divider class="col-span-12" />

            <sw-input-group
              :label="$t('authorize.name')"
             
              class="md:col-span-6"
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
              :label="$t('paypal.merchant_id')"
              :error="merchantIdError"
              class="md:col-span-4"
              required
            >
              <sw-input
                v-model="formData.merchant_id"
                :invalid="$v.formData.merchant_id.$error"
                focus
                type="text"
                name="merchant_id"
                @input="$v.formData.merchant_id.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('paypal.public_key')"
              :error="publicKeyError"
              class="md:col-span-4"
              required
            >
              <sw-input
                v-model="formData.public_key"
                :invalid="$v.formData.public_key.$error"
                focus
                type="text"
                name="public_key"
                @input="$v.formData.public_key.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('paypal.private_key')"
              :error="privateKeyError"
              class="md:col-span-4"
              required
            >
              <sw-input
                v-model="formData.private_key"
                :invalid="$v.formData.private_key.$error"
                focus
                type="password"
                name="private_key"
                @input="$v.formData.private_key.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('paypal.email')"
              class="md:col-span-4"
              :error="emailError"
              required
            >
              <sw-input
                v-model="formData.email"
                :invalid="$v.formData.email.$error"
                focus
                type="email"
                name="email"
                @input="$v.formData.email.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('paypal.enviroment')"
              :error="enviromentError"
              class="md:col-span-4"
              required
            >
              <sw-select
                v-model="formData.enviroment"
                :options="enviromentOptions"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                label="text"
                track-by="value"
                :invalid="$v.formData.enviroment.$error"
                @input="$v.formData.enviroment.$touch()"
            />

            </sw-input-group>

            <!-- <sw-input-group
              :label="$t('paypal.paypal_id')"
              :error="paypalIdError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-model="formData.paypal_id"
                :invalid="$v.formData.paypal_id.$error"
                focus
                type="text"
                name="paypal_id"
                @input="$v.formData.paypal_id.$touch()"
              />
            </sw-input-group> -->

            <!-- <sw-input-group
            :label="$t('paypal.paypal_secret')"
            class="md:col-span-3"
            :error="paypalSecretError"
            required
            >
              <sw-input
                v-model="formData.paypal_secret"
                :invalid="$v.formData.paypal_secret.$error"
                focus
                type="text"
                name="paypal_secret"
                @input="$v.formData.paypal_secret.$touch()"
              />
            </sw-input-group> -->

            <!-- <sw-input-group
            :label="$t('paypal.paypal_signature')"
            class="md:col-span-3"
            :error="paypalSignatureError"
            required
            >
              <sw-input
                v-model="formData.paypal_signature"
                :invalid="$v.formData.paypal_signature.$error"
                focus
                type="text"
                name="paypal_signature"
                @input="$v.formData.paypal_signature.$touch()"
              />
            </sw-input-group> -->

            <sw-divider class="my-0 col-span-12 opacity-0" />

            <!--div class="md:col-span-3">
              <p
                class="p-0 m-0 text-xs leading-4 text-gray-500"
              >
                {{ $t('paypal.paypal_validation_mode_info') }}
              </p>
            </div>
              
            <div class="flex mt-2 col-span-12">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.test_mode"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('paypal.test_mode') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('paypal.test_mode_info') }}
                </p>
              </div>
            </div>
              
            <div class="flex mt-2 col-span-12">
              <div class="relative w-12">
                <sw-switch
                  v-model="formData.developer_mode"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('paypal.developer_mode') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('paypal.developer_mode_info') }}
                </p>
              </div>
            </div>

            <sw-divider class="my-0 col-span-12 opacity-0" />-->

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
              :label="$t('paypal.status')"
              class="md:col-span-4"
            >
            <sw-select
                v-model="formData.status"
                :options="status"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                class="mt-2"
                :placeholder="$t('general.select_status')"
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
            

          </div>
        </div>

        <div class="mt-6 mb-4">
          <sw-button
            :loading="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="flex justify-center w-full md:w-auto"
          >
            <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
            {{
              isEdit
                ? $t('paypal.update_paypal')
                : $t('paypal.save_paypal')
            }}
          </sw-button>
        </div>
    </form>
  </base-page>
  </sw-card>
</template>

<script>
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  email
} = require('vuelidate/lib/validators')
export default {
  components: {
    draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
  },
  data() {
    return {
      selectedCurrency: '',
      isRequestOnGoing: false,
      isLoading: false,
      enviromentOptions: [
        {
          value: 'sandbox',
          text: this.$t('paypal.sandbox'),
        },
        {
          value: 'live',
          text: this.$t('paypal.live'),
        },
      ],
      payment_API: [
        {
          value: 'CIM',
          text: 'CIM (must be enabled by Authorize.Net)',
        }
      ],
      payment_account_validation_mode: [
        {
          value: 'none',
          text: 'None',
        },
        {
          value: 'test',
          text: 'Test',
        },
        {
          value: 'live',
          text: 'Live',
        },
      ],
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
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
      ],
      test_mode: false,
      // developer_mode: false,
      formData: {
        name:'',
        login_id: '',
        transaction_key: '',
        enable_identification_verification: false,
        enable_fee_charges: false,
        payment_API: {
          value: 'CIM',
          text: 'CIM (must be enabled by Authorize.Net)',
        },
        payment_account_validation_mode: {
          value: 'none',
          text: 'None',
        },
        // test_mode: false,
        // developer_mode: false,
        currency: {
          value: 'USD',
          text: 'USD',
        },
        status: {
          value: 'A',
          text: 'Active',
        },
        // paypal_id: '',
        email: '',
        // paypal_secret: '',
        // paypal_signature: '',
        merchant_id: '',
        public_key: '',
        private_key: '',
        enviroment: '',
      },
    }
  },
  computed: {
    pageTitle() {
      if (this.isEdit) {
        return this.$t('paypal.edit_paypal')
      }
      return this.$t('paypal.new_paypal')
    },
    isEdit() {
      if (this.$route.name === 'paypal.edit') {
        return true
      }
      return false
    },
    // paypalIdError() {
    //   if (!this.$v.formData.paypal_id.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.paypal_id.required) {
    //     return this.$t('validation.required')
    //   }
    // },
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.email.email) {
        return this.$t('validation.email_invalid')
      }
    },
    // paypalSecretError() {
    //   if (!this.$v.formData.paypal_secret.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.paypal_secret.required) {
    //     return this.$t('validation.required')
    //   }
    // },
    // paypalSignatureError() {
    //   if (!this.$v.formData.paypal_signature.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.paypal_signature.required) {
    //     return this.$t('validation.required')
    //   }
    // },
    merchantIdError() {
      if (!this.$v.formData.merchant_id.$error) {
        return ''
      }
      if (!this.$v.formData.merchant_id.required) {
        return this.$t('validation.required')
      }
    },
    publicKeyError() {
      if (!this.$v.formData.public_key.$error) {
        return ''
      }
      if (!this.$v.formData.public_key.required) {
        return this.$t('validation.required')
      }
    },
    privateKeyError() {
      if (!this.$v.formData.private_key.$error) {
        return ''
      }
      if (!this.$v.formData.private_key.required) {
        return this.$t('validation.required')
      }
    },
    enviromentError() {
      if (!this.$v.formData.enviroment.$error) {
        return ''
      }
      if (!this.$v.formData.enviroment.required) {
        return this.$t('validation.required')
      }
    }
  },
  methods: {
    ...mapActions('paypal', [
      'fetchPaypal',
      'addPaypal',
      'updatePaypal',
    ]),
    
    async loadPaypal() {
      let res = await this.fetchPaypal(this.$route.params.id)
      
      this.formData = res.data.paypal
      this.formData.enviroment = this.enviromentOptions.find(
        (item) => item.value === this.formData.enviroment
      )
      
      this.isRequestOnGoing = false
      
    },

    async submitPaypal() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      
      this.formData.status = this.formData.status.value
      this.formData.currency = this.formData.currency.value
      // this.formData.paypal_id = this.formData.paypal_id
      this.formData.email = this.formData.email
      // this.formData.paypal_secret = this.formData.paypal_secret
      // this.formData.paypal_signature = this.formData.paypal_signature
      // this.formData.test_mode = this.formData.test_mode ? 1 : 0
      // this.formData.developer_mode = this.formData.developer_mode ? 1 : 0
      this.formData.merchant_id = this.formData.name
      this.formData.merchant_id = this.formData.merchant_id
      this.formData.public_key = this.formData.public_key
      this.formData.private_key = this.formData.private_key
      this.formData.enviroment = this.formData.enviroment.value
      this.formData.enable_identification_verification = this.formData
        .enable_identification_verification
        ? 1
        : 0
      this.formData.enable_fee_charges = this.formData.enable_fee_charges
        ? 1
        : 0
      
      try {
        let res
        this.isLoading = true
        if (this.isEdit) {
          this.formData.id = this.$route.params.id
          res = await this.updatePaypal(this.formData)
          window.toastr['success'](this.$tc('paypal.updated_message'))
          this.$router.push('/admin/settings/paypal')
          return true
        } else {
          res = await this.addPaypal(this.formData)

          this.isLoading = false

          if (!this.isEdit) {
            window.toastr['success'](this.$tc('paypal.created_message'))
            this.$router.push('/admin/settings/paypal')
            return true
          }

        }
      } catch (error) {
       // console.log('Error', error)
        window.toastr['error'](error.response.data.response)
        this.status = [
          {
            value: 'A',
            text: 'Active',
          },
          {
            value: 'I',
            text: 'Inactive',
          },
          {
            value: 'R',
            text: 'Restricted',
          },
        ]
        this.formData.status = {
          value: 'A',
          text: 'Active',
        }
        this.isLoading = false
        return false
      }
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
      // paypal_id: {},
      // paypal_secret: {},
      // paypal_signature: {},
      merchant_id: {
        required,
      },
      email: {
        required,
        email
      },
      public_key: {
        required,
      },
      private_key: {
        required,
      },
      enviroment: {
        required,
      }
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