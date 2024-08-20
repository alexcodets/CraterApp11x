<template>
  <div class="contact-modal">
    <form action="" @submit.prevent="submit">

      <sw-card class="">

        <sw-input-group :error="amountError" :label="$t('core_pos.amount')" class="mt-4">
          <sw-money :currency="defaultCurrencyForInput" class="focus:border focus:border-solid focus:border-primary"
            v-model.number="formData.amount" @input="$v.formData.amount.$touch()" />
        </sw-input-group>

        <sw-input-group :error="descriptionError" :label="$t('core_pos.description')" class="mt-4">
          <sw-textarea rows="4" cols="50" v-model="formData.description" @input="$v.formData.description.$touch()" />
        </sw-input-group>

        <!-- BUTTON -->
        <div class="mt-5 flex">
          <sw-button :loading="isLoading" variant="primary" icon="save">
            <save-icon v-if="!isLoading" class="mr-2" />
            {{ $t('general.save') }}
          </sw-button>
          <sw-button type="button" class="ml-1" :loading="isLoading" variant="primary-outline" @click="closeIncomeWithdrawalModal">
            {{ $t('general.cancel') }}
          </sw-button>
        </div>

      </sw-card>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon } from "@vue-hero-icons/outline"
import moment from 'moment';
const {
  required,
  numeric,
  minValue,
  minLength,
  email,
  url,
  maxLength,
  sameAs,
} = require('vuelidate/lib/validators')
export default {
  components: {
    CheckCircleIcon
  },
  data() {
    return {
      title: '',
      isLoading: false,
      formData: {
        amount: 0,
        description: '',
        cash_histories_id: null,
        cash_register_id: null,
        type: null,
      }

    }
  },
  validations: {
    formData: {
      amount: {
        minValue: minValue(0.1)
      },
      description:{
        required
      }
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),

    amountError() {
      if (!this.$v.formData.amount.$error) {
        return "";
      }
      if (!this.$v.formData.amount.minValue) {
        return this.$t("validation.price_minvalue");
      }
    },
    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return "";
      }
      if (!this.$v.formData.description.required) {
        return this.$tc('validation.required')
      }
    },

  },

  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('corePos', ['addCashHistory']),

    loadData() {
      this.formData.cash_histories_id = this.modalData.cash_histories_id
      this.formData.cash_register_id = this.modalData.cash_register_id
      this.formData.type = this.modalData.type

    },

    async submit() {

      this.$v.formData.$touch();

      if (this.$v.$invalid) {
        return false
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('core_pos.message_income_withdrawal_cash'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const res = await window.axios.post('/api/v1/core-pos/register-income-withdrawal', this.formData)

          if (res.data.success) {
            window.toastr['success'](this.$t('core_pos.success'))
            this.closeIncomeWithdrawalModal()
            window.location.reload()
            return true
          }

          window.toastr['error']('Error')
          return true
        }
      })
    },

    closeIncomeWithdrawalModal(){
      this.resetModalData()
      this.closeModal()
    }

  },
}
</script>
<style lang="scss">
.note-modal {
  .header-editior .editor-menu-bar {
    margin-left: 0.5px;
    margin-right: 0px;
  }
}
</style>
