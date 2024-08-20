<template>
  <!--
    <div class="flex-1 text-sm">
      <div
        v-if="paymentMethod.name"
        class="relative flex items-center h-10 pl-2 bg-gray-100 border border-gray-200 border-solid rounded"
      >
        {{ paymentMethod.name }}
  
        <span
          class="absolute text-gray-400 cursor-pointer"
          style="top: 8px; right: 10px"
          @click="deselectPaymentMethod()"
        >
          <x-circle-icon class="h-5" />
        </span>
      </div>
      <sw-select
        v-else
        ref="baseSelect"
        v-model="paymentMethodSelect"
        :options="paymentMethodsOptions"        
        :show-labels="false"
        :preserve-search="true"
        :initial-search="paymentMethod.name"        
        :invalid="invalid"
        :placeholder="$t('payments.select_a_payment_method')"
        label="name"
        class="multi-select-item"
        @value="onTextChange"
        @select="onSelect"
      >       
      </sw-select>      
    </div>
  -->
  <div class="flex flex-wrap p-3">        
      <div v-for="(payment_method, index) in paymentMethodsOptions" :key="index">
        <sw-button 
          ref="baseSelect"
          :invalid="invalid"
          type="button"
          class="mr-2 button-styles"
          :class="{
                    'disabled': isOptionSelected && (option_id_selected != null &&
                                                           option_id_selected != payment_method.id),
                    'ml-2 mb-5': !isOptionSelected,
                    //
                    'isSelected ml-11 mb-0': isOptionSelected && (option_id_selected != null &&
                                                           option_id_selected == payment_method.id),
                  }"
          variant="primary-outline"
          @click="onSelect(payment_method)"
          :disabled="isOptionSelected && (option_id_selected != null && option_id_selected != payment_method.id)"
        >
          {{ payment_method.name }}
        </sw-button>
      </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon, ShoppingCartIcon } from '@vue-hero-icons/solid'
const _ = require('lodash')

export default {
  components: {
    XCircleIcon,
    ShoppingCartIcon,
  },
  props: {
    paymentMethod: {
      type: Object,
      required: true,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
    isEdit:{
      type: Boolean,
      default: false,
    },
    paymentMethodsModule:{
      type: Array,
      default: []
    }
  },
  data() {
    return {
      paymentMethodSelect: null,
      paymentMethodsOptions: null,
      loading: false,
      //
      isOptionSelected: false,
      option_id_selected: null,
    }
  },
  computed: {
    ...mapGetters('item', ['items']),
    ...mapGetters('payment', ['paymentModes', 'selectedNote']),     
  },
  async created() {
    this.paymentMethodsOptions = this.paymentMethodsModule
    if(this.isEdit && this.paymentMethod)
    {
      this.isOptionSelected = true
      this.option_id_selected = this.paymentMethod.id
    }
  },
  watch: {      
  },
  methods: {
    ...mapActions('paymentMultiple', ['getPaymentMethods']),

    onSelect(val)
    {
      if(!this.isOptionSelected)
      {
        this.$emit('select', val)
        this.$emit('isSelected', true)
      }else{          
        this.$emit('deselect')
        this.$emit('isSelected', false)
      }
      this.isOptionSelected = !this.isOptionSelected
      this.option_id_selected = val.id
    },

  },    
}
</script>

<style scoped>  
  .disabled{
    display: none !important;  
  }
  .isSelected{
    background-color: rgb(235, 235, 228) !important;
  }
  .button-styles {
    align-items: center;
    appearance: none;
    background-color: #fff;
    border-radius: 24px;
    border-style: none;
    box-shadow: rgba(0, 0, 0, .2) 0 3px 5px -1px,rgba(0, 0, 0, .14) 0 6px 10px 0,rgba(0, 0, 0, .12) 0 1px 18px 0;
    box-sizing: border-box;
    color: #3c4043;
    cursor: pointer;
    display: inline-flex;
    fill: currentcolor;
    font-family: "Google Sans",Roboto,Arial,sans-serif;
    font-size: 14px;
    font-weight: 500;
    height: 48px;
    justify-content: center;
    letter-spacing: .25px;
    line-height: normal;
    max-width: 100%;
    overflow: visible;
    padding: 2px 24px;
    position: relative;
    text-align: center;
    text-transform: none;
    transition: box-shadow 280ms cubic-bezier(.4, 0, .2, 1),opacity 15ms linear 30ms,transform 270ms cubic-bezier(0, 0, .2, 1) 0ms;
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    width: auto;
    will-change: transform,opacity;
    z-index: 0;
  }
  .button-styles:hover {
    background: #F6F9FE;
    color: #174ea6;
  }
  .button-styles:active {
    box-shadow: 0 4px 4px 0 rgb(60 64 67 / 30%), 0 8px 12px 6px rgb(60 64 67 / 15%);
    outline: none;
  }
  .button-styles:not(:disabled) {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
  }
  .button-styles:not(:disabled):hover {
    box-shadow: rgba(60, 64, 67, .3) 0 2px 3px 0, rgba(60, 64, 67, .15) 0 6px 10px 4px;
  }
  .button-styles:not(:disabled):focus {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
  }
  .button-styles:not(:disabled):active {
    box-shadow: rgba(60, 64, 67, .3) 0 4px 4px 0, rgba(60, 64, 67, .15) 0 8px 12px 6px;
  }
  .button-styles:disabled {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
  }
</style>
