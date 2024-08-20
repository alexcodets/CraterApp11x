<template>
    <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
    <td colspan="5" class="p-0 text-left align-top">
      <table class="w-full mb-4">
        <colgroup>
          <col style="width: 60%" />
          <col style="width: 20%" />
          <col style="width: 5%" />  
          <col style="width: 15%" />   
        </colgroup>
        <tbody>
          <tr>
            <td class="pt-6 pb-2 pl-4 align-center">
              <div class="flex z-40 justify-center">                 
                <payment-method-select      
                  ref="paymentMethodSelect"
                  :invalid="$v.payment_method.name.$error"                  
                  :payment-method="payment_method"
                  :is-edit="isEdit"
                  :payment-methods-module="paymentMethodsModule"
                  @select="onSelectPaymentMethod"
                  @deselect="deselectPaymentMethod"
                  @isSelected="isSelected"
                />
              </div>
              <div v-if="IsSelected" class="flex z-40 justify-center">                   
                <div class="flex flex-wrap p-5 pr-6">
                  <div v-for="(pos_money, index) in pos_money_arr" :key="index">
                    <div>
                        <sw-button 
                          class="button-33 ml-11 mb-8"
                          type="button"
                          variant="primary-outline"
                          @click="addAmount(pos_money)"                       
                        >
                          {{ pos_money.name }}
                      </sw-button>
                    </div>        
                  </div>
                </div>
              </div>
            </td>

            <td class="align-center">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">
                  
                  <div class="relative w-full">
                    <sw-money
                      inputmode="numeric"
                      v-model="amount"
                      :currency="customerCurrency"
                      :invalid="$v.payment_method.amount.$error || error || (errorPmAmount && indexError == index)"
                      onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
                    />
                  </div>     
                  <div v-if="(errorPmAmount && indexError == index)">
                    <span class="text-danger">
                      The invoice amount was exceeded
                    </span>
                  </div>             
                </div>
              </div>
            </td> 

            <td class="align-center">
              <div class="flex items-center justify-start text-sm">
                <div
                  class="
                    flex
                    items-center
                    justify-center
                    w-6
                    h-10
                    mx-2
                    cursor-pointer
                  "
                >               
                  <refresh-icon
                    class="h-12 text-gray-700"
                    @click="clearAmount()"  
                  />                 
                </div>
              </div>
            </td>   

            <td class="align-center">
              <div class="flex items-center justify-center text-sm">
                <div
                  class="
                    flex
                    items-center
                    justify-center
                    w-6
                    h-10
                    mx-2
                    cursor-pointer
                  "
                >               
                  <trash-icon
                    v-if="showTrashIcon"
                    class="h-6 text-gray-700"
                    @click="removePaymentMethod"
                  />                 
                </div>
              </div>
            </td>
          </tr>    
        
          <tr v-if="isOnlyCash" class="tax-tr">
            <td class="px-5 py-0 text-right align-top" style="vertical-align: middle;">                       
            </td>
            <td class="align-center">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">
                  <div class="relative w-full">     
                    <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                      {{ $t('payments.received') }}
                    </label>                   
                  </div>                  
                </div>
              </div>
            </td>             
          </tr>

          <tr v-if="isOnlyCash" class="tax-tr">
            <td class="px-5 py-0 text-right align-top" style="vertical-align: middle;">             
            </td>
            <td class="align-center pt-1">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">
                  <div class="relative w-full">
                    <sw-money
                      inputmode="numeric"
                      v-model="received"
                      :currency="customerCurrency"                      
                      onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
                    />
                  </div>                  
                </div>
              </div>
            </td>             
          </tr>

          <tr v-if="isOnlyCash" class="tax-tr">
              <td class="px-5 py-0 text-right align-top pt-4 py-3" style="vertical-align: middle;">               
              </td>
              <td class="align-center pt-3 py-0">
                <div class="flex flex-col">
                  <div class="flex-auto flex-fill bd-highlight">
                    <div class="relative w-full">
                      <label class="text-sm font-semibold leading-5 text-gray-500 uppercase">
                        {{ $t('payments.returned') }}
                      </label>
                    </div>                  
                  </div>
                </div>
              </td>             
          </tr>

          <tr v-if="isOnlyCash" class="tax-tr">
              <td class="px-5 py-0 text-right align-top pt-4 py-3" style="vertical-align: middle;">               
              </td>
              <td class="align-center pt-1 pb-3">
                <div class="flex flex-col">
                  <div class="flex-auto flex-fill bd-highlight">
                    <div class="relative w-full">
                      <div 
                        class=" min-h-10 block pt-1.5 pr-10 pb-0 pl-2 rounded border border-solid text-sm  border-gray-300 bg-white bg-gray-200 text-gray-600 min-h-10 block pt-1.5 pr-10 pb-0 pl-2 rounded border border-solid text-sm"
                        v-html="$utils.formatMoney(returned, currency)" 
                      />
                    </div>                  
                  </div>
                </div>
              </td>             
          </tr>
                  
        </tbody>
      </table>
    </td>
  </tr>
