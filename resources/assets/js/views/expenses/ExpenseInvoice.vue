<template>
  <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
    <td colspan="6" class="p-0 text-left align-top">
      <table class="w-full mb-4">
        <colgroup>
            <col style="width: 15%" />
                  <col style="width: 12.5%" />
                  <col style="width: 35%" />  
                  <col style="width: 12.5%" />     
                  <col style="width: 15%" />
                  <col style="width: 10%" /> 
        </colgroup>
        <tbody>

        <tr>

          <td class="pt-6 pr-4 pb-2 pl-4 align-center">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">                  
                  <div class="relative w-full">
                    <sw-input                
                        v-model="invoice_number"
                        v-on:blur="validateProviderAndInvoiceNumber"
                        focus
                        type="text"
                        name="name"                        
                        :invalid="!isValid || (index > 0 && $v.invoice.invoice_number.$invalid)"
                    />
                  </div>  
                </div>
              </div>
          </td>    

          <td class="pt-6 pr-4 pb-2 pl-4 align-center">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">                  
                  <div class="relative w-full">
                    <sw-money
                      inputmode="numeric"
                      v-model="subtotal"
                      :currency="customerCurrency"
                      :invalid="(index > 0 && $v.invoice.subtotal.$invalid)"
                      onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
                    />
                  </div>  
                </div>
              </div>
          </td> 

          <td class="pt-6 pr-4 pb-2 pl-4 align-center">
              <div class="flex flex-col mb-2">
                <div class="flex-auto flex-fill bd-highlight">                  
                  <div class="relative w-full">
                    <sw-select
                        ref="select"
                        v-model="invoice.taxes"
                        :options="taxesOptions"
                        :searchable="true"
                        :show-labels="false"
                        :allow-empty="true"
                        :multiple="true"
                        class="mt-2"
                        track-by="name"
                        label="name"
                        :tabindex="7"
                        @select="updateTotalTaxes()" 
                    />
                  </div>  
                </div>
              </div>
          </td> 

          <td class="pt-6 pr-4 pb-2 pl-4 align-center">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">                  
                  <div class="relative w-full">
                    <sw-money
                      inputmode="numeric"
                      v-model="total_taxes"
                      :currency="customerCurrency"
                      onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
                    />
                  </div>  
                </div>
              </div>
          </td> 

          <td class="pt-6 pr-4 pb-2 pl-4 align-center">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">                  
                  <div class="relative w-full">
                    <div class=" min-h-10 block pt-1.5 pr-10 pb-0 pl-2 rounded border border-solid text-sm  border-gray-300 bg-white bg-gray-200 text-gray-600 min-h-10 block pt-1.5 pr-10 pb-0 pl-2 rounded border border-solid text-sm"
                    v-html="$utils.formatMoney(total, currency)" />
                  </div>  
                </div>
              </div>
          </td> 
          
          <td class="pt-6 pb-2 align-center">
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
                    @click="removeInvoice"
                  />   
                  <refresh-icon
                    v-else
                    class="h-12 text-gray-700"  
                    @click="clearInvoice"             
                  />                   
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
import expenseInvoiceStub from '../../stub/expenseInvoice'
import { TrashIcon, ViewGridIcon, ChevronDownIcon, RefreshIcon } from '@vue-hero-icons/solid'
import DragIcon from '@/components/icon/DragIcon'

const {
  required,
  numeric,
  minValue,
  between,
  maxLength,
  minLength
} = require('vuelidate/lib/validators')

export default {
    components: {
    TrashIcon,
    ViewGridIcon,
    ChevronDownIcon,
    DragIcon,
    RefreshIcon
  },
  props: {  
    index:{
      type: Number,
      default: null,
    },    
    invoiceData: {
      type: Object,
      default: null,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
    taxesOptions: {
      type: Array,
      default: [],
    },
    provider: {
      type: Object,
      default: null,
    },
  },
  data(){
    return{
        invoice: { ...this.invoiceData },
        isValid: true
    }
  },
  created(){    
    //window.hub.$on('checkItems', this.validateItem)
  },
  watch: {
    invoice: {
      handler: 'updateInvoice',
      deep: true,
    },    
    provider: {
      handler: 'validateProviderAndInvoiceNumber',
      deep: true,
    }, 
    subtotal: {
      handler: 'updateTotalTaxes',
      deep: true,
    },     
  },
  validations() {
        return {
            invoice: {
                invoice_number: {
                    required,
                    minLength: minLength(1)
                },
                subtotal: {
                    required,
                    minValue: minValue(1),
                }
            },
        }        
  },
  computed: {

    showTrashIcon() {     
      if (this.index > 0) {
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

    invoice_number: {
      get: function () {       
        return this.invoice.invoice_number
      },
      set: function (newValue) {       
          this.invoice.invoice_number = newValue        
      },
    },

    //
    subtotal: {
      get: function () {
        if (parseFloat(this.invoice.subtotal) > 0)
        {
          return this.invoice.subtotal / 100
        }
        return this.invoice.subtotal
      },
      set: function (newValue) {
        if (parseFloat(newValue) > 0)
        {
            this.invoice.subtotal = Math.round(newValue * 100)
        } else {
            this.invoice.subtotal = newValue
        }
      },
    },

    sum_taxes(){
        return this.invoice.taxes.reduce(function (a, b) {
                    return a + b['percent'] 
               }, 0)
    },

    total_taxes: {
      get: function () {
        if (parseFloat(this.invoice.total_tax) > 0)
        {
          return this.invoice.total_tax / 100
        }
        return this.invoice.total_tax
      },
      set: function (newValue) {
        if (parseFloat(newValue) > 0)
        {
            this.invoice.total_tax = Math.round(newValue * 100)
        } else {
            this.invoice.total_tax = newValue
        }
      },
    },

    /*
    total_taxes(){
        return this.subtotal * this.sum_taxes
        
    },*/ 

    total(){
        return (this.subtotal + this.total_taxes) * 100
    },

  },
  methods: {

    updateTotalTaxes(){
      setTimeout(() => {
        this.total_taxes = (this.subtotal * this.sum_taxes) / 100       
      }, 1);
    },

    async validateProviderAndInvoiceNumber()
    {
        if(this.provider != null && this.invoice_number != "")
        {
            let data = {
                provider_id: this.provider.id,
                invoice_number: this.invoice_number
            }
            
            let response = await window.axios.post('/api/v1/expenses/is-valid-invoice-and-provider', data)

            if(response.data.success)
            {
                if(!response.data.isValid)
                {
                    this.isValid = false
                    window.toastr['error']
                        (`Existing provider and invoice combination:     
                         <br> </br>                    
                         <p> Provider: ${this.provider.title} </p>
                         <p> Invoice number:  ${this.invoice_number} </p>`)
                    return
                }
                this.isValid = true
            }          
        }
    },    
  
    updateInvoice() {           

      this.$emit('update', {
        index: this.index,
        invoice: {
          ...this.invoice,
          total: this.total
        },
      })      
    },

    validateItem() {
      /*this.$v.invoice.$touch()
      if (this.invoice !== null) {
        this.$emit('paymentMethodValidate', this.index, !this.$v.$invalid)
      } else {
        this.$emit('paymentMethodValidate', this.index, false)
      }*/
    },

    removeInvoice() {
      this.$emit('remove', this.index)
    },

    clearInvoice() {
      this.invoice = { ...expenseInvoiceStub }      
    },
  }
}

</script>
