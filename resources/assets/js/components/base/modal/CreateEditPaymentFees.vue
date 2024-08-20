<template>
  <div class="mail-config-modal">
    <form action="" @submit.prevent="savePackagesGroup">
      <div class="p-4 md:p-8">
        <sw-input-group
          :label="$t('payment_fees.name')"
          class="mt-3"
          :error="name"
          required
        >
          <sw-input
            ref="name"
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="p-4 md:p-8">
        <sw-input-group
          :label="$t('payment_fees.type')"
          class="col-span-12 md:col-span-12 md:-mt-10"
        >
          <sw-select
            v-model="formData.type"
            :options="typeOptions"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :max-height="200"
            label="name"
          />
        </sw-input-group>
      </div>

      <div class="p-4 md:p-8">
        <sw-input-group
          :label="
            formData.type && formData.type.value
              ? formData.type.value === 'fixed'
                ? $t('general.amount')
                : $t('general.percentage')
              : ''
          "
          class="mt-4 pr-3"
          variant="vertical"
          required
        >
          <sw-money
            v-model.trim="price"
            :tabindex="2"
            class="relative w-full focus:border focus:border-solid focus:border-primary-500"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          variant="primary-outline"
          class="mr-3"
          @click="closeTaxModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button variant="primary" type="submit" :loading="isLoading">
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/outline'
const {
  required,
  minLength,
  email,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    PaperAirplaneIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      idRow: null,
      formData: {
        name: null,
        type: null,
        amount: 0,
        payment_gateway: null,
        authorize_setting_id: null,
        aux_vault_setting_id: null,
        paypal_settings_id: null,
      },
      typeOptions: [
        { name: 'fixed', value: 'fixed' },
        { name: 'percentage', value: 'percentage' },
      ],
    }
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    name() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.name.maxLength) {
        return this.$tc('validation.message_maxlength')
      }
    },

    price: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },
  },
  validations: {
    formData: {
      name: {
        required,
        maxLength: maxLength(100),
      },
    },
  },
  async mounted() {
    await this.setInitialData()
    this.$refs.name.focus = true
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('pack', ['saveName']),
    ...mapActions('group', ['addGroup']),

    ...mapActions('paymentFee', [
      'addPaymentFee',
      'fetchViewPaymentFee',
      'updatePaymentFee',
    ]),

    resetFormData() {
      this.formData = {
        name: null,
      }
      this.$v.formData.$reset()
    },
    async savePackagesGroup() {
      // console.log(this.formData)
      this.$v.formData.$touch()

      if (this.formData.type == null) {
        window.toastr['error']('The type must be selected')
        return false
      }

      if (this.formData.amount == null || this.formData.amount == 0) {
        window.toastr['error']('Price must be greater than zero.')
        return false
      }

      if (
        this.formData.type.value == 'percentage' &&
        this.formData.amount > 10000
      ) {
        window.toastr['error'](
          'If the payment fee is a percentage type, the amount must not exceed 100.'
        )
        return false
      }

      if (this.$v.$invalid) {
        this.isLoading = false
        return true
      }

      this.isLoading = true
      this.formData.type = this.formData.type.value
      // console.log(this.modalData)
      if (this.modalData.action == 'edit' && this.modalData.row != null) {
        this.formData.id = this.modalData.row.id
        let response = await this.updatePaymentFee(this.formData)

        //  console.log(response)

        if (!response || !response.data || response.data.success !== true) {
          window.toastr['error']('')
          this.isLoading = false
          return false
        } else {
          window.toastr['success'](this.$tc('pbx_services.item_update_success'))
          this.isLoading = false
          this.closeTaxModal()
          this.$router.go()
        }
      } else {
        let response = await this.addPaymentFee(this.formData)
        // console.log(response)

        if (!response || !response.data || response.data.success !== true) {
          window.toastr['error']('Operation error')
          this.isLoading = false
          return false
        } else {
          window.toastr['success'](this.$tc('pbx_services.item_added_success'))
          this.isLoading = false
          this.closeTaxModal()
          this.$router.go()
        }
      }

      this.isLoading = false
      return true
    },
    closeTaxModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
    setInitialData() {
      return new Promise((resolve, reject) => {
        if (this.modalData) {
          //  console.log(this.modalData)
          this.formData.payment_gateway = this.modalData.from
          if (this.modalData.from == 'authorize') {
            this.formData.authorize_setting_id = this.modalData.id
          }

          if (this.modalData.from == 'Paypal') {
            // console.log('entro')
            this.formData.paypal_settings_id = this.modalData.id
          }

          if (this.modalData.from == 'AuxVault') {
            this.formData.aux_vault_setting_id = this.modalData.id
          }

          if (this.modalData.action == 'edit' && this.modalData.row != null) {
            this.formData.name = this.modalData.row.name
            this.formData.amount = this.modalData.row.amount
            this.idRow = this.modalData.row.id
            if (this.modalData.row.type == 'fixed') {
              this.formData.type = { name: 'fixed', value: 'fixed' }
            } else {
              this.formData.type = { name: 'percentage', value: 'percentage' }
            }
          }
          //console.log(this.formData)
          resolve({
            viewType: true,
          })
        } else {
          reject(new Error('No modal data available'))
        }
      })
    },
  },
}
</script>