</template>

<script>
import Guid from 'guid'
import { mapActions, mapGetters } from 'vuex'
import TaxStub from '../../../stub/tax'
import InvoiceStub from '../../../stub/paymentMethod'
import { TrashIcon, ViewGridIcon, ChevronDownIcon, RefreshIcon } from '@vue-hero-icons/solid'
import DragIcon from '@/components/icon/DragIcon'
import PaymentMethodSelect from './PaymentMethodSelect.vue'
import PaymentMethodStub from '../../../stub/paymentMethod'

const {
  required,
  numeric,
  minValue,
  between,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
    components: {
    TrashIcon,
    ViewGridIcon,
    ChevronDownIcon,
    DragIcon,
    PaymentMethodSelect,
    RefreshIcon
  },
  props: {
    index:{
      type: Number,
      default: null,
    },
    indexError:{
      type: Number,
      default: 0,
    },
    errorPmAmount:{
      type: Boolean,
      default: false
    },
    paymentMethodData: {
      type: Object,
      default: null,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
    paymentsMethods:{
      type: Array,
      default: null,
    },
    isEdit:{
      type: Boolean,
      default: false
    },
    paymentMethodsModule:{
      type: Array,
      default: []
    },
    invoiceAmount:{
      type: Number,
      default: 0,
    },
    paymentAmount:{
      type: Number,
      default: 0,
    }
  },
  data(){
    return{
        payment_method: { ...this.paymentMethodData },
        paymentMethodSelect: null,
        IsSelected: false,
        error: false,
        isOnlyCash: false,
        pos_money_arr: []
    }
  },
  created(){    
    window.hub.$on('checkItems', this.validateItem)
  },
  watch: {
    payment_method: {
      handler: 'updatePaymentMethod',
      deep: true,
    },    
  },
  validations() {
    return {
        payment_method: {
            name: {
                required,
            },
            amount: {
                required,
                between: between(1, this.invoiceAmount),
            },
        },
    }
  },
  computed: {

    showTrashIcon() {     
      if (this.paymentsMethods.length > 1) {
        return true
      }
      return false     
    },

    customerCurrency() {
      if (this.currency) {
        return {
          decimal: this.currency.decimal_separator,
          thousands: this.currency.thousand_separator,
          prefix: this.currency.symbol + ' ',
          precision: this.currency.precision,
          masked: false,
        }
      } else {
        return this.defaultCurrenctForInput
      }
    },
    amount: {
      get: function () {
        if (parseFloat(this.payment_method.amount) > 0) {
          return this.payment_method.amount / 100
        }

        return this.payment_method.amount
      },
      set: function (newValue) {
        if (parseFloat(newValue) > 0) {
          this.payment_method.amount = Math.round(newValue * 100)
        } else {
          this.payment_method.amount = newValue
        }
      },
    },

    result(){
      if((this.invoiceAmount - this.paymentAmount) < 0) {
        this.error = true        
        return 0
      }
      this.error = false
      return this.invoiceAmount - this.paymentAmount
    },
    //
    received: {
      get: function () {
        if (parseFloat(this.payment_method.received) > 0) {
          return this.payment_method.received / 100
        }

        return this.payment_method.received
      },
      set: function (newValue) {
        if (parseFloat(newValue) > 0) {
          this.payment_method.received = Math.round(newValue * 100)
        } else {
          this.payment_method.received = newValue
        }
      },
    },
    //
    returned() {
      let returned = this.received - this.amount
      return returned > 0 ? (returned * 100) : 0      
    },    
  },
  methods: {

    addAmount(val){
      this.payment_method.amount += (val.amount * 100)
    },

    clearAmount(){
      this.payment_method.amount = 0
    },

    isSelected(value){
      this.IsSelected = value
    },

    deselectPaymentMethod() {
      this.isOnlyCash = false

      this.payment_method = {
        ...PaymentMethodStub,
        id_raw: this.payment_method.id_raw,
        amount: this.payment_method.amount
      }
      
      this.$nextTick(() => {
        //this.$refs.paymentMethodSelect.$refs.baseSelect.$refs.search.focus()
      })
      
    },

    onSelectPaymentMethod(val) {
      this.payment_method.id = val.id
      this.payment_method.name = val.name
      this.pos_money_arr = val.pos_money

      this.isOnlyCash = val.only_cash == 1 ? true : false 
           
      if (this.index == 0 && this.paymentsMethods.length == 1)
      {
        this.payment_method.amount = this.invoiceAmount
      }else{
        if(this.payment_method.amount != 0)
        {
          this.payment_method.amount = this.payment_method.amount
        }else{
          this.payment_method.amount = this.result
        }        
      }
      if(this.error){
        window.toastr['error']('The amount to be paid exceeds the invoice total')
      } 
    },
    
    updatePaymentMethod() {   
      this.$emit('update', {
        index: this.index,
        payment_method: {
          ...this.payment_method,
          returned: this.returned
        },
      })      
    },

    validateItem() {
      this.$v.payment_method.$touch()
      if (this.payment_method !== null) {
        this.$emit('paymentMethodValidate', this.index, !this.$v.$invalid)
      } else {
        this.$emit('paymentMethodValidate', this.index, false)
      }
    },

    removePaymentMethod() {
      this.$emit('remove', this.index)
    },
  }
}

</script>

<style>
/* CSS */
  .button-33 {
    background-color: #0de062;
    border-radius: 100px;
    box-shadow: rgba(44, 187, 99, .2) 0 -25px 18px -14px inset,rgba(44, 187, 99, .15) 0 1px 2px,rgba(44, 187, 99, .15) 0 2px 4px,rgba(44, 187, 99, .15) 0 4px 8px,rgba(44, 187, 99, .15) 0 8px 16px,rgba(44, 187, 99, .15) 0 16px 32px;
    color: #3c4043;
    cursor: pointer;
    display: inline-block;
    font-family: CerebriSans-Regular,-apple-system,system-ui,Roboto,sans-serif;
    padding: 12px 22px;
    text-align: center;
    text-decoration: none;
    transition: all 250ms;
    border: 0;
    font-size: 16px;
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
  }
  .button-33:hover {
    background: rgba(0, 245, 86, 0.7);
    color: #3c4043;
    transform: scale(1.075);
  }
  .button-33:active {
    box-shadow: 0 4px 4px 0 rgb(60 64 67 / 30%), 0 8px 12px 6px rgb(60 64 67 / 15%);
    outline: none;
  }
  .button-33:not(:disabled) {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
  }
  .button-33:not(:disabled):hover {
    box-shadow: rgba(60, 64, 67, .3) 0 2px 3px 0, rgba(60, 64, 67, .15) 0 6px 10px 4px;
  }
  .button-33:not(:disabled):focus {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
  }
  .button-33:not(:disabled):active {
    box-shadow: rgba(60, 64, 67, .3) 0 4px 4px 0, rgba(60, 64, 67, .15) 0 8px 12px 6px;
  }
  .button-33:disabled {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
  }
</style>