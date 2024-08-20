<template>
  <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
    <td colspan="5" class="p-0 text-left align-top">
      <table class="w-full">
        <colgroup>
          <col style="width: 40%" />
          <col style="width: 10%" />
          <col style="width: 15%" />
          <col v-if="discountPerItem === 'YES'" style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <tbody>
          <tr>
            <td class="px-5 pt-4 text-left align-top" >
              <div class="flex justify-start z-40">
                <div
                  class="
                    flex
                    items-center
                    justify-center
                    w-12
                    h-5
                    mt-2
                    text-gray-400
                    cursor-move
                    handle
                  "
                >
                  <drag-icon />
                </div>
                <item-select
                  ref="itemSelect"
                  :invalid="$v.item.name.$error"
                  :invalid-description="$v.item.description.$error"
                  :item="item"
                  :tax-per-item="taxPerItem"
                  :retentionActive="retentionActive"
                  :taxes="item.taxes"
                  :avalaraIsTaxable="avalaraIsTaxable"
                  @search="searchVal"
                  @select="onSelectItem"
                  @deselect="deselectItem"
                  @onDesriptionInput="$v.item.description.$touch()"
                  @onSelectItem="isSelected = true"
                />
              </div>
            </td>
            <td class="px-5 pt-4 text-right align-top" >
              <sw-input
                v-model="item.quantity"
                :invalid="$v.item.quantity.$error"
                type="number"
                small
                @keyup="updateItem"
                @input="$v.item.quantity.$touch()"
                min="0"
                onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
              />
              <div v-if="$v.item.quantity.$error">
                <span v-if="!$v.item.quantity.maxLength" class="text-danger">
                  {{ $t('validation.quantity_maxlength') }}
                </span>
              </div>
            </td>
            <td class="px-5 pt-4 text-left align-top">
              <div class="flex flex-col">
                <div class="flex-auto flex-fill bd-highlight">
                  <div class="relative w-full">
                    <sw-money
                      v-model="price"
                      :currency="customerCurrency"
                      :invalid="$v.item.price.$error"
                      @input="$v.item.price.$touch()"
                    />
                  </div>
                  <div v-if="$v.item.price.$error">
                    <span v-if="!$v.item.price.maxLength" class="text-danger">
                      {{ $t('validation.price_maxlength') }}
                    </span>
                  </div>
                </div>
              </div>
            </td>
            <td
              v-if="discountPerItem === 'YES'"
              class="px-5 pt-4 text-left align-top" 
            >
              <div class="flex flex-col">
                <div class="flex flex-auto" role="group">
                  <sw-money
                    v-model="discount"
                    :invalid="$v.item.discount_val.$error"
                    class="border-r-0 rounded-tr-none rounded-br-none"
                    @input="$v.item.discount_val.$touch()"
                  />
                  <sw-dropdown>
                    <sw-button
                      slot="activator"
                      type="button"
                      data-toggle="dropdown"
                      size="discount"
                      aria-haspopup="true"
                      aria-expanded="false"
                      style="height: 30px"
                      variant="white"
                    >
                      <span class="flex items-center">
                        {{
                          item.discount_type == 'fixed' ? currency.symbol : '%'
                        }}
                        <chevron-down-icon class="h-5" />
                      </span>
                    </sw-button>

                    <sw-dropdown-item @click="selectFixed">
                      {{ $t('general.fixed') }}
                    </sw-dropdown-item>

                    <sw-dropdown-item @click="selectPercentage">
                      {{ $t('general.percentage') }}
                    </sw-dropdown-item>
                  </sw-dropdown>
                </div>
              </div>
            </td>
            <td class="px-5 pt-4 text-right align-top">
              <div class="flex items-center justify-end text-sm">
                <span>
                  <div v-html="$utils.formatMoney(total, currency)" />
                </span>

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
                <!--  v-if="showRemoveItemIcon"-->
                  <trash-icon
                    v-if="banType"
                    class="h-5 text-gray-700"
                    @click="removeItem"
                  />
                  <trash-icon
                    v-else-if="showRemoveItemIcon"
                    class="h-5 text-gray-700"
                    @click="removeItem"
                  />
                </div>
              </div>
            </td>
          </tr>


          <tr v-if="retentionActive === 'YES'" class="tax-tr">
            <td class="px-5 pt-4 text-right align-top">
              <label>
                {{ $t('settings.retentions.title') }}
              </label>
            </td>
            <td v-if="retentionBool" colspan="4" class="px-5 py-4 text-left align-top">
              <RetentionsItem
                :retentionData="item.retentions"
                :currency="currency"
                :total="total"
                @update="updateRetentions"
                @remove="removeRetentions"
              />
            </td>
          </tr>

          <!---->
            <!---->
              <!---->

          <tr v-if="taxPerItem === 'YES' && !avalaraIsTaxable" class="tax-tr">
            <td class="px-5 py-0 text-right align-top">
              <label>
                {{ $t('general.tax') }}
              </label>
            </td>
            <td colspan="4" class="px-5 py-0 text-left align-top">
              <tax
                v-for="(tax, index) in item.taxes"
                :key="tax.id"
                :index="index"
                :tax-data="tax"
                :taxes="item.taxes"
                :discounted-total="total"
                :total-tax="totalSimpleTax"
                :total="total"
                :currency="currency"
                :button-bool="buttonBool"
                @update="updateTax"
                @remove="removeTax"
              />
            </td>            
          </tr>

          <tr v-if="taxPerItem === 'YES' && avalaraIsTaxable && !buttonBool" class="tax-tr pt-12">
            <td class="px-5 py-0 text-right align-top">
              <label>
                {{ $t('general.tax') }}
              </label>
            </td>
            <td colspan="4" class="px-5 py-0 text-left align-top">
              <tax
                v-for="(tax, index) in item.taxes"
                :key="tax.id"
                :index="index"
                :tax-data="tax"
                :taxes="item.taxes"
                :discounted-total="total"
                :total-tax="totalSimpleTax"
                :total="total"
                :currency="currency"
                :button-bool="buttonBool"
                @update="updateTax"
                @remove="removeTax"
              />
            </td>            
          </tr>         

          <tr v-if="taxPerItem === 'YES' && avalaraIsTaxable && buttonBool" class="tax-tr">
            <td class="px-5 py-0 text-right align-top">
              <label>
                {{ $t('general.tax') }}
              </label>
            </td>
            <td colspan="4" class="px-5 py-0 text-left align-top">
              <tax
                v-for="(tax, index) in item.taxes"
                :key="tax.id"
                :index="index"
                :tax-data="tax"
                :taxes="item.taxes"
                :discounted-total="total"
                :total-tax="totalSimpleTax"
                :total="total"
                :currency="currency"
                :button-bool="buttonBool"
                @update="updateTax"
                @remove="removeTax"
              />
            </td>            
          </tr>

              <!---->
            <!---->
          <!---->
          
          <tr v-if="avalaraIsTaxable" class="tax-tr">
            <td class="px-2 py-0 text-left align-top" />
            <avalara-tax
              :item="item"
              :discounted-total="total"
              :total-tax="totalTax"
              :total="total"
              :currency="currency"
              :user="user"
              :button-bool="buttonBool"
              :is-edit-avalara="IsEditAvalara"
              :invoice-pbx-service="InvoicePbxService"
              @updatedButtonBool="updatedButtonBoolItemComponent"
              @update="updateAvalaraTax"
            />
          </tr>
        </tbody>
      </table>
    </td>
  </tr>
