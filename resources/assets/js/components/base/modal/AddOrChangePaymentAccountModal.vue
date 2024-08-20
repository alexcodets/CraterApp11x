<template>
  <div class="overflow-auto mb-2 mt-0 ml-1 mr-1" :style="isModalChange ? 'height:32vh;' : 'height:67vh;'">
    <base-loader v-if="isLoadModal" :show-bg-overlay="true" />
    <div v-else>
      <form action="" @submit.prevent="submitForm">
        <div class="p-10 sm:p-6">
          <!-- Select para tipo de cuenta de pago (CC O ACH) -->
          <sw-input-group :label="$t('payment_accounts.payment_account_type')" class="mb-4 mt-2" required>
            <sw-select v-model="payment_account_type" :options="types" :searchable="true" :show-labels="false"
              :allow-empty="false" :placeholder="$t('payment_accounts.select_a_payment_account_type')" class=""
              track-by="value" label="name" :tabindex="1" @select="selectType" />
          </sw-input-group>

          <sw-divider class="mb-3 md:mb-3" />

          <div v-if="isCC && !isACH && !isModalChange">
            <sw-input-group :label="$t('payment_accounts.name_on_card')" class="mb-4" :error="displayFirstNameError"
              required>
              <sw-input :invalid="$v.formData.first_name.$error" v-model="formData.first_name" focus type="text"
                name="name" tabindex="1" @input="$v.formData.first_name.$touch()" autocomplete="off" />
            </sw-input-group>

            <sw-input-group :label="$t('payment_accounts.card_number')" class="mb-4" :error="cardNumberError" required>
              <sw-input :invalid="$v.formData.card_number.$error" v-model="formData.card_number" focus :type="getInputType"
                name="card_number" tabindex="1" @input="$v.formData.card_number.$touch()" autocomplete="off" >
                <template v-slot:rightIcon>
                  <eye-off-icon v-if="isShowNumber" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber = !isShowNumber" />
                  <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber = !isShowNumber" />
                </template>
                </sw-input>
            </sw-input-group>

            <sw-input-group :label="$t('settings.payment_gateways.credit_cards')" class="mb-4" required>
              <sw-select v-model="formData.credit_cards" :options="credit_cards" :searchable="true" :show-labels="false"
                :allow-empty="true" :tabindex="1" :placeholder="$t('payment_accounts.select_a_credit_card_type')"
                :invalid="$v.formData.credit_cards.$error" class="mt-2" label="name" />
            </sw-input-group>

            <sw-input-group :label="$t('payment_accounts.cvv')" class="mb-4" :error="CvvError" required>
              <sw-input :invalid="$v.formData.cvv.$error" v-model="formData.cvv" focus :type="getInputType2" name="cvv"
                tabindex="1" @input="$v.formData.cvv.$touch()" autocomplete="off">
                <template v-slot:rightIcon>
                  <eye-off-icon v-if="isShowNumber2" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber2 = !isShowNumber2" />
                  <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber2 = !isShowNumber2" />
                </template>
              </sw-input>
            </sw-input-group>

            <creditCardExpirationDate v-model="formData.expiration_date" class="mb-2"
              :invalid="$v.formData.expiration_date.$error" />
          </div>

          <div v-if="isACH && !isCC && !isModalChange">
            <sw-input-group :label="$t('payment_accounts.name_on_account')" class="mb-4" :error="displayFirstNameError"
              required>
              <sw-input :invalid="$v.formData.first_name.$error" v-model="formData.first_name" focus type="text"
                name="name" tabindex="1" @input="$v.formData.first_name.$touch()" autocomplete="off" />
            </sw-input-group>

            <sw-input-group :label="$t('payment_accounts.ACH_type')" class="mb-4" :error="ACHTypeError" required>
              <sw-select v-model="formData.ACH_type" :options="bank_account_type" :invalid="$v.formData.ACH_type.$error"
                :placeholder="$t('payment_accounts.select_an_account_type')" :searchable="true" :show-labels="false"
                :tabindex="1" :allow-empty="false" label="text" track-by="value" />
            </sw-input-group>

            <sw-input-group :label="$t('payment_accounts.account_number')" class="mb-4" :error="accountNumberError"
              required>
              <sw-input :invalid="$v.formData.account_number.$error" v-model="formData.account_number" focus
                :type="getInputType" name="account_number" tabindex="1" @input="$v.formData.account_number.$touch()"
                autocomplete="off">
                <template v-slot:rightIcon>
                  <eye-off-icon v-if="isShowNumber" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber = !isShowNumber" />
                  <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber = !isShowNumber" />
                </template>
              </sw-input>
            </sw-input-group>

            <sw-input-group :label="$t('payment_accounts.routing_number')" class="mb-4" :error="routingNumberError"
              required>
              <sw-input :invalid="$v.formData.routing_number.$error" v-model="formData.routing_number" focus
                :type="getInputType2" name="routing_number" tabindex="1" @input="$v.formData.routing_number.$touch()"
                autocomplete="off" >
                <template v-slot:rightIcon>
                  <eye-off-icon v-if="isShowNumber2" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber2 = !isShowNumber2" />
                  <eye-icon v-else class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                    @click="isShowNumber2 = !isShowNumber2" />
                </template>
              </sw-input>

            </sw-input-group>

            <sw-input-group :label="$t('payment_accounts.bankname')" class="mb-2">
              <sw-input v-model="formData.bank_name" focus type="text" name="bank_name" tabindex="1"
                autocomplete="off" />
            </sw-input-group>
          </div>

          <div v-if="isModalChange">
            <sw-input-group :label="$t('payment_accounts.payment_account')" class="mb-2 mt-2" required>
              <sw-select v-model="payment_account" :options="formatted_payment_accounts" :searchable="true"
                :show-labels="false" :allow-empty="false" :placeholder="$t('payment_accounts.select_a_payment_account')"
                class="" track-by="id" :label="isCC ? 'card_number_cvv' : 'name_account_number'" :tabindex="1"
                :invalid="$v.payment_account.$error" />
            </sw-input-group>
          </div>

        </div>
        <div class="z-0 flex justify-end mt-2 p-4 border-t border-gray-200 border-solid">
          <sw-button class="mr-3" variant="primary-outline" type="button" @click="closeItemCategoryModal">
            {{ $t('general.cancel') }}
          </sw-button>
          <sw-button :loading="isLoading" variant="primary" icon="save" type="submit">
            <save-icon v-if="!isLoading" class="mr-2" />
            {{
    isModalChange ? $t('payment_accounts.change_account') : $t('payment_accounts.add_account')
  }}
          </sw-button>
        </div>
      </form>
    </div>

  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import _ from 'lodash'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
