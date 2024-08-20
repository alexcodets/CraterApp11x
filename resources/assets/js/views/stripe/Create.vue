<template>
  <div>
    <sw-card variant="setting-card" class="mt-3">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <div>
        <div class="flex justify-between">
          <sw-page-header :title="pageTitle">
            <sw-breadcrumb slot="breadcrumbs">
              <sw-breadcrumb-item
                :title="$tc('settings.payment_gateways.title', 2)"
                to="/admin/settings/payment-gateways"
              />
              <sw-breadcrumb-item
                :title="$tc('stripe.title')"
                to="/admin/settings/stripe"
              />
              <sw-breadcrumb-item
                v-if="$route.name === 'stripe.edit'"
                :title="$t('stripe.edit_stripe')"
                to="#"
                active
              />
              <sw-breadcrumb-item
                v-else
                :title="$t('stripe.new_stripe')"
                to="#"
                active
              />
            </sw-breadcrumb>
          </sw-page-header>

          <div class="mt-3">
            <sw-button
              tag-name="router-link"
              :to="`/admin/settings/stripe`"
              variant="primary-outline"
            >
              {{ $t('general.go_back') }}
            </sw-button>
          </div>
        </div>
      </div>

      <div class="mt-5">
        <form action="" @submit.prevent="submitStripe">
          <!-- Basic  -->

          <h6 class="sw-section-title mb-5">
            {{ $t('stripe.gateway_options') }}
          </h6>

          <div class="grid grid-cols-12 gap-4 gap-y-6">
            <sw-input-group
              :label="$t('stripe.public_key')"
              :error="publicKeyError"
              class="col-span-12 md:col-span-6"
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
              :label="$t('stripe.secret_key')"
              :error="secretKeyError"
              class="col-span-12 md:col-span-6"
              required
            >
              <sw-input
                v-model="formData.secret_key"
                :invalid="$v.formData.secret_key.$error"
                focus
                type="password"
                name="secret_key"
                @input="$v.formData.secret_key.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('stripe.enviroment')"
              :error="enviromentError"
              class="col-span-12 md:col-span-4"
              required
            >
              <sw-select
                v-model="formData.environment"
                :options="enviromentOptions"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                label="text"
                track-by="value"
                :invalid="$v.formData.environment.$error"
                @input="$v.formData.environment.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('stripe.currency')"
              class="col-span-12 md:col-span-4"
              :error="currencyError"
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
                :invalid="$v.formData.currency.$error"
                @input="$v.formData.currency.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('stripe.status')"
              class="col-span-12 md:col-span-4"
              :error="statusError"
            >
              <sw-select
                v-model="formData.status"
                :options="status"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                :placeholder="$t('general.select_status')"
                label="text"
                track-by="value"
                :invalid="$v.formData.status.$error"
                @input="$v.formData.status.$touch()"
              />
            </sw-input-group>
          </div>

          <div class="flex flex-wrap items-center mt-8 gap-2">
            <sw-button
              variant="primary-outline"
              type="button"
              size="lg"
              class="w-full md:w-auto mb-2 md:mb-0"
              @click="cancelForm()"
            >
              <x-circle-icon class="w-6 h-6 mr-1 -ml-2" />
              {{ $t('general.cancel') }}
            </sw-button>

            <sw-button
              :loading="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="w-full md:w-auto mb-2 md:mb-0"
            >
              <save-icon class="mr-2 -ml-1" v-if="!isLoading" />
              {{
                isEdit ? $t('stripe.update_stripe') : $t('stripe.save_stripe')
              }}
            </sw-button>
          </div>
        </form>
      </div>
    </sw-card>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import { mapActions, mapGetters } from 'vuex'
import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'
const { required, email } = require('vuelidate/lib/validators')
export default {
  components: {
    draggable,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    XCircleIcon,
  },
  data() {
    return {
      isRequestOnGoing: false,
      isLoading: false,
      enviromentOptions: [
        { text: this.$t('stripe.sandbox'), value: 'sandbox' },
        { text: this.$t('stripe.live'), value: 'live' },
      ],
      currency: [
        { value: 'USD', text: 'USD' },
        { value: 'EUR', text: 'EUR' },
        { value: 'CAD', text: 'CAD' },
        { value: 'GBP', text: 'GBP' },
      ],
      status: [
        { text: 'Active', value: 'A' },
        { text: 'Inactive', value: 'I' },
      ],

      formData: {
        public_key: '',
        secret_key: '',
        environment: '',
        currency: '',
        status: '',
      },
    }
  },
  computed: {
    pageTitle() {
      if (this.isEdit) {
        return this.$t('stripe.edit_stripe')
      }
      return this.$t('stripe.new_stripe')
    },

    isEdit() {
      if (this.$route.name === 'stripe.edit') {
        return true
      }
      return false
    },

    publicKeyError() {
      if (!this.$v.formData.public_key.$error) {
        return ''
      }
      if (!this.$v.formData.public_key.required) {
        return this.$t('validation.required')
      }
    },

    secretKeyError() {
      if (!this.$v.formData.secret_key.$error) {
        return ''
      }
      if (!this.$v.formData.secret_key.required) {
        return this.$t('validation.required')
      }
    },

    currencyError() {
      if (!this.$v.formData.currency.$error) {
        return ''
      }
      if (!this.$v.formData.currency.required) {
        return this.$t('validation.required')
      }
    },

    enviromentError() {
      if (!this.$v.formData.environment.$error) {
        return ''
      }
      if (!this.$v.formData.environment.required) {
        return this.$t('validation.required')
      }
    },

    statusError() {
      if (!this.$v.formData.status.$error) {
        return ''
      }
      if (!this.$v.formData.status.required) {
        return this.$t('validation.required')
      }
    },
  },

  created() {
    if (this.isEdit) {
      this.loadStripe()
    }
  },

  methods: {
    ...mapActions('stripes', ['fetchStripe', 'addStripe', 'updateStripe']),

    async loadStripe() {
      try {
        this.isRequestOnGoing = true
        let res = await this.fetchStripe(this.$route.params.id)
          this.formData = res.data,
          this.formData.environment = this.enviromentOptions.find(
            (item) => item.value === this.formData.environment
          ),
          this.formData.currency = this.currency.find(
            (item) => item.value === this.formData.currency
          ),
          this.formData.status = this.status.find(
            (item) => item.value === this.formData.status
          )

        this.isRequestOnGoing = false
      } catch (error) {
       // console.log(error)
      } finally {
        this.isRequestOnGoing = false
      }
    },

    async submitStripe() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      try {
        let res
        this.isLoading = true

        const payload = {
          ...this.formData,
          status: this.formData.status.value,
          currency: this.formData.currency.value,
          environment: this.formData.environment.value,
          id: this.$route.params.id,
        }
        if (this.isEdit) {
          res = await this.updateStripe(payload)
          window.toastr['success'](this.$tc('stripe.updated_message'))
        } else {
          res = await this.addStripe(payload)
          window.toastr['success'](this.$tc('stripe.created_message'))
        }
        this.$router.push('/admin/settings/stripe')
        this.isLoading = false
        return true
      } catch (error) {
        window.toastr['error'](error.response.data.response)
        this.isLoading = false
        return false
      }
    },

    cancelForm() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
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

  validations: {
    formData: {
      public_key: {
        required,
      },
      secret_key: {
        required,
      },
      environment: {
        required,
      },
      currency: {
        required,
      },
      status: {
        required,
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
