<template>
  <base-page class="customer-create">
    <sw-page-header class="mb-5" :title="pageTitle">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="/customer/dashboard"
          :title="$t('general.home')"
        />
        <sw-breadcrumb-item
          :to="`/customer/payment-accounts`"
          :title="$tc('payment_accounts.title', 2)"
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/customer/payment-accounts/${$route.params.payment_account_id}/edit-${formData.payment_account_type}`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
        <sw-button
          slot="activator"
          variant="primary"
          @click="removePaymentAccount($route.params.payment_account_id)"
        >
          {{ $t('general.delete') }}
        </sw-button>
      </template>
    </sw-page-header>

    <sw-card variant="customer-card">
      <!-- Contact Info  -->
      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('payment_accounts.contact_info') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <sw-input-group
            v-if="isACH"
            :label="$t('payment_accounts.name_on_account')"
            class="md:col-span-12"
          >
            <sw-input
              v-model="formData.first_name"
              :disabled="true"
              focus
              type="text"
              name="name"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            v-if="isCC"
            :label="$t('payment_accounts.name_on_card')"
            class="md:col-span-12"
          >
            <sw-input
              v-model="formData.first_name"
              :disabled="true"
              focus
              type="text"
              name="name"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.address_1')"
            class="md:col-span-4"
          >
            <sw-input
              v-model="formData.address_1"
              :disabled="true"
              focus
              type="text"
              name="address_1"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
           
            :label="$t('payment_accounts.address_2')"
            class="md:col-span-8"
          >
            <sw-input
              v-model="formData.address_2"
              :disabled="true"
              focus
              type="text"
              name="address_1"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.city')"
            class="md:col-span-4"
          >
            <sw-input
              v-model="formData.city"
              :disabled="true"
              name="formData.city"
              type="text"
              tabindex="10"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.state')"
            class="md:col-span-8"
          >
            <sw-input
              v-model="formData.state"
              :disabled="true"
              focus
              type="text"
              name="name"
              tabindex="1"
            />
          </sw-input-group>

          <div class="md:col-span-4">
            <sw-input-group :label="$t('payment_accounts.zip')">
              <sw-input
                tabindex="14"
                v-model.trim="formData.zip"
                :disabled="true"
                type="text"
                name="zip"
              />
            </sw-input-group>
          </div>

          <sw-input-group
            :label="$t('payment_accounts.country')"
            class="md:col-span-8"
          >
            <sw-input
              v-model="formData.country"
              :disabled="true"
              focus
              type="text"
              name="name"
              tabindex="1"
            />
          </sw-input-group>
        </div>
      </div>

      <sw-divider class="mb-5 md:mb-8" />

      <!-- Bank Account Information  -->
      <div v-if="isACH" class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('payment_accounts.bank_account_info') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <sw-input-group
            :label="$t('payment_accounts.ACH_type')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.ACH_type"
              :disabled="true"
              focus
              type="text"
              name="ACH_type"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.account_number')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.account_number_pass"
              :disabled="true"
              focus
              type="text"
              name="account_number"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.routing_number')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.routing_number_pass"
              focus
              :disabled="true"
              type="text"
              name="routing_number"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.bankname')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.bank_name"
              focus
              :disabled="true"
              type="text"
              name="bank_name"
              tabindex="1"
            />
          </sw-input-group>
        </div>
      </div>

      <!-- Credit Card Information  -->
      <div v-if="isCC" class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('payment_accounts.credit_card_info') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <sw-input-group
            :label="$t('payment_accounts.card_number')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.card_number_pass"
              :disabled="true"
              focus
              type="text"
              name="card_number"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('settings.payment_gateways.credit_cards')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.credit_cards_pass"
              :disabled="true"
              focus
              type="text"
              name="credit_card"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.cvv')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.cvv_pass"
              :disabled="true"
              focus
              type="text"
              name="cvv"
              tabindex="1"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payment_accounts.expiration_date')"
            class="md:col-span-3"
          >
            <sw-input
              v-model="formData.expiration_date"
              :disabled="true"
              focus
              type="password"
              name="expiration_date"
              tabindex="1"
            />
          </sw-input-group>
        </div>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import { mapActions } from 'vuex'
import _ from 'lodash'

export default {
  data() {
    return {
      isCopyFromBilling: false,
      isLoading: false,
      initLoad: false,
      formData: {
        first_name: null,
        last_name: null,
        country: null,
        state: null,
        city: null,
        address_1: null,
        address_2: null,
        zip: null,
        payment_account_type: null,

        card_number: null,
        credit_card: null,
        cvv: null,
        expiration_date: new Date(),

        ACH_type: null,
        account_number: null,
        routing_number: null,
        bank_name: null,
      },
    }
  },
  computed: {
    pageTitle() {
      return this.$t('payment_accounts.title')
    },
    isACH() {
      if (this.$route.name === 'paymentAccountCustomer.view.ACH') {
        return true
      }
      return false
    },
    isCC() {
      if (this.$route.name === 'paymentAccountCustomer.view.CC') {
        return true
      }
      return false
    },
  },
  created() {
    this.loadPaymentAccount()
  },
  methods: {
    ...mapActions('paymentAccountsCustomer', [
      'fetchPaymentAccount',
      'deletePaymentAccount',
    ]),

    ...mapActions('customFields', ['fetchCustomFields']),

    async loadPaymentAccount() {
      let id = this.$route.params.payment_account_id

      let response = await this.fetchPaymentAccount(id)

      this.formData = { ...this.formData, ...response.data.payment_accounts }
      this.formData.country = response.data.payment_accounts.country.name
      this.formData.state = response.data.payment_accounts.state.name
      let limit = 0
      if (this.formData.card_number) {
        const auxCardNumber = this.formData.card_number.toString().split('')
        let showCardNumber = ''
        let limit = auxCardNumber.length - 4
        auxCardNumber.forEach((el, i) => {
          if (i < limit) showCardNumber = showCardNumber + '*'
          else showCardNumber = showCardNumber + el
        })
        this.formData.card_number_pass = showCardNumber
      }
      if (this.formData.credit_card) {
        this.formData.credit_cards_pass = this.formData.credit_card
      }
      if (this.formData.cvv) {
        const auxCvv = this.formData.cvv.toString().split('')
        let showCvv = ''
        auxCvv.forEach(() => {
          showCvv = showCvv + '*'
        })
        this.formData.cvv_pass = showCvv
      }
      if (this.formData.account_number) {
        const auxAccountNumber = this.formData.account_number
          .toString()
          .split('')
        let showAccountNumber = ''
        limit = auxAccountNumber.length - 4
        auxAccountNumber.forEach((el, i) => {
          if (i < limit) showAccountNumber = showAccountNumber + '*'
          else showAccountNumber = showAccountNumber + el
        })
        this.formData.account_number_pass = showAccountNumber
      }
      if (this.formData.routing_number) {
        const auxRoutingNumber = this.formData.routing_number
          .toString()
          .split('')
        let showRoutingNumber = ''
        auxRoutingNumber.forEach(() => {
          showRoutingNumber = showRoutingNumber + '*'
        })
        this.formData.routing_number_pass = showRoutingNumber
      }
    },

    async removePaymentAccount(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payment_accounts.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePaymentAccount({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('payment_accounts.deleted_message', 1)
            )
            this.$router.push(`/customer/payment-accounts`)
            return true
          } else {
            window.toastr['error'](res.data.message)
            return true
          }
        }
      })
    },
  },
}
</script>