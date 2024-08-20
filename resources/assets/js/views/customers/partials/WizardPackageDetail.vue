
<template>
  <div
    class="w-full mb-8 bg-white  border border-gray-200 border-solid rounded p-8 relative package-details"
  >
    <div class="heading-section">
      <p class="text-2xl not-italic font-semibold leading-7 text-black">
        {{ $t('customers.package_details') }}
      </p>
    </div>

    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

    <!--------------------------- ITEMS --------------------------->

    <div class="mt-8">
      <table class="w-full text-center item-table">
        <colgroup>
          <col style="width: 40%" />
          <col style="width: 10%" />
          <col style="width: 15%" />
          <col v-if="discountPerItem === 'YES'" style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <thead class="bg-white border border-gray-200 border-solid">
          <tr>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span class="pl-12">
                {{ $tc('items.item', 2) }}
              </span>
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              {{ $t('invoices.item.quantity') }}
            </th>
            <th
              class=" px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              {{ $t('invoices.item.price') }}
            </th>
            <th
              v-if="discountPerItem === 'YES'"
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              {{ $t('invoices.item.discount') }}
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span class="pr-10">
                {{ $t('invoices.item.total') }}
              </span>
            </th>
          </tr>
        </thead>

        <draggable
          v-model="packageCustomer.items"
          class="item-body"
          tag="tbody"
          handle=".handle"
        >
          <package-item
            v-for="(item, index) in packageCustomer.items"
            :key="item.id"
            :index="index"
            :item-data="item"
            :invoice-items="packageCustomer.items"
            :currency="currency"
            :tax-per-item="taxPerItem"
            :discount-per-item="discountPerItem"
            @remove="removeItem"
            @update="updateItem"
            @itemValidate="checkItemsData"
          />
        </draggable>
      </table>
    </div>

    <div
      class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
      @click="addItem"
    >
      <shopping-cart-icon class="h-5 mr-2" />
      {{ $t('invoices.add_item') }}
    </div>

    <!------------------------ TOTALS -------------------------->

    <div
      class="block my-10 invoice-foot lg:justify-between lg:flex lg:items-start"
    >
      <div class="w-full lg:w-1/2"></div>

      <div
        class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded invoice-total lg:mt-0"
      >
        <!----------- SUB TOTAL ----------->
        <div class="flex items-center justify-between w-full">
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
          >
            {{ $t('invoices.sub_total') }}
          </label>
          <label
            class="flex items-center justify-center m-0 text-lg text-black uppercase"
          >
            <div v-html="$utils.formatMoney(subtotal, currency)" />
          </label>
        </div>

        <!-------------- TAXES ------------->
        <div
          v-for="tax in allTaxes"
          :key="tax.tax_type_id"
          class="flex items-center justify-between w-full"
        >
          <label
            class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ tax.name }} - {{ tax.percent }}%
          </label>
          <label
            class="flex items-center justify-center m-0 text-lg text-black uppercase"
            style="font-size: 18px"
          >
            <div v-html="$utils.formatMoney(tax.amount, currency)" />
          </label>
        </div>

        <!------------- DISCOUNT ----------->
        <div
          v-if="(discountPerItem === 'NO' || discountPerItem === null) && parameters.allow_discount"
          class="flex items-center justify-between w-full mt-2"
        >
        
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >{{ $t('invoices.discount') }}</label
          >
          <div class="flex" style="width: 105px" role="group">
            <sw-input
              v-model="discount"
              :invalid="$v.packageCustomer.discount_val.$error"
              class="border-r-0 rounded-tr-sm rounded-br-sm"
              @input="$v.packageCustomer.discount_val.$touch()"
            />
            <sw-dropdown position="bottom-end">
              <sw-button
                slot="activator"
                type="button"
                data-toggle="dropdown"
                size="discount"
                aria-haspopup="true"
                aria-expanded="false"
                style="height: 43px"
                variant="white"
              >
                <span class="flex">
                  {{
                    packageCustomer.discount_type === 'fixed'
                      ? currency.symbol
                      : '%'
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

        <!------------- ADD TAXES ----------->
        <div v-if="taxPerItem ? 'NO' : null">
          <tax
            v-for="(tax, index) in packageCustomer.taxes"
            :index="index"
            :total="subtotalWithDiscount"
            :key="tax.id"
            :tax="tax"
            :taxes="packageCustomer.taxes"
            :currency="currency"
            :total-tax="totalSimpleTax"
            @remove="removeInvoiceTax"
            @update="updateTax"
          />
        </div>

        <sw-popup
          v-if="(taxPerItem === 'NO' || taxPerItem === null) && parameters.tax_type.value === 'G'"
          ref="taxModal"
          class="my-3 text-sm font-semibold leading-5 text-primary-400"
        >
          <div slot="activator" class="float-right pt-2 pb-5">
            + {{ $t('invoices.add_tax') }}
          </div>
          <tax-select-popup
            :taxes="packageCustomer.taxes"
            @select="onSelectTax"
          />
        </sw-popup>

        <!-------------- TOTAL -------------->
        <div
          class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid"
        >
          <label
            class="text-sm font-semibold leading-5 text-gray-500 uppercase"
          >
            {{ $t('invoices.total') }} {{ $t('invoices.amount') }}:
          </label>
          <label
            class="flex items-center justify-center text-lg uppercase text-primary-400"
          >
            <div v-html="$utils.formatMoney(total, currency)" />
          </label>
        </div>
      </div>
    </div>

    <div>
      <sw-button
        :disabled="isLoading"
        variant="primary-outline"
        class="flex justify-center w-auto align-bottom"
        @click="back"
      >
        <arrow-left-icon class="h-5 mr-2 -ml-1" />
        {{ $t('general.back') }}
      </sw-button>

      <sw-button
        :loading="isLoading"
        variant="primary"
        class="flex justify-center w-auto align-bottom ml-4"
        @click="next"
      >
        {{ $t('general.continue') }}
        <arrow-right-icon class="h-5 ml-2 -mr-1" />
      </sw-button>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import PackageItem from '../Item'
import Tax from '../PackageTax'
import PackageStub from '../../../stub/customerPackage'
import TaxStub from '../../../stub/tax'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
  ArrowRightIcon,
  ArrowLeftIcon,
   TrashIcon,
} from '@vue-hero-icons/solid'
import { PlusSmIcon } from '@vue-hero-icons/outline'
import draggable from 'vuedraggable'
import moment from 'moment'
import Guid from 'guid'

const {
  required,
  between,
  maxLength,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  components: {
    PackageItem,
    Tax,
    draggable,
    PlusSmIcon,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
    ArrowRightIcon,
    ArrowLeftIcon,
  },
  data() {
    return {
      packageCustomer: {
        sub_total: null,
        total: null,
        tax: null,
        discount_type: 'fixed',
        discount_val: 0,
        discount: 0,
        items: [
          {
            ...PackageStub,
            id: Guid.raw(),
            end_period_act: false,
        end_period_number: 1,
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        taxes: [],
      },
      selectedCurrency: '',
      taxPerItem: null,
      discountPerItem: null,
      package: '',
      parameters: '',
      isRequestOnGoing: false,
      isLoading: false,
    }
  },
  validations() {
    return {
      packageCustomer: {
        discount_val: {
          between: between(0, this.subtotal),
        },
        items:{
          required
        }
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters('customer', ['packageParameters']),

    currency() {
      return this.selectedCurrency
    },

    subtotal() {
      return this.packageCustomer.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },

    allTaxes() {
      let taxes = []

      this.packageCustomer.items.forEach((item) => {
        item.taxes.forEach((tax) => {
          let found = taxes.find((_tax) => {
            return _tax.tax_type_id === tax.tax_type_id
          })

          if (found) {
            found.amount += tax.amount
          } else if (tax.tax_type_id) {
            taxes.push({
              tax_type_id: tax.tax_type_id,
              amount: tax.amount,
              percent: tax.percent,
              name: tax.name,
            })
          }
        })
      })

      return taxes
    },

    discount: {
      get: function () {
        return this.packageCustomer.discount
      },
      set: function (newValue) {
        if (this.packageCustomer.discount_type === 'percentage') {
          this.packageCustomer.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.packageCustomer.discount_val = Math.round(newValue * 100)
        }

        this.packageCustomer.discount = newValue
      },
    },

    subtotalWithDiscount() {
      return this.subtotal - this.packageCustomer.discount_val
    },

    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.packageCustomer.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },

    totalCompoundTax() {
      return Math.round(
        window._.sumBy(this.packageCustomer.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },

    totalTax() {
     // console.log("totaltax");
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
       // console.log("taxpertiem no");
        return this.totalSimpleTax + this.totalCompoundTax
      }

     // console.log("taxpertiem si");
      //console.log(this.packageCustomer.items);
      return Math.round(
        window._.sumBy(this.packageCustomer.items, function (item) {
          return item.tax
        })
      )
    },

    total() {
      //console.log("total")
    //  console.log(this.totalTax)
      return this.subtotalWithDiscount + this.totalTax
    },

    isEdit() {
      if (this.$route.name === 'services.edit') {
        return true
      }
      return false
    },
  },
  watch: {
    subtotal(newValue) {
      if (this.packageCustomer.discount_type === 'percentage') {
        this.packageCustomer.discount_val =
          (this.packageCustomer.discount * newValue) / 100
      }
    },
  },
  created() {
    this.loadPackage()
    window.hub.$on('newTax', this.onSelectTax)
  },
  methods: {
    ...mapActions('pack', ['fetchPackage']),
    ...mapActions('item', ['fetchItems']),
    ...mapActions('taxType', ['fetchTaxTypes']),
    ...mapActions('customer', ['setPackageParameters']),
    ...mapActions('service', ['fetchService']),

    async loadPackage() {
      this.isRequestOnGoing = true
      this.parameters = this.packageParameters.parameters
      await this.fetchTaxTypes({ limit: 'all' })

      this.taxPerItem = this.parameters.tax_type.value === 'I' ? 'YES' : 'NO'
      this.discountPerItem = this.parameters.discount_type.value === 'I' ? 'YES' : 'NO'

      if (this.isEdit) {
        let res = await this.fetchService(this.$route.params.customer_package_id)
       // console.log(res)
        let service = res.data.service
        this.packageCustomer = { ...service }

        // Un servicio creado SIN descuento pero luego se edita CON descuento
        if (!service.allow_discount && this.parameters.allow_discount) {
          let response = await this.fetchPackage(this.parameters.package.value)
          let pack_data = response.data.response
          if (this.parameters.discount_type.value === 'G') {
            this.packageCustomer.discount_type = pack_data.discounts.value
            this.discount = pack_data.discount
          }
        }

        // Un servicio creado CON descuento pero luego se edita SIN descuento
        if (!this.parameters.allow_discount) {
          this.packageCustomer.discount_type = 'fixed'
          this.discount = 0
        }

        this.packageCustomer.items = []
        service.items.forEach((item) => {
          if (item.taxes.length === 0 && this.taxPerItem === 'YES') {
            this.packageCustomer.items.push({
              ...item,
              id: Guid.raw(),
              
              taxes: [{ ...TaxStub, id: Guid.raw() }],
            })
          } else {
            this.packageCustomer.items.push({ ...item })
          }
        })      

        this.packageCustomer.taxes = []
        if (this.parameters.tax_type.value === 'G') {
          service.taxes.forEach((_tax) => {
            this.onSelectTax(_tax)
          })
        }
      } else {
        let response = await this.fetchPackage(this.parameters.package.value)
       
        this.package = response.data.response
        let itemsArray = []
        this.package.items.forEach((item) => {

          let obj = {}
          obj.taxes = [{ ...TaxStub, id: Guid.raw() }]
         
          if (item.taxes.length > 0 && this.taxPerItem === 'YES') {
            obj.taxes = item.taxes
          }

          obj.package_id = item.package_id
          obj.item_id = item.items_id
          obj.name = item.name
          obj.description = item.description
          obj.quantity = parseInt(item.quantity)
          obj.price = item.price
          obj.discount_type = item.discount_type
          obj.discount_val = item.discount_val
          obj.discount = this.discountPerItem === 'YES' ? item.discount : 0
          obj.total = item.total
          obj.tax = item.tax // Aca deberia ir el tax del pivote pero no lo estan registrando
          obj.totalTax = 0
          obj.totalSimpleTax = 0
          obj.totalCompoundTax = 0
          
          obj.end_period_act = item.end_period_act
          obj.end_period_number = item.end_period_number
          itemsArray.push(obj)
        })
        this.packageCustomer.items = itemsArray

        if (this.parameters.tax_type.value === 'G') {
          this.package.tax_types.forEach((_tax) => {
            this.onSelectTax(_tax)
          })
        }

        if (
          this.parameters.discount_type.value === 'G' &&
          this.parameters.allow_discount
        ) {
          this.discount = this.package.discount
          this.packageCustomer.discount_type = this.package.discounts.value
        }
      }

      this.selectedCurrency = this.defaultCurrency
      await this.fetchItems({
        filter: {},
        orderByField: '',
        orderBy: '',
        limit:100000000000000 ,
      })
      this.isRequestOnGoing = false
    },

    async back() {
      await this.$emit('back')
      this.package = ''
    },

    addItem() {
      this.packageCustomer.items.push({
        ...PackageStub,
        id: Guid.raw(),
        end_period_act: false,
        end_period_number: 1,
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
      this.searchItems()
    },

    async searchItems() {
      let data = {
        filter: {
          name: '',
          unit: '',
          price: '',
        },
        orderByField: '',
        orderBy: '',
       
        limit: 1000000,
      }
      await this.fetchItems(data)
    },

    removeItem(index) {
      this.packageCustomer.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.packageCustomer.items[data.index], { ...data.item })
    },

    checkItemsData(index, isValid) {
      this.packageCustomer.items[index].valid = isValid
    },

    selectFixed() {
      if (this.packageCustomer.discount_type === 'fixed') {
        return
      }

      this.packageCustomer.discount_val = Math.round(
        this.packageCustomer.discount * 100
      )
      this.packageCustomer.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.packageCustomer.discount_type === 'percentage') {
        return
      }

      this.packageCustomer.discount_val =
        (this.subtotal * this.packageCustomer.discount) / 100

      this.packageCustomer.discount_type = 'percentage'
    },

    removeInvoiceTax(index) {
      this.packageCustomer.taxes.splice(index, 1)
    },

    updateTax(data) {
      Object.assign(this.packageCustomer.taxes[data.index], { ...data.item })
    },

    onSelectTax(selectedTax) {
      let amount = 0
      
      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = Math.round(
          ((this.subtotalWithDiscount + this.totalSimpleTax) *
            selectedTax.percent) /
            100
        )
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = Math.round(
          (this.subtotalWithDiscount * selectedTax.percent) / 100
        )
      }

      this.packageCustomer.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

      if (this.$refs) {
        this.$refs.taxModal.close()
      }
    },

    async next() {
      
      let flag = false
      if(this.packageCustomer.items.length == 0 ){
        flag = true
        window.toastr['error'](this.$t('invoices.pbx_services.add_items'))
      }

      this.packageCustomer.items.forEach((item) => {
             
        if( item.quantity == 0  || item.quantity == '' )
        {
          flag = true
          window.toastr['error'](this.$t('invoices.pbx_services.verify_items_quantity') +', ' + item.name)
        }       
        if( item.name == '' )
        {
          flag = true
          window.toastr['error'](this.$t('invoices.pbx_services.verify_items_empty'))
        }       
        if(  item.price == 0)
        {
          flag = true
          window.toastr['error'](this.$t('invoices.pbx_services.verify_items_price') +', ' + item.name)
        }       
      })
      if(flag){return}

      this.isLoading = true
      ;(this.packageCustomer.sub_total = this.subtotal),
        (this.packageCustomer.total = this.total),
        (this.packageCustomer.tax = this.totalTax)
      let data = {
        parameters: this.parameters,
        package: this.package,
        packageCustomer: this.packageCustomer,
      }

      this.setPackageParameters(data)
      await this.$emit('next')
      this.isLoading = false
    },
  },
}
</script>

<style lang="scss">
.package-details {
  .invoice-foot {
    .invoice-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .invoice-foot {
      .invoice-total {
        min-width: 384px;
      }
    }
  }
}
</style>