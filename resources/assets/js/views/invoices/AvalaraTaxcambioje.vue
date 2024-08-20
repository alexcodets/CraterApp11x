<template>

  <td v-if="taxes.length && buttonBool" colspan="4" class="px-2 pb-2 pt-1 text-left align-top">
    <avalara-tax-item
      v-for="tax in taxes"
      :key="tax.id"
      :currency="currency"
      :tax-data="tax"
    />
  </td> 

  <!--
  <td v-else-if="taxes.length && !buttonBool && IsEditAvalara" colspan="4" class="px-2 pb-2 pt-1 text-left align-top">
    <avalara-tax-item
      v-for="tax in taxes"
      :key="tax.id"
      :currency="currency"
      :tax-data="tax"
    />
  </td> 
  -->

  <td v-else>   
    <p class="mb-10 py-10"></p>    
  </td> 

</template>

<script>
import { CheckCircleIcon, TrashIcon } from '@vue-hero-icons/solid'
import avalaraTaxItem from './AvalaraTaxItem.vue'
import { mapActions } from 'vuex'

export default {
  components: {
    CheckCircleIcon,
    TrashIcon,
    avalaraTaxItem,
  },
  props: {
    item: {
      type: Object,
      default: null,
    },
    total: {
      type: Number,
      default: 0,
    },
    user: {
      type: Object,
      default: null,
    },
    totalTax: {
      type: Number,
      default: 0,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
    //
    buttonBool: {
      type: Boolean,
      default: false,
    },  
    IsEditAvalara: {
      type: Boolean,
      default: false,
    },      
    //
    InvoicePbxService:{
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      tax: { ...this.taxData },
      selectedTax: null,
      taxes: [],
      taxes_empty_or_null : [
          {
            name : "No Avalara taxes were generated for this item",
            tax : 0.00
          },
        ]
    }
  },
  watch: {
    total: {
      handler: 'updateItemTaxes',
    },
    user: {
      handler: 'updateItemTaxes',
    },
    item: {
      handler: 'updateItemTaxes',
    },
  },
  created() {},
  computed: {
    totalTaxes() {
      return this.taxes.reduce(function (accumulator, currentValue) {
        return accumulator + parseFloat(currentValue.tax)
      }, 0)
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('avalara', ['fetchAvalaraItemTaxes']),
    customLabel({ name, percent }) {
      return `${name} - ${percent}%`
    },
    updateItemTaxes() {    

    if(!this.InvoicePbxService)
    {
      if(!this.IsEditAvalara){

        if (this.total <= 0 || this.user == null)
        {
          this.taxes = []
          this.$emit('updatedButtonBool', false)
          return
        }

      if(this.item.item_id == null)
      {       
        this.taxes = [
          {
            name : "Avalara taxes are not applicable to this item",
            tax : 0.00
          },
        ]   
        this.$emit('updatedButtonBool', false)
        return
      }

      this.isLoading = true

      let data = {
        data: {
          quantity: this.item.quantity,
          price: this.item.price *this.item.quantity,
        },
        id: this.item.item_id || this.item.items_id,
        user_id: this.user.id,
      }

      this.$emit('updatedButtonBool', false)

        this.fetchAvalaraItemTaxes(data)
        .then((res) => {

          this.isLoading = false
          
          if (res.data.success)
          {
            if(res.data.data.items[0].txs.length != 0)
            {
              this.taxes = res.data.data.items[0].txs
            }else
            {
              this.taxes = this.taxes_empty_or_null 
            }                       
          }         

          if (!res.data.success)
          {
            this.taxes = this.taxes_empty_or_null   
            //console.log(res.data.message, res.data.errors)
            //window.toastr['error'](res.data.message, res.data.status)            
          }

          this.$emit('update', { total: this.totalTaxes, taxes: this.taxes })
          
        })
        .catch((err) => {
          this.isLoading = false
        })
      }

      if(this.IsEditAvalara){

        if (this.total <= 0 || this.user == null)
        {
          this.taxes = []
          //this.$emit('updatedButtonBool', false)
          return
        }

      if(this.item.item_id == null)
      {       
        this.taxes = [
          {
            name : "Avalara taxes are not applicable to this item",
            tax : 0.00
          },
        ]   
        //this.$emit('updatedButtonBool', false)
        return
      }

      this.isLoading = true

      let data = {
        data: {
          quantity: this.item.quantity,
          price: this.item.price *this.item.quantity,
        },
        id: this.item.item_id || this.item.items_id,
        user_id: this.user.id,
      }

      //this.$emit('updatedButtonBool', false)

        this.fetchAvalaraItemTaxes(data)
        .then((res) => {

          this.isLoading = false
          
          if (res.data.success)
          {
            if(res.data.data.items[0].txs.length != 0)
            {
              this.taxes = res.data.data.items[0].txs
            }else
            {
              this.taxes = this.taxes_empty_or_null 
            }                       
          }         

          if (!res.data.success)
          {
            this.taxes = this.taxes_empty_or_null   
            //console.log(res.data.message, res.data.errors)
            //window.toastr['error'](res.data.message, res.data.status)            
          }

          this.$emit('update', { total: this.totalTaxes, taxes: this.taxes })
          
        })
        .catch((err) => {
          this.isLoading = false
        })
      }
    }
    
    },
  },
}
</script>