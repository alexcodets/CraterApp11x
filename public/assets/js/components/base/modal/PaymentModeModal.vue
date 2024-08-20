<template>
  <form action="" @submit.prevent="submitPaymentMode">
    <div class="p-8 sm:p-6">
      <sw-input-group
        :label="$t('settings.customization.payments.mode_name')"
        :error="nameError"
        variant="horizontal"
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

      <sw-input-group
        :label="$t('providers.status')"
        variant="horizontal"
        class="mt-2"
      >
      <sw-select
          v-model="formData.status"
          :options="status"
          :searchable="true"
          :show-labels="false"
          :tabindex="16"
          :allow-empty="true"
          :placeholder="$t('providers.status')"
          label="text"
          track-by="value"
      />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_gateways.add_payment_gateway')"
        variant="horizontal"
        class="mt-2"
      >
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
              <sw-switch
                  v-model="formData.add_payment_gateway"
                  class="absolute mt-2"
                  style="top: -30px"
              />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_gateways.for_customer_use')"
        variant="horizontal"
        class="mt-2"
      >
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
              <sw-switch
                  v-model="formData.for_customer_use"
                  class="absolute mt-2"
                  style="top: -30px"
              />
          </div>
        </div>
      </sw-input-group>

      <!-- generate espense -->
      <sw-input-group
        :label="$t('settings.payment_gateways.generate_expense')"
        variant="horizontal"
        class="mt-2"
      >
        <div class="flex my-8 mb-4">
          <div class="relative w-12">
              <sw-switch
                  v-model="formData.generate_expense"
                  class="absolute mt-2"
                  style="top: -30px"
              />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        :label="$t('payments.account_accepted')"
        variant="horizontal"
        class="mt-2"
        :error="accountAcceptedError"
      >

      <!-- :invalid="$v.formData.account_accepted.$error"           -->
      
      <sw-select
        v-model="formData.account_accepted"
        :options="account_accepted"
        :searchable="true"
        :show-labels="false"
        :tabindex="16"
        :allow-empty="true"
        :placeholder="$t('providers.status')"
        label="text"
        track-by="value"
        @input="$v.formData.account_accepted.$touch()"
        :invalid="formData.account_accepted_error"
      />
      </sw-input-group>

    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid mt-10">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="closePaymentModeModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button :loading="isLoading" variant="primary" type="submit">
        <save-icon class="mr-2" v-if="!isLoading" />
        {{ !isEdit ? $t('general.save') : $t('general.update') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
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
      account_accepted: [
          {
              value: 'N',
              text: 'None'
          },
          {
              value: 'A',
              text: 'ACH',
          },
          {
              value: 'C',
              text: 'Credit Card',
          },
      ],
      formData: {
        id: null,
        name: null,
        add_payment_gateway: false,
        for_customer_use: false,
        generate_expense: false,
        status: {
          value: 'A',
          text: 'Active',
        },
        account_accepted: {
          value: 'N',
          text: 'None'
        },
        account_accepted_error: null,
        status: '',
        // account_accepted: '',
      },
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    accountAcceptedError() {
      if (this.formData.add_payment_gateway && (!this.formData.account_accepted.value || this.formData.account_accepted.value === '' || this.formData.account_accepted.value === 'N')) {
        this.formData.account_accepted_error = true;
        return this.$tc('validation.required')
      } else {
        this.formData.account_accepted_error = false;
        return ''
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
      account_accepted: {
        // required: this.formData.add_payment_gateway ? true : false
        // required
      }

    },
  },
  async mounted() {
    this.$refs.name.focus = true
   // console.log(this.formData.account_accepted);
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset();
    },
    async submitPaymentMode() {
      this.$v.formData.$touch()
      if (this.$v.$invalid || this.formData.account_accepted.error) {
        return true
      }
      this.isLoading = true
      let response
      this.formData.status = this.formData.status.value
      this.formData.account_accepted = this.formData.account_accepted.value
      

      if (this.isEdit) {
       // console.log(this.formData);
        response = await this.updatePaymentMode(this.formData)
        if (response.data) {
          window.toastr['success'](
            this.$t('settings.customization.payments.payment_mode_updated')
          )
          this.refreshData ? this.refreshData() : ''
          this.closePaymentModeModal()
          return true
        }
        window.toastr['error'](response.data.error)
      } else {
        try {
          response = await this.addPaymentMode(this.formData)
          if (response.data) {
            this.isLoading = false
            window.toastr['success'](
              this.$t('settings.customization.payments.payment_mode_added')
            )
            this.refreshData ? this.refreshData() : ''
            this.closePaymentModeModal()
            return true
          }
          window.toastr['error'](response.data.error)
        } catch (err) {
          this.isLoading = false
        }
      }
    },
    async setData() {
    

      let a_type_name = ''
      switch (this.modalData.status) {
        case 'A':
          a_type_name = 'Active'
          break
        case 'I':
          a_type_name = 'Inactive'
          break
        default:
          break
      }

      let a_type_accept = ''
      switch (this.modalData.account_accepted) {
        case 'N':
          a_type_accept = 'None'
          break
        case 'A':
          a_type_accept = 'ACH'
          break
        case 'C':
          a_type_accept = 'Credit Card'
          break
        default:
          break
      }

      // let array=[];
      // array.push({value:this.modalData.status, text:a_type_name});
     

      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
        add_payment_gateway: this.modalData.add_payment_gateway,
        status: {value:this.modalData.status, text:a_type_name},
        account_accepted: {value:this.modalData.account_accepted, text:a_type_accept},
        for_customer_use: this.modalData.for_customer_use,
        generate_expense: this.modalData.generate_expense ? 1 : 0
      }
      //  console.log('modal data:' , this.formData);

    },
    closePaymentModeModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