import { DocumentDuplicateIcon, XCircleIcon } from '@vue-hero-icons/solid'
import { VueDatePicker } from '@mathieustan/vue-datepicker';
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css';
import creditCardExpirationDate from '@/components/payments/creditCardExpirationDate.vue'
const {
  required,
  minLength,
  maxLength,
  between,
  alphaNum,
  alpha,
  email,
  url,
  sameAs,
  numeric
} = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentDuplicateIcon,
    VueDatePicker,
    creditCardExpirationDate,
    XCircleIcon,
    EyeIcon,
    EyeOffIcon
  },
  data() {
    return {
      isLoadModal: false,
      isCopyFromBilling: false,
      isLoading: false,
      initLoad: false,
      isShowNumber: false,
      isShowNumber2: false,
      isEditButton: false,
      locale: { lang: 'en' },
      formData: {
        first_name: null,
        country_id: null,
        state_id: null,
        city: null,
        address_1: null,
        address_2: null,
        zip: null,
        payment_account_type: null,
        client_id: null,
        status: "A",
        card_number: null,
        cvv: null,
        expiration_date: new Date(),
        credit_cards: null,
        ACH_type: null,
        account_number: null,
        routing_number: null,
        bank_name: null,
      },
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
      bank_account_type: [
        {
          value: 'Checking',
          text: 'Checking',
        },
        {
          value: 'Savings',
          text: 'Savings',
        },
      ],
      billing_state: null,
      billing_country: null,
      countries: [],
      billing_states: [],
      credit_cards: [
        { name: 'VISA', value: 'VISA' },
        { name: 'MASTERCARD', value: 'MASTERCARD' },
        { name: 'AMERICAN EXPRESS', value: 'AMERICAN EXPRESS' },
        { name: 'DISCOVER', value: 'DISCOVER' },
      ],
      //
      payment_account_type: { name: this.$t('payment_accounts.types.credit_card'), value: 'CC' },
      types: [
        { name: this.$t('payment_accounts.types.credit_card'), value: 'CC' },
        { name: this.$t('payment_accounts.types.ach'), value: 'ACH' },
      ],
      payment_account: null,
      payment_accounts: [],
      formatted_payment_accounts: []
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),

    isCC() {
      if (this.payment_account_type?.value == "CC") {
        return true
      }
      return false
    },

    isACH() {
      if (this.payment_account_type?.value == "ACH") {
        return true
      }
      return false
    },

    isModalChange() {
      if (this.modalData.type == "CHANGE") {
        return true
      }
      return false
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
    getInputType() {
      if (this.isShowNumber) {
        return 'text'
      }
      return 'password'
    },
    getInputType2() {
      if (this.isShowNumber2) {
        return 'text'
      }
      return 'password'
    },
    cardNumberError() {
      if (!this.$v.formData.card_number.$error) {
        return ''
      }
      if (!this.$v.formData.card_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.card_number.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.card_number.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.card_number.$params.minLength.min,
          { count: this.$v.formData.card_number.$params.minLength.min }
        )
      }
      if (!this.$v.formData.card_number.maxLength) {
        return this.$t('authorize.cc_number_maxLength')
      }
    },
    CvvError() {
      if (!this.$v.formData.cvv.$error) {
        return ''
      }
      if (!this.$v.formData.cvv.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.cvv.numeric) {
        return this.$tc('validation.numbers_only')
      }
      if (!this.$v.formData.cvv.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.cvv.$params.minLength.min,
          { count: this.$v.formData.cvv.$params.minLength.min }
        )
      }
      if (!this.$v.formData.cvv.maxLength) {
        return this.$t('authorize.cvv_maxLength')
      }
    },
    DateError() {
      if (!this.$v.formData.expiration_date.$error) {
        return ''
      }
      if (!this.$v.formData.expiration_date.required) {
        return this.$t('validation.required')
      }
    },
    ACHTypeError() {
      if (!this.$v.formData.ACH_type.$error) {
        return ''
      }
      if (!this.$v.formData.ACH_type.required) {
        return this.$tc('validation.required')
      }
    },
    accountNumberError() {
      if (!this.$v.formData.account_number.$error) {
        return ''
      }
      if (!this.$v.formData.account_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.account_number.minLength) {
        return this.$t('validation.account_number_minLength')
      }
      if (!this.$v.formData.account_number.maxLength) {
        return this.$t('validation.account_number_maxLength')
      }
    },
    routingNumberError() {
      if (!this.$v.formData.routing_number.$error) {
        return ''
      }
      if (!this.$v.formData.routing_number.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.routing_number.minLength) {
        return this.$t('validation.routing_number_minLength')
      }
      if (!this.$v.formData.routing_number.maxLength) {
        return this.$t('validation.routing_number_maxLength')
      }
    },

  },
  validations() {
    if (this.isACH && !this.isCC && !this.isModalChange) {
      return {
        formData: {
          first_name: {
            required,
            minLength: minLength(3),
          },
          country_id: {
            required,
          },
          state_id: {
            required,
          },
          city: {
            required,
          },
          address_1: {
            required,
            maxLength: maxLength(255),
          },
          address_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
          status: {
            required,
          },
          ACH_type: {
            required,
          },
          account_number: {
            required,
            minLength: minLength(9),
            maxLength: maxLength(16),
          },
          routing_number: {
            required,
            minLength: minLength(8),
            maxLength: maxLength(10),
          },
        },
      }
    } else if (this.isCC && !this.isACH && !this.isModalChange) {
      return {
        formData: {
          first_name: {
            required,
            minLength: minLength(3),
          },
          country_id: {
            required,
          },
          state_id: {
            required,
          },
          city: {
            required,
          },
          address_1: {
            required,
            maxLength: maxLength(255),
          },
          address_2: {
            maxLength: maxLength(255),
          },
          zip: {
            required,
          },
          status: {
            required,
          },
          card_number: {
            required,
            numeric,
            minLength: minLength(13),
            maxLength: maxLength(19),
          },
          credit_cards: {
            required,
          },
          cvv: {
            required,
            numeric,
            minLength: minLength(3),
            maxLength: maxLength(4),
          },
          expiration_date: {
            required,
          },
        },
      }
    } else if (this.isModalChange) {
      return {
        payment_account: {
          required,
        },
      }
    }
  },

  async created() {
    await this.LoadData()
  },

  async mounted() {
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('item', ['addItemCategory', 'updateItemCategory']),
    ...mapActions('payment', ['fetchPaymentModesCorePosMoney']),
    ...mapActions('paymentMultiple', ['getPaymentMethods']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('paymentAccounts', [
      'addPaymentAccount',
      'fetchPaymentAccount',
      'fetchPaymentAccounts',
      'updatePaymentAccount',
    ]),

    async LoadData() {
      this.isLoadModal = true
      if (this.isModalChange) {
        let data = {
          customer_id: this.modalData.client_id,
          limit: 1000,
        }
        let response = await this.fetchPaymentAccounts(data)
        if (response.status == 200) {
          this.payment_accounts = [...response.data.payment_accounts.data]

          if (this.modalData.current_payment_account.payment_account_type == "CC") {
            this.payment_account_type = { name: this.$t('payment_accounts.types.credit_card'), value: 'CC' }
          } else {
            this.payment_account_type = { name: this.$t('payment_accounts.types.ach'), value: 'ACH' }
          }

          await this.setPaymentAccounts()
          this.payment_account =
            this.formatted_payment_accounts.find(({ id }) => id == this.modalData.current_payment_account.id)
        }
        this.isLoadModal = false
        return true
      }

      this.setBillingAddress(this.modalData.client_id, this.modalData.billing_address)
      this.isLoadModal = false
      return true
    },

    selectType() {
      this.isShowNumber = false;
      this.isShowNumber2 = false;
      setTimeout(async () => {
        this.payment_account = null
        await this.setPaymentAccounts()
      }, 100);
    },

    async setPaymentAccounts() {
      if (this.isCC) {
        this.formatted_payment_accounts = this.payment_accounts
          .filter((el) => {
            return el.payment_account_type == 'CC'
          })
          .map((el) => {
            const auxCardNumber = el.card_number.toString().split('')
            let showCardNumber = ''
            const limit = auxCardNumber.length - 4
            auxCardNumber.forEach((el, i) => {
              if (i < limit) showCardNumber = showCardNumber + '*'
              else showCardNumber = showCardNumber + el
            })
            return {
              ...el,
              card_number_cvv: el.credit_card + ' ' + showCardNumber,
            }
          })
      } else {
        this.formatted_payment_accounts = this.payment_accounts
          .filter((el) => {
            return el.payment_account_type == 'ACH'
          })
          .map((el) => {
            const auxAccountNumber = el.account_number.toString().split('')
            let showAccountNumber = ''
            const limit = auxAccountNumber.length - 4
            auxAccountNumber.forEach((el, i) => {
              if (i < limit) showAccountNumber = showAccountNumber + '*'
              else showAccountNumber = showAccountNumber + el
            })
            return {
              ...el,
              name_account_number: el.first_name + ' ' + showAccountNumber,
            }
          })
      }
    },

    setBillingAddress(client_id, billing_address) {
      this.formData.client_id = client_id
      this.formData.country_id = billing_address?.country_id
      this.formData.state_id = billing_address?.state_id
      this.formData.city = billing_address?.city
      this.formData.address_1 = billing_address?.address_street_1
      this.formData.address_2 = billing_address?.address_street_2
      this.formData.zip = billing_address?.zip
    },

    async submitForm() {

      if (this.isModalChange) {
        this.$v.payment_account.$touch()
        if (this.$v.$invalid) {
          return true
        }
        this.isLoading = true

        window.toastr['success'](this.$t('payment_accounts.change_successfully'))
        window.hub.$emit('changePaymentAccount', this.payment_account)

        this.isLoading = false
        this.closeItemCategoryModal()
        return true
      } else {
        if (this.isCC) {
          this.formData.payment_account_type = 'CC'
        } else if (this.isACH) {
          this.formData.ACH_type = this.formData.ACH_type?.value
          this.formData.payment_account_type = 'ACH'
          this.formData.expiration_date = null
        }

        this.$v.formData.$touch()
        if (this.$v.$invalid) {
          return true
        }

        let response = null
        try {
          response = await this.addPaymentAccount(this.formData)
          if (response.data) {
            this.isLoading = false
            window.toastr['success'](this.$t('general.created_successfully'))
            window.hub.$emit('addPaymentAccount', response.data.payment_accounts)
            this.refreshData ? this.refreshData() : ''
            this.closeItemCategoryModal()
            return true
          } else {
            window.toastr['error'](response)
          }
        } catch (error) {
          this.isLoading = false
          // console.log("catch: ", error)
          window.toastr['error'](error)
        }
      }
    },

    closeItemCategoryModal() {
      this.resetModalData()
      this.closeModal()
    },
  },
}
</script>
