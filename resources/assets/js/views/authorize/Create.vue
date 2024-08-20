<template>
  <sw-card variant="setting-card">
    <!-- Base  -->
    <base-page class="relative">
      <!--------- Form ---------->
      <form action="" @submit.prevent="submitAuthorize">
        <!-- Header  -->
        <sw-page-header :title="pageTitle">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              :title="$tc('settings.payment_gateways.title', 2)"
              to="/admin/settings/payment-gateways"
            />
            <sw-breadcrumb-item
              :title="$tc('authorize.title')"
              to="/admin/settings/authorize"
            />
            <sw-breadcrumb-item
              v-if="$route.name === 'authorize.edit'"
              :title="$t('authorize.edit_authorize')"
              to="#"
              active
            />
            <sw-breadcrumb-item
              v-else
              :title="$t('authorize.new_authorize')"
              to="#"
              active
            />
          </sw-breadcrumb>

          <template slot="actions">
            <sw-button
              variant="primary-outline"
              type="button"
              size="lg"
              class="mr-4 hidden sm:flex"
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
              class="hidden sm:flex"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{
                isEdit
                  ? $t('authorize.update_authorize')
                  : $t('authorize.save_authorize')
              }}
            </sw-button>
          </template>
        </sw-page-header>

        <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
        <!-- Basic  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('authorize.gateway_options') }}
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
              :label="$t('authorize.login_id')"
              :error="loginError"
              class="md:col-span-6"
              required
            >
              <sw-input
                v-model="formData.login_id"
                :invalid="$v.formData.login_id.$error"
                focus
                type="text"
                name="login_id"
                @input="$v.formData.login_id.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('authorize.transaction_key')"
              class="md:col-span-6"
              :error="transactionKeyError"
              required
            >
              <sw-input
                v-model="formData.transaction_key"
                :invalid="$v.formData.transaction_key.$error"
                focus
                :type="isShowTrasactionKey ? 'text' : 'password'"
                name="transaction_key"
                @input="$v.formData.transaction_key.$touch()"
                numeric
              >
                <template v-slot:rightIcon>
                  <eye-off-icon
                    v-if="isShowTrasactionKey"
                    class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowTrasactionKey = !isShowTrasactionKey"
                  />
                  <eye-icon
                    v-else
                    class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowTrasactionKey = !isShowTrasactionKey"
                  />
                </template>
              </sw-input>
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

            <sw-divider class="my-0 col-span-12 opacity-0" />

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
                  {{ $t('authorize.test_mode') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('authorize.test_mode_info') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment  -->
        <!-- <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('authorize.payment') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-divider class="col-span-12" />
            <div class="col-span-12"></div>

            <sw-input-group
              :label="$t('authorize.amount')"
              :error="amountError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-model="formData.amount"
                :invalid="$v.formData.amount.$error"
                focus
                type="text"
                name="amount"
                @input="$v.formData.amount.$touch()"
              />
            </sw-input-group>
        
            <sw-input-group
              :label="$t('authorize.currency')"
              class="md:col-span-3"
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
              :label="$t('authorize.cc_number')"
              :error="ccNumberError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-model="formData.cc_number"
                :invalid="$v.formData.cc_number.$error"
                focus
                type="text"
                name="cc_number"
                @input="$v.formData.cc_number.$touch()"
              />
            </sw-input-group>

            <sw-divider class="my-0 col-span-12 opacity-0" />

            <sw-input-group
              :label="$t('authorize.expiry_month')"
              :error="expiryMonthError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-model="formData.expiry_month"
                :invalid="$v.formData.expiry_month.$error"
                focus
                type="text"
                name="expiry_month"
                @input="$v.formData.expiry_month.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('authorize.expiry_year')"
              :error="expiryYearError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-model="formData.expiry_year"
                :invalid="$v.formData.expiry_year.$error"
                focus
                type="text"
                name="expiry_year"
                @input="$v.formData.expiry_year.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('authorize.cvv')"
              :error="CvVError"
              class="md:col-span-3"
              required
            >
              <sw-input
                v-model="formData.cvv"
                :invalid="$v.formData.cvv.$error"
                focus
                type="text"
                name="cvv"
                @input="$v.formData.cvv.$touch()"
              />
            </sw-input-group>

            <sw-divider class="my-0 col-span-12 opacity-0" />

            <sw-input-group
              :label="$t('authorize.email')"
              class="md:col-span-3"
              :error="emailError"
              required
            >
              <sw-input
                :invalid="$v.formData.email.$error"
                v-model.trim="formData.email"
                class="mt-2"
                type="text"
                name="email"
                tabindex="3"
                @input="$v.formData.email.$touch()"
              />
            </sw-input-group>            
        
            <sw-input-group
              :label="$t('authorize.status')"
              class="md:col-span-3"
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

          </div>
        </div> -->

        <div class="mt-6 mb-4">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary-outline"
            class="mr-3 flex w-full mt-4 sm:hidden md:hidden"
            size="lg"
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
            class="flex w-full mt-4 mb-2 mb-md-0 sm:hidden md:hidden"
          >
            <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
            {{
              isEdit
                ? $t('authorize.update_authorize')
                : $t('authorize.save_authorize')
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
  EyeIcon,
  EyeOffIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const {
  required,
  // email,
  // minLength,
  // maxLength,
  // numeric,
} = require('vuelidate/lib/validators')
export default {
  components: {
    draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    EyeIcon,
    EyeOffIcon,
    XCircleIcon,
  },
  data() {
    return {
      selectedCurrency: '',
      isRequestOnGoing: false,
      isLoading: false,
      payment_API: [
        {
          value: 'CIM',
          text: 'CIM (must be enabled by Authorize.Net)',
        },
        // {
        //   value: 'AIM',
        //   text: 'AIM (default)',
        // },
      ],
      // payment_account_validation_mode: [
      //   {
      //     value: 'none',
      //     text: 'None',
      //   },
      //   {
      //     value: 'test',
      //     text: 'Test',
      //   },
      //   {
      //     value: 'live',
      //     text: 'Live',
      //   },
      // ],
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
      developer_mode: false,
      isShowTrasactionKey: false,
      test_mode: false,
      formData: {
        name: '',
        login_id: '',
        transaction_key: '',
        enable_identification_verification: false,
        enable_fee_charges: false,
        payment_API: {
          value: 'CIM',
          text: 'CIM (must be enabled by Authorize.Net)',
        },
        // payment_account_validation_mode: {
        //   value: 'none',
        //   text: 'None',
        // },
        test_mode: false,
        developer_mode: false,
        // currency: {
        //   value: 'USD',
        //   text: 'USD',
        // },
        status: {
          value: 'I',
          text: 'Inactive',
        },
        is_default:0
        // email: '',
        // amount: '',
        // cc_number: '',
        // expiry_month: '',
        // expiry_year: '',
        // cvv: '',
      },
    }
  },
  computed: {
    pageTitle() {
      if (this.isEdit) {
        return this.$t('authorize.edit_authorize')
      }
      return this.$t('authorize.new_authorize')
    },
    isEdit() {
      return this.$route.name === 'authorize.edit' ? true : false
    },
    loginError() {
      if (!this.$v.formData.login_id.$error) {
        return ''
      }
      if (!this.$v.formData.login_id.required) {
        return this.$t('validation.required')
      }
    },
    transactionKeyError() {
      if (!this.$v.formData.transaction_key.$error) {
        return ''
      }
      if (!this.$v.formData.transaction_key.required) {
        return this.$t('validation.required')
      }
    },
    paymentAPIError() {
      if (!this.$v.formData.payment_API.$error) {
        return ''
      }
      if (!this.$v.formData.payment_API.required) {
        return this.$t('validation.required')
      }
    },
    // emailError() {
    //   if (!this.$v.formData.email.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.email.email) {
    //     return this.$tc('validation.email_incorrect')
    //   }
    // },
    // amountError() {
    //   if (!this.$v.formData.amount.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.amount.required) {
    //     return this.$tc('validation.required')
    //   }
    //   if (!this.$v.formData.amount.numeric) {
    //     return this.$tc('validation.numbers_only')
    //   }
    // },
    // ccNumberError() {
    //   if (!this.$v.formData.cc_number.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.cc_number.required) {
    //     return this.$tc('validation.required')
    //   }
    //   if (!this.$v.formData.cc_number.numeric) {
    //     return this.$tc('validation.numbers_only')
    //   }
    // },
    // expiryMonthError() {
    //   if (!this.$v.formData.expiry_month.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.expiry_month.required) {
    //     return this.$tc('validation.required')
    //   }
    //   if (!this.$v.formData.expiry_month.numeric) {
    //     return this.$tc('validation.numbers_only')
    //   }
    //   if (!this.$v.formData.expiry_month.minLength) {
    //     return this.$tc(
    //       'validation.name_min_length',
    //       this.$v.formData.expiry_month.$params.minLength.min,
    //       { count: this.$v.formData.expiry_month.$params.minLength.min }
    //     )
    //   }
    //   if (!this.$v.formData.expiry_month.maxLength) {
    //     return this.$t('authorize.month_maxLength')
    //   }
    // },
    // expiryYearError() {
    //   if (!this.$v.formData.expiry_year.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.expiry_year.required) {
    //     return this.$tc('validation.required')
    //   }
    //   if (!this.$v.formData.expiry_year.numeric) {
    //     return this.$tc('validation.numbers_only')
    //   }
    //   if (!this.$v.formData.expiry_year.minLength) {
    //     return this.$tc(
    //       'validation.name_min_length',
    //       this.$v.formData.expiry_year.$params.minLength.min,
    //       { count: this.$v.formData.expiry_year.$params.minLength.min }
    //     )
    //   }
    //   if (!this.$v.formData.expiry_year.maxLength) {
    //     return this.$t('authorize.year_maxLength')
    //   }
    // },
    // CvVError() {
    //   if (!this.$v.formData.cvv.$error) {
    //     return ''
    //   }
    //   if (!this.$v.formData.cvv.required) {
    //     return this.$tc('validation.required')
    //   }
    //   if (!this.$v.formData.cvv.numeric) {
    //     return this.$tc('validation.numbers_only')
    //   }
    //   if (!this.$v.formData.cvv.minLength) {
    //     return this.$tc(
    //       'validation.name_min_length',
    //       this.$v.formData.cvv.$params.minLength.min,
    //       { count: this.$v.formData.cvv.$params.minLength.min }
    //     )
    //   }
    //   if (!this.$v.formData.cvv.maxLength) {
    //     return this.$t('authorize.cvv_maxLength')
    //   }
    // },
  },
  methods: {
    ...mapActions('authorizations', [
      'fetchAuthorization',
      'addAuthorization',
      'updateAuthorization',
    ]),

    async loadAuthorization() {
      let res = await this.fetchAuthorization(this.$route.params.id)

      this.formData = res.data.authorize

      this.isRequestOnGoing = false
    },

    async submitAuthorize() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.formData.status = this.formData.status.value
      // this.formData.currency = this.formData.currency.value
      this.formData.payment_API = this.formData.payment_API.value
      // this.formData.payment_account_validation_mode = this.formData.payment_account_validation_mode.value
      this.formData.test_mode = this.formData.test_mode ? 1 : 0
      this.formData.developer_mode = this.formData.developer_mode ? 1 : 0
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
          //console.log(this.formData)
          res = await this.updateAuthorization(this.formData)
          window.toastr['success'](this.$tc('authorize.updated_message'))
          this.$router.push('/admin/settings/authorize')
          return true
        } else {
          res = await this.addAuthorization(this.formData)

          this.isLoading = false

          if (!this.isEdit) {
            window.toastr['success'](this.$tc('authorize.created_message'))
            this.$router.push('/admin/settings/authorize')
            return true
          }
        }
      } catch (error) {
        window.toastr['error'](error.response.data.response)
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
      this.loadAuthorization()
    }
  },

  validations: {
    formData: {
      login_id: {
        required,
      },
      transaction_key: {
        required,
      },
      // currency: {
      //   required,
      // },
      // email: {
      //   required,
      //   email,
      // },
      // amount: {
      //   required,
      // },
      // cc_number: {
      //   required,
      //   numeric,
      // },
      // expiry_month: {
      //   required,
      //   numeric,
      //   minLength: minLength(2),
      //   maxLength: maxLength(2),
      // },
      // expiry_year: {
      //   required,
      //   numeric,
      //   minLength: minLength(2),
      //   maxLength: maxLength(2),
      // },
      // cvv: {
      //   required,
      //   numeric,
      //   minLength: minLength(3),
      //   maxLength: maxLength(3),
      // },
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