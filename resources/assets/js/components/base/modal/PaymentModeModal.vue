<template>
  <form action="" @submit.prevent="submitPaymentMode">
    <div class="p-8 sm:p-6">
      <sw-input-group
        :label="$t('settings.customization.payments.mode_name')"
        :error="nameError"
        variant="horizontal"
        required
        class="mb-4"
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
        class="mb-4"
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
        :label="$t('settings.payment_gateways.only_cash')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.only_cash"
              class="absolute"
              style="top: -20px"
              @change="changeBoolStatus('only_cash', formData.only_cash)"
            />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_gateways.add_payment_gateway')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.add_payment_gateway"
              class="absolute"
              style="top: -20px"
              @change="changeBoolStatus('payment_gateway')"
              :disabled="isOnlyCashActive"
            />
          </div>
        </div>
      </sw-input-group>
     
      <sw-input-group
        v-if="formData.add_payment_gateway"
        :label="$t('payments.account_accepted')"
        variant="horizontal"
        class="mb-4"
        :error="accountAcceptedError"
      >
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
          :invalid="$v.formData.account_accepted.$error"
        />
      </sw-input-group>
      
      <sw-input-group
        :label="$t('settings.payment_gateways.paypal_button')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.paypal_button"
              class="absolute"
              style="top: -20px"
              @change="changeBoolStatus('paypal')"
              :disabled="isOnlyCashActive"
            />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_gateways.stripe_button')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.stripe_button"
              class="absolute"
              style="top: -20px"
              @change="changeBoolStatus('stripe')"
              :disabled="isOnlyCashActive"
            />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_gateways.for_customer_use')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.for_customer_use"
              class="absolute"
              style="top: -20px"
            />
          </div>
        </div>
      </sw-input-group>

      <!-- generate espense -->
      <sw-input-group
        :label="$t('settings.payment_gateways.generate_expense')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.generate_expense"
              class="absolute"
              style="top: -20px"
            />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        v-if="formData.generate_expense"
        :label="$t('payments.expense_categories')"
        variant="horizontal"
        class="mb-4"
        :error="expenseError"
      >
        <sw-select
          v-model="formData.generate_expense_id"
          :options="getExpenseCategories"
          :searchable="true"
          :show-labels="false"
          :tabindex="16"
          :allow-empty="true"
          :placeholder="$t('payments.categories')"
          label="name"
          track-by="name"
          @input="$v.formData.generate_expense_id.$touch()"
          :invalid="$v.formData.generate_expense_id.$error"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_modes.void_refund')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.void_refund"
              class="absolute"
              style="top: -20px"
            />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        v-if="formData.void_refund"
        :label="$t('payments.expense_categories')"
        variant="horizontal"
        class="mb-4"
        :error="voidRefundExpenseError"
      >
        <sw-select
          v-model="formData.void_refund_expense_id"
          :options="getExpenseCategories"
          :searchable="true"
          :show-labels="false"
          :tabindex="16"
          :allow-empty="true"
          :placeholder="$t('payments.categories')"
          label="name"
          track-by="name"
          @input="$v.formData.void_refund_expense_id.$touch()"
          :invalid="$v.formData.void_refund_expense_id.$error"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_modes.default_payment_method_for_expense_import')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.expense_import"
              class="absolute"
              style="top: -20px"
            />
          </div>
        </div>
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.payment_modes.show_notes_table')"
        variant="horizontal"
        class="mb-4"
      >
        <div class="flex mt-2">
          <div class="relative w-12">
            <sw-switch
              v-model="formData.show_notes_table"
              class="absolute"
              style="top: -20px"
            />
          </div>
        </div>
      </sw-input-group>

    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid mt-10">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="swalClosePaymentModal"
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
const { required, minLength, requiredIf } = require('vuelidate/lib/validators')

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
        only_cash: false,
        add_payment_gateway: false,
        paypal_button: false,
        stripe_button: false,
        for_customer_use: false,
        generate_expense: false,
        status: {
          value: 'A',
          text: 'Active',
        },
        account_accepted: null,
        generate_expense_id: null,
        void_refund: false,
        void_refund_expense_id: null,
        expense_import: false,
        show_notes_table:false,
      },
      isOnlyCashActive: false,
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    ...mapGetters('category', ['categories']),

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    accountAcceptedError() {
      if (!this.$v.formData.account_accepted.$error) {
        return ''
      }
      if (!this.$v.formData.account_accepted.required) {
        return this.$tc('validation.required')
      }
    },

    expenseError() {
      if (!this.$v.formData.generate_expense_id.$error) {
        return ''
      }
      if (!this.$v.formData.generate_expense_id.required) {
        return this.$tc('validation.required')
      }
    },

    voidRefundExpenseError() {
      if (!this.$v.formData.void_refund_expense_id.$error) {
        return ''
      }
      if (!this.$v.formData.void_refund_expense_id.required) {
        return this.$tc('validation.required')
      }
    },

    getExpenseCategories() {
      return this.categories.map((category) => {
        return {
          ...category,
        }
      })
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
      account_accepted: {
        required: requiredIf(function () {
          return this.formData.add_payment_gateway
        }),
      },
      generate_expense_id : {
        required: requiredIf(function () {
          return this.formData.generate_expense
        }),
      },
      void_refund_expense_id : {
        required: requiredIf(function () {
          return this.formData.void_refund
        }),
      },
    },
  },
  async mounted() {
    this.$refs.name.focus = true
    this.fetchCategories({ limit: 'all' })
    //console.log(this.formData.account_accepted);
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
    ...mapActions('category', ['fetchCategories']),

    changeBoolStatus (val, state = false){     

      if(val == "only_cash")
      {
        if(state)
        {
          this.isOnlyCashActive = true
          this.formData.add_payment_gateway = false
          this.formData.paypal_button = false
          this.formData.stripe_button = false
        }else{
          this.isOnlyCashActive = false
        }        
      }else{
        if(val == "paypal")
        {
        this.formData.add_payment_gateway = false
        this.formData.account_accepted = 'N'
        }

        if(val == "stripe")
        {
        this.formData.add_payment_gateway = false
        this.formData.account_accepted = 'N'
        }

        if(val == "payment_gateway")
        {
          this.formData.paypal_button = false
          this.formData.stripe_button = false
        }
      }

    },

    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset();
    },

    async submitPaymentMode() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      this.isLoading = true
      let response
      //console.log(this.formData);
      this.formData.status = this.formData.status.value
      this.formData.account_accepted = this.formData.account_accepted ? this.formData.account_accepted?.value  : "N"
      this.formData.generate_expense_id = this.formData.generate_expense ? this.formData.generate_expense_id.id : null
      this.formData.void_refund_expense_id = this.formData.void_refund ? this.formData.void_refund_expense_id.id : null

      if (this.isEdit) {
        //console.log(this.formData);
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
          if (response.data) 
          {
            window.hub.$emit('newPaymentMode', response.data.paymentMethod)
            this.isLoading = false
            window.toastr['success'](this.$t('settings.customization.payments.payment_mode_added'))
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
        only_cash: this.modalData.only_cash,
        add_payment_gateway: this.modalData.add_payment_gateway,
        paypal_button: this.modalData.paypal_button,
        stripe_button: this.modalData.stripe_button,
        status: { value:this.modalData.status, text:a_type_name },
        account_accepted: { value:this.modalData.account_accepted, text:a_type_accept },
        for_customer_use: this.modalData.for_customer_use,
        generate_expense: this.modalData.generate_expense ? 1 : 0,
        void_refund: this.modalData.void_refund ? 1 : 0,
        generate_expense_id: this.getExpenseCategories.find(
          (exp) =>  exp.id === this.modalData.generate_expense_id
        ),
        void_refund_expense_id: this.getExpenseCategories.find(
          (_exp) => _exp.id === this.modalData.void_refund_expense_id
        ),
        expense_import: this.modalData.expense_import,
        show_notes_table: this.modalData.show_notes_table,
      }
        //console.log('modal data:' , this.formData);

    },
    swalClosePaymentModal(){
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('general.cancel_text'),
        icon: 'error',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          this.resetModalData()
          this.resetFormData()
          this.closeModal()
        }
      })
    },

    closePaymentModeModal() {

      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
