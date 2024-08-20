<template>
    <form action="" @submit.prevent="submitPosMoney">
     
        <div class="p-10 sm:p-6">

          <sw-input-group
            :label="'Name'"
            :error="nameError"
            required
            class="mb-8 mt-4"
            >
                <sw-input
                v-model="formData.name"
                pattern="([^\s][A-z0-9À-ž\s]+)"
                title="The name cannot contain special characters"
                :invalid="$v.formData.name.$error"
                @input="$v.formData.name.$touch()"
                />
            </sw-input-group>

            <sw-input-group
                :label="$t('settings.core_pos.payment_methods')"
                class="mb-8 mt-6"  
            >
            <sw-select                
                v-model="payment_methods_ids"
                :options="payment_methods_module"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('payments.select_a_payment_method')"
                class=""
                track-by="id"
                label="name"
                :tabindex="11"
                :multiple=true
            />
            </sw-input-group>

            <sw-input-group
            :label="'Amount'"
            :error="amountError"
            required
            class="mb-8 mt-4"
            >
                <sw-money
                    :currency="defaultCurrencyForInput"
                    class="focus:border focus:border-solid focus:border-primary"
                    v-model.number="formData.amount"
                    :invalid="$v.formData.amount.$error"
                    @input="$v.formData.amount.$touch()"
                />
            </sw-input-group>

            <sw-input-group
                :label="'Currency'"
                class="mb-8 mt-6"
                required
            >
            <sw-select
            :invalid="$v.currency.$error"
                v-model="currency"
                :options="currencies"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :placeholder="$t('items.select_a_type')"
                class=""
                track-by="id"
                label="name"
                :tabindex="11"
            />
            </sw-input-group>

            <sw-input-group
                :label="'Is Coin'"
                class="mb-8 ml-1"
            >
                <div class="flex mt-2">
                    <div class="relative w-12">
                        <sw-switch
                        v-model="formData.is_coin"
                        class="absolute"
                        style="top: -20px"
                        />
                    </div>
                </div>
            </sw-input-group>

        </div>
          
    
      <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeItemCategoryModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          variant="primary"
          icon="save"
          type="submit"
        >
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex'
  const { required, minLengt, between, alphaNum, alpha } = require('vuelidate/lib/validators')
  
  export default {
    data() {
      return {
        payment_methods_ids: [],
        payment_methods_module: [],
        isEdit: false,
        isLoading: false,
        formData: {
          id: null,
          amount: 0.01,
          is_coin: false,
          currency_id: null
        },   
        currency: null     
        //currencies: []
      }
    },
    computed: {
      ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),
      ...mapGetters(['currencies']),
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
      amountError() {
        if (!this.$v.formData.amount.$error) {
          return ''
        }
  
        if (!this.$v.formData.amount.required) {
          return this.$tc('validation.required')
        }

        if (!this.$v.formData.amount.between) {
          return 'minimum amount must be greater than 0.00'
        }

      },
      currencyError() {
        if (!this.$v.currency.$error) {
          return ''
        }
  
        if (!this.$v.currency.required) {
          return this.$tc('validation.required')
        }
      },
    },
    validations: {
      formData: {
        name: {
          required,          
        },
        amount: {
          required,
          between: between(0.01, 999999999999999999.00),
        },        
      },
      currency: {
          required,
      },
    },

    async created(){      
    },
  
    async mounted() {
      let response = await this.getPaymentMethods()
      if(response.data.success){
        this.payment_methods_module = response.data.payment_methods
      }

      if (!this.modalDataID)
      {
        let response_CS = await this.fetchCompanySettings(['currency'])
        let company_currency = response_CS.data.currency

        this.currency = this.currencies.filter(
        (currency) => currency.id == company_currency
        )[0]
      }
     
      if (this.modalDataID) {
        this.isEdit = true
        this.setData()
      }
    },
    methods: {
      ...mapActions('modal', ['closeModal', 'resetModalData']),
      ...mapActions('item', ['addItemCategory', 'updateItemCategory']),
      ...mapActions('payment', ['fetchPaymentModesCorePosMoney']),
      ...mapActions('paymentMultiple', ['getPaymentMethods']),
      ...mapActions('company', ['fetchCompanySettings']),

      resetFormData() {
        this.formData = {
          id: null,
          name: null,
          is_group: false,
          is_item: false
        }
        this.$v.formData.$reset()
      },
      async submitPosMoney() {

        if(this.currency == null)
        {
            window.toastr['error']('currency field cannot be empty')
            return
        }

        this.$v.formData.$touch()
        if (this.$v.$invalid) {
          return true
        }  

        //
        this.formData.currency_id = this.currency.id
                           
        this.formData.payment_methods_ids = this.payment_methods_ids.length > 0 
                                ? this.payment_methods_ids.map(pm =>{
                                    return pm.id
                                  })
                                : [],

        this.isLoading = true
        let response
  
        try {
          if (!this.isEdit) {
            response = await window.axios.post(`/api/v1/core-pos/money/addMoney`, this.formData)
          } else {
            response = await window.axios.post(`/api/v1/core-pos/money/updateMoney`, this.formData)
          }
  
          if (response.data) {
            this.isLoading = false
            if (!this.isEdit) {
              window.toastr['success']('Money added successfully')
            } else {
              window.toastr['success']('money updated successfully')
            }
            this.refreshData ? this.refreshData() : ''
            this.closeItemCategoryModal()
            return true
          }
        } catch (error) {
          this.isLoading = false
          window.toastr['error'](response.data.error)
        }
      },

      async setData() {
        this.formData = {
          id: this.modalData.id,
          name: this.modalData.name,
          amount: parseFloat(this.modalData.amount),
          is_coin: this.modalData.is_coin == 1 ? true : false
        }
        this.currency = this.currencies.filter(
        (currency) => currency.id == this.modalData.currency_id
        )[0]

        this.payment_methods_ids = [ ...this.modalData.payment_methods]       
      },

      closeItemCategoryModal() {
        this.resetModalData()
        this.resetFormData()
        this.closeModal()
      },
    },
  }
  </script>
  