</template>

<script>
import Guid from 'guid'
import { mapActions, mapGetters } from 'vuex'
import TaxStub from '../../stub/tax'
import InvoiceStub from '../../stub/invoice'
import ItemSelect from './ItemSelect'
import RetentionsItem from './RetentionsItem'
import Tax from './Tax'
import AvalaraTax from './AvalaraTax'
import { TrashIcon, ViewGridIcon, ChevronDownIcon } from '@vue-hero-icons/solid'
import DragIcon from '@/components/icon/DragIcon'
const {
  required,
  numeric,
  minValue,
  between,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    Tax,
    AvalaraTax,
    ItemSelect,
    TrashIcon,
    ViewGridIcon,
    ChevronDownIcon,
    DragIcon,
    RetentionsItem
  },
  props: {
    retentionBool:{
      type: Boolean,
      default: true
    },
    itemData: {
      type: Object,
      default: null,
    },
    index: {
      type: Number,
      default: null,
    },
    type: {
      type: String,
      default: '',
    },
    currency: {
      type: [Object, String],
      required: true,
    },
    taxPerItem: {
      type: String,
      default: '',
    },
    retentionActive: {
      type: String,
      default: '',
    },
    avalaraIsTaxable: {
      type: Boolean,
      default: false,
    },
    banType: {
      type: Number,
      default: null,
    },
    discountPerItem: {
      type: String,
      default: '',
    },
    invoiceItems: {
      type: Array,
      default: null,
    },
    user: {
      type: Object,
      default: null,
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
      isClosePopup: false,
      itemSelect: null,
      item: { ...this.itemData },
      maxDiscount: 0,
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      isSelected: false,
      totalAvalaraTax: 0,
      avalaraTaxes: [],
    }
  },
  computed: {
    ...mapGetters('item', ['items']),
    ...mapGetters('modal', ['modalActive']),
    ...mapGetters('company', ['defaultCurrencyForInput']),
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
    showRemoveItemIcon() {
      if (this.invoiceItems.length == 1) {
        return false
      }
      return true
    },
    subtotal() {
      return this.item.price * this.item.quantity
    },
    discount: {
      get: function () {
        return this.item.discount
      },
      set: function (newValue) {
        if (this.item.discount_type === 'percentage') {
          this.item.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.item.discount_val = Math.round(newValue * 100)
        }

        this.item.discount = newValue
      },
    },
    total() {
      if (this.discountPerItem === 'YES') {
        return this.subtotal - this.item.discount_val
      } else {
        return this.subtotal
      }
    },
    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.item.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },
    totalCompoundTax() {
      return Math.round(
        window._.sumBy(this.item.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },
    totalTax() {
      return this.totalSimpleTax + this.totalCompoundTax + this.totalAvalaraTax
    },
    price: {
      get: function () {
        if (parseFloat(this.item.price) > 0) {
          return this.item.price / 100
        }

        return this.item.price
      },
      set: function (newValue) {
        if (parseFloat(newValue) > 0) {
          this.item.price = Math.round(newValue * 100)
          this.maxDiscount = this.item.price
        } else {
          this.item.price = newValue
        }
      },
    },
  },
  watch: {
    item: {
      handler: 'updateItem',
      deep: true,
    },
    subtotal(newValue) {
      if (this.item.discount_type === 'percentage') {
        this.item.discount_val = (this.item.discount * newValue) / 100
      }
    },
    modalActive(val) {
      if (!val) {
        this.isSelected = false
      }
    },
  },
  validations() {
    return {
      item: {
        name: {
          required,
        },
        quantity: {
          required,
          minValue: minValue(0),
          maxLength: maxLength(20),
        },
        price: {
          required,
          minValue: minValue(1),
          maxLength: maxLength(20),
        },
        discount_val: {
          between: between(0, this.maxDiscount),
        },
        description: {
          maxLength: maxLength(65000),
        },
      },
    }
  },
  mounted() {
    this.$v.item.$reset()
  },
  created() {
    window.hub.$on('checkItems', this.validateItem)
    window.hub.$on('newItem', (val) => {
      if (this.taxPerItem === 'YES') {
        this.item.taxes = val.taxes
      }
      if (!this.item.item_id && this.modalActive && this.isSelected) {
        this.onSelectItem(val)
      }
    })
  },
  methods: {
    updatedButtonBoolItemComponent(data){
      this.$emit('updatedButtonBool', data);      
    },
    updateRetentions(retentions){
      this.item.retentions = retentions
    },
    removeRetentions(){
      this.item.retentions = {}
    },
    updateTax(data) {
      this.$set(this.item.taxes, data.index, data.item)

      let lastTax = this.item.taxes[this.item.taxes.length - 1]

      if (lastTax.tax_type_id !== 0) {
        this.item.taxes.push({ ...TaxStub, id: Guid.raw() })
      }

      this.updateItem()
    },
    updateAvalaraTax(data) {
      this.totalAvalaraTax = data.total * 100
      this.avalaraTaxes = data.taxes
      this.updateItem()
    },
    removeTax(index) {
      this.item.taxes.splice(index, 1)

      this.updateItem()
    },
    taxWithPercentage({ name, percent }) {
      return `${name} (${percent}%)`
    },
    avalaraTotalUpdate(data) {
      this.totalAvalaraTax = data.total
      this.avalaraTaxes = data.taxes
    },
    searchVal(val) {
      this.item.name = val
    },
    deselectItem() {
      this.item = {
        ...InvoiceStub,
        id: this.item.id,
        taxes: [{ ...TaxStub, id: Guid.raw() }],
        retentions: {},
      }
      this.$nextTick(() => {
        this.$refs.itemSelect.$refs.baseSelect.$refs.search.focus()
      })
    },
    onSelectItem(item) {
      this.item.name = item.name
      this.item.price = item.price
      this.item.item_id = item.id
      this.item.description = item.description
      this.item.unit_name = item.unit_name

      if (this.taxPerItem === 'YES' && item.taxes) {
        let index = 0
        item.taxes.forEach((tax) => {
          this.updateTax({ index, item: { ...tax } })
          index++
        })
      }
      if (this.retentionActive === 'YES') {     
        this.item.retentions = item.retentions
      }
    },
    selectFixed() {
      if (this.item.discount_type === 'fixed') {
        return
      }

      this.item.discount_val = Math.round(this.item.discount * 100)
      this.item.discount_type = 'fixed'
    },
    selectPercentage() {
      if (this.item.discount_type === 'percentage') {
        return
      }

      this.item.discount_val = (this.subtotal * this.item.discount) / 100

      this.item.discount_type = 'percentage'
    },
    updateItem() {
      this.$emit('update', {
        index: this.index,
        item: {
          ...this.item,
          total: this.total,
          totalSimpleTax: this.totalSimpleTax,
          totalCompoundTax: this.totalCompoundTax,
          totalTax: this.totalTax,
          tax: this.totalTax,
          totalRetention: this.totalRetention,
          avalaraTax: this.totalAvalaraTax,
          avalaraTaxes: [...this.avalaraTaxes],
          taxes: [...this.item.taxes],
          retentions: this.item.retentions ? { ...this.item.retentions } : {},
        },
      })
    },
    removeItem() {
      this.$emit('remove', this.index)
    },
    validateItem() {
      this.$v.item.$touch()

      if (this.item !== null) {
        this.$emit('itemValidate', this.index, !this.$v.$invalid)
      } else {
        this.$emit('itemValidate', this.index, false)
      }
    },
  },
}
</script>