<style>
.address-grid {
  display: grid;
  grid-template-columns: repeat(
    4,
    1fr
  ); /* Crea cuatro columnas de igual ancho */
}

.address-row {
  display: contents;
}

.address-column {
  padding: 10px; /* Ajusta el padding según sea necesario */
  /* Añade estilos adicionales si es necesario */
}
</style>

<template>
  <div
    class="package-details"
    :class="{
      'w-full mb-8 bg-white border border-gray-200 border-solid rounded p-8 relative':
        !isView,
    }"
  >
    <div v-if="!isView" class="heading-section">
      <p class="text-2xl not-italic font-semibold leading-7 text-black">
        {{ $t('customers.confirm_package') }}
      </p>
    </div>

    <div v-else>
      <sw-divider class="my-6" />

      <p class="text-gray-500 uppercase sw-section-title">
        {{ $t('customers.package_details') }}
      </p>
    </div>

    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <div class="mt-8">
          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('packages.package_number') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                <span v-if="isEdit">
                  {{ this.parameters.package.package_number != undefined  ? this.parameters.package.package_number : '' }}
                </span>
                <span v-else>
                  {{ this.package.package_number != undefined ? this.package.package_number : '' }}
                </span>
                
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('packages.name') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                <span v-if="isEdit">
                  {{ this.parameters.package.package_name != undefined  ? this.parameters.package.package_name : '' }}
                </span>
                <span v-else>
                  {{ this.package.name != undefined  ? this.package.name : '' }}
                </span>
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('packages.status') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
               


                <div v-if="this.parameters.status.name == 'A' || this.parameters.status.name == 'Active'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
              :color="$utils.getBadgeStatusColor('COMPLETED').color"
              class="px-3 py-1"
            >
            {{ $t('general.active') }}
            </sw-badge>             
              
            </div>

            
            <div v-if="this.parameters.status.name == 'P' || this.parameters.status.name == 'Pending'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
              :color="$utils.getBadgeStatusColor('VIEWED').color"
              class="px-3 py-1"
            >
            {{ $t('general.pending') }}
            </sw-badge>
          </div>
         


          <div v-if="this.parameters.status.name == 'S' || this.parameters.status.name == 'Suspended'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('SENT').bgColor"
              :color="$utils.getBadgeStatusColor('SENT').color"
              class="px-3 py-1"
            >
           {{ $t('general.suspended') }}
            </sw-badge>
          </div>
             
          <div v-if="this.parameters.status.name  == 'C' || this.parameters.status.name == 'Cancelled'">
              <sw-badge
              :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
              :color="$utils.getBadgeStatusColor('OVERDUE').color"
              class="px-3 py-1"
            >
            {{ $t('general.cancelled') }}
            </sw-badge>
          </div>


              </p>
            </div>
          </div>

          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.tax_by') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters.tax_type.name }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.discount_by') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters.discount_type.name }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.term') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters.term.name }}
              </p>
            </div>
          </div>

          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('customers.date_begin') }}
              </p>
              <p class="text-sm font-bold leading-5 text-black non-italic">
                {{ this.parameters.date_begin }}
              </p>
            </div>
          </div>

          <!------------------- Address ----------------------->

          <div v-if="this.packageCustomer.FormattedAddress &&
this.packageCustomer.FormattedAddress !== 'N/A' && this.isView">
<!-- Contenido del div -->

<sw-divider class="my-8" />

<p class="text-gray-500 uppercase sw-section-title">
{{ $t('settings.company_info.address') }}
</p>

<div class="address-grid">
<!-- Primera fila -->
<div class="address-row">
<div class="address-column"><b>{{ $t('customers.country') }}:</b> {{ this.packageCustomer.FormattedAddress.CountryName }}</div>
<div class="address-column"><b>{{ $t('customers.state') }}:</b> {{ this.packageCustomer.FormattedAddress.StateName }}</div>
<div class="address-column"><b>{{ $t('customers.county') }}:</b> {{ this.packageCustomer.FormattedAddress.county }}</div>
<div class="address-column"><b>{{ $t('customers.city') }}:</b>{{ this.packageCustomer.FormattedAddress.city }}</div>
</div>
<!-- Segunda fila -->
<div class="address-row">
<div class="address-column"><b>{{ $t('customers.zip_code') }}:</b> {{ this.packageCustomer.FormattedAddress.zip }}</div>
<div class="address-column" v-if="this.packageCustomer.FormattedAddress.type === 'services_address'">
<b>{{ $t('general.type') }}:</b> {{ $t('general.for_service') }}
</div>
<div class="address-column" v-else-if="this.packageCustomer.FormattedAddress.type === 'billing'">
<b>{{ $t('general.type') }}:</b> {{ $t('customers.billing_address') }}
</div>
<div class="address-column" v-else-if="this.packageCustomer.FormattedAddress.type === 'shipping'">
<b>{{ $t('general.type') }}:</b> {{ $t('customers.shipping_address') }}
</div>
<div class="address-column"><b>{{ $t('customers.address_1') }}:</b> {{ this.packageCustomer.FormattedAddress.address_street_1 }}</div>
<div class="address-column"><b>{{ $t('customers.address_2') }}:</b> {{ this.packageCustomer.FormattedAddress.address_street_2 }}</div>
</div>
</div>
</div>


          <!------------------- ITEMS ----------------------->
          <sw-divider class="my-8" />

          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('items.title') }}
          </p>

          <div class="mt-8">
            <table class="w-full text-center item-table">
              <colgroup>
                <col style="width: 40%" />
                <col style="width: 10%" />
                <col style="width: 15%" />
                <col v-if="discountPerItem === 'YES'" style="width: 15%" />
                <col style="width: 15%" />
              </colgroup>
              <thead class="bg-white">
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                  "
                >
                  <span class="">
                    {{ $tc('items.item', 1) }}
                  </span>
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-right text-gray-700
                  "
                >
                  <span class="">
                    {{ $t('item_groups.item.qty') }}
                  </span>
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                  "
                >
                  <span class="">
                    {{ $t('item_groups.item.price') }}
                  </span>
                </th>
                <th
                  v-if="discountPerItem === 'YES'"
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-left text-gray-700
                  "
                >
                  {{ $t('invoices.item.discount') }}
                </th>
                <th
                  class="
                    px-5
                    py-3
                    text-sm
                    not-italic
                    font-medium
                    leading-5
                    text-right text-gray-700
                  "
                >
                  <span class="">
                    {{ $t('invoices.item.total') }}
                  </span>
                </th>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in packageCustomer.items"
                  :key="index"
                  class="border-t border-gray-200 border-solid"
                >
                  <td colspan="5" class="p-0 text-left align-top">
                    <table class="w-full">
                      <colgroup>
                        <col style="width: 40%" />
                        <col style="width: 10%" />
                        <col style="width: 15%" />
                        <col
                          v-if="discountPerItem === 'YES'"
                          style="width: 15%"
                        />
                        <col style="width: 15%" />
                      </colgroup>
                      <tbody>
                        <tr>
                          <td class="px-5 py-4 text-left align-top">
                            <div class="flex justify-start">
                              <span>{{ item.name }}</span>
                            </div>
                          </td>
                          <td class="px-5 py-4 text-right align-top">
                            <span>{{ item.quantity }}</span>
                          </td>
                          <td class="px-5 py-4 text-left align-top">
                            <div class="flex flex-col">
                              <div class="flex-auto flex-fill bd-highlight">
                                <div class="relative w-full">
                                  <span
                                    v-html="
                                      $utils.formatMoney(
                                        item.price,
                                        defaultCurrency
                                      )
                                    "
                                  >
                                  </span>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td
                            v-if="discountPerItem === 'YES'"
                            class="px-5 py-4 text-left align-top"
                          >
                            <div class="flex flex-col">
                              <div class="flex flex-auto" role="group">
                                <span>{{ item.discount }}</span>
                                <span class="flex">
                                  {{
                                    item.discount_type === 'fixed'
                                      ? defaultCurrency.symbol
                                      : '%'
                                  }}
                                </span>
                              </div>
                            </div>
                          </td>
                          <td class="px-5 py-4 text-right align-top">
                            <div class="flex items-center justify-end text-sm">
                              <span>
                                <div
                                  v-html="
                                    $utils.formatMoney(
                                      item.total,
                                      defaultCurrency
                                    )
                                  "
                                />
                              </span>
                            </div>
                          </td>
                        </tr>

                                   <!--perido deo faturacion /> -->
          <tr style="display: flex; align-items: center; margin-bottom: 10px">
            <td style="margin-right: 10px; margin-left: 10px">
              <sw-switch
                v-model="item.end_period_act"
                class="relative"
                :disabled="true"
                style="top: -10px; margin-bottom: 10px"
              />
            </td>
            <td style="flex-grow: 1; margin-left: 15px; margin-bottom: 10px">
              <div>
                <p
                  class="p-0 mb-1 text-base leading-snug text-black"
                  style="margin-top: 10px"
                >
                  {{ $t('packages.end_period_act') }}
                </p>
                <p
                  class="p-0 m-0 text-xs leading-tight text-gray-500"
                  style="
                    max-width: 480px;
                    margin-left: 10px;
                    margin-bottom: 10px;
                  "
                >
                  {{ $t('packages.end_period_act_desp') }}
                </p>
              </div>
            </td>
            <td style="margin-left: 10px">
              <sw-input
                :label="$t('packages.end_period_num')"
                v-model="item.end_period_number"

                :disabled="true"
                type="number"
                min="1"
                small
                style="margin-top: 10px; margin-bottom: 10px"
                onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"
              />
            </td>
          </tr>
                        <tr v-if="taxPerItem === 'YES'" class="tax-tr">
                          <td class="px-5 py-4 text-left align-top" />
                          <td colspan="4" class="px-5 py-4 text-left align-top">
                            <div
                              v-for="(tax, index) in item.taxes"
                              :key="index"
                              v-if="tax.amount"
                              class="flex items-center justify-between mb-3"
                            >
                              <div class="flex items-center" style="flex: 4">
                                <label class="pr-2 mb-0" align="right">
                                  {{ $t('general.tax') }}
                                </label>
                                <span>{{
                                  '(' + tax.name + ' - ' + tax.percent + '%)'
                                }}</span>
                              </div>
                              <div
                                class="text-sm text-right"
                                style="flex: 3"
                                v-html="
                                  $utils.formatMoney(
                                    tax.amount,
                                    defaultCurrency
                                  )
                                "
                              />
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <sw-divider class="my-8" />

          <!------------------------ TOTALS -------------------------->

          <div
            class="
              block
              my-10
              invoice-foot
              lg:justify-between
              lg:flex
              lg:items-start
            "
          >
            <div class="w-full lg:w-1/2"></div>

            <div
              class="
                px-5
                py-4
                mt-6
                bg-white
                border border-gray-200 border-solid
                rounded
                invoice-total
                lg:mt-0
              "
            >
              <!----------- SUB TOTAL ----------->
              <div class="flex items-center justify-between w-full">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('invoices.sub_total') }}</label
                >
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                    uppercase
                  "
                >
                  <div
                    v-html="
                      $utils.formatMoney(
                        packageCustomer.sub_total,
                        defaultCurrency
                      )
                    "
                  />
                </label>
              </div>

              <!--------------- ITEM TAXES ------------->
              <div
                v-for="tax in allTaxes"
                :key="tax.tax_type_id"
                class="flex items-center justify-between w-full"
              >
                <label
                  class="
                    m-0
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ tax.name }} - {{ tax.percent }}%
                </label>
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    m-0
                    text-lg text-black
                    uppercase
                  "
                  style="font-size: 18px"
                >
                  <div
                    v-html="$utils.formatMoney(tax.amount, defaultCurrency)"
                  />
                </label>
              </div>

              <!--------------- DISCOUNT --------------->
              <div
                v-if="discountPerItem === 'NO' || discountPerItem === null"
                class="flex items-center justify-between w-full mt-2"
              >
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >
                  {{ $t('invoices.discount') }}
                </label>
                <div class="text-right" style="width: 105px" role="group">
                  <span>{{ packageCustomer.discount }}</span>
                  <span class="">
                    {{
                      packageCustomer.discount_type === 'fixed'
                        ? defaultCurrency.symbol
                        : '%'
                    }}
                  </span>
                </div>
              </div>

              <!------------- PACKAGES TAXES ----------->
              <div v-if="taxPerItem ? 'NO' : null">
                <div
                  v-for="(tax, index) in packageCustomer.taxes"
                  :key="index"
                  class="flex items-center justify-between w-full mt-2 text-sm"
                >
                  <label
                    class="font-semibold leading-5 text-gray-500 uppercase"
                  >
                    {{ tax.name }} ({{ tax.percent }}%)
                  </label>
                  <label
                    class="flex items-center justify-center text-lg text-black"
                  >
                    <div
                      v-html="$utils.formatMoney(tax.amount, defaultCurrency)"
                    />
                  </label>
                </div>
              </div>

              <!----------------- TOTAL ----------------->
              <div
                class="
                  flex
                  items-center
                  justify-between
                  w-full
                  pt-2
                  mt-5
                  border-t border-gray-200 border-solid
                "
              >
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                >
                  {{ $t('invoices.total') }} {{ $t('invoices.amount') }}:
                </label>
                <label
                  class="
                    flex
                    items-center
                    justify-center
                    text-lg
                    uppercase
                    text-primary-400
                  "
                >
                  <div
                    v-html="
                      $utils.formatMoney(packageCustomer.total, defaultCurrency)
                    "
                  />
                </label>
              </div>
            </div>
          </div>

          <!------------------- ACTIONS --------------------->
          <div v-if="!isView" class="mb-4">
            <sw-button
              :disabled="isLoading"
              variant="primary-outline"
              size="lg"
              class="flex justify-center w-full md:w-auto align-bottom"
              @click="back()"
            >
              <arrow-left-icon class="h-5 mr-2 -ml-1" />
              {{ $t('general.back') }}
            </sw-button>

            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
              class="flex justify-center w-full md:w-auto ml-4 align-bottom"
              @click="submitPackage"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{ isEdit ? $t('general.update') : $t('services.add_service') }}
            </sw-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ArrowRightIcon, ArrowLeftIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import PackageStub from '../../../stub/customerPackage'
import TaxStub from '../../../stub/tax'
import Guid from 'guid'

const { required } = require('vuelidate/lib/validators')

export default {
  components: {
    ArrowRightIcon,
    ArrowLeftIcon,
  },
  data() {
    return {
      isLoading: false,
      isRequestOnGoing: false,
      package: '',
      parameters: {
        status: { name: '', value: '' },
        tax_type: { name: '', value: '' },
        discount_type: { name: '', value: '' },
        term: { name: '', value: '' },
        date_begin: '',
      },
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
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        taxes: [],
      },
      discountPerItem: null,
      taxPerItem: null,
      formData: {
        package: '',
        status: { name: 'Active', value: 'A' },
        date_begin: '',
        discount_type: { name: 'None', value: 'N' },
        allow_discount: false,
        discount_start_date: '',
        discount_end_date: '',
        tax_type: { name: 'None', value: 'N' },
      },
      status: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ],
      types: [
        { name: 'None', value: 'N' },
        { name: 'General', value: 'G' },
        { name: 'By item', value: 'I' },
      ],
      term: [
        { name: 'Daily', value: 'daily' },
        { name: 'Weekly', value: 'weekly' },
        { name: 'Monthly', value: 'monthly' },
        { name: 'Bimonthly', value: 'bimonthly' },
        { name: 'Quarterly', value: 'quarterly' },
        { name: 'Biannual', value: 'biannual' },
        { name: 'Yearly', value: 'yearly' },
        { name: 'One time', value: 'one time' },
      ],
    }
  },
  validations: {
    formData: {
      package: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer', 'packageParameters']),
    ...mapGetters('pack', ['packagesByGroup']),
    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters('service', ['selectedService']),

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

    shouldRenderDiv() {
      return (
        this.parameters.FormattedAddress &&
        this.parameters.FormattedAddress !== 'N/A'
      )
    },

    discount() {
      /*if (this.formData.discount_type.value === 'N') {
                return 0.00;
            }
            if (this.package && this.package.type === 'percentage') {
                return this.package.discount + '%'
            }
            if (this.package && this.package.type === 'fixed') {
                return this.$utils.formatMoney(this.package.discount, this.defaultCurrency)
            }*/

      return 0.0
    },

    isPending() {
      return this.parameters.status.value === 'P'
    },

    isEdit() {
      if (this.$route.name === 'services.edit') {
        return true
      }
      return false
    },

    isView() {
      if (this.$route.name === 'customers.package-view') {
        return true
      }
      return false
    },
  },
  created() {
    this.loadData()
  },
  methods: {
    ...mapActions('pack', ['fetchPackagesByGroup', 'fetchPackage']),
    ...mapActions('customer', ['addPackage']),
    ...mapActions('service', ['fetchViewService', 'updateService']),

    async loadData() {
      this.isRequestOnGoing = true

      if (this.isView) {
        await this.fetchViewService(this.$route.params.customer_package_id)

        this.packageCustomer = this.selectedService

        //console.log(this.packageCustomer)
        this.package = this.selectedService.package
        this.parameters.status = this.status.find(
          (_status) => this.packageCustomer.status === _status.value
        )
        this.parameters.tax_type = this.types.find(
          (_type) => this.packageCustomer.tax_by === _type.value
        )
        this.parameters.discount_type = this.types.find(
          (_type) => this.packageCustomer.discount_by === _type.value
        )
        this.parameters.term = this.term.find(
          (_term) => this.packageCustomer.term === _term.value
        )
        this.parameters.date_begin = this.packageCustomer.activation_date

        this.taxPerItem = this.parameters.tax_type.value === 'I' ? 'YES' : 'NO'
        this.discountPerItem =
          this.parameters.discount_type.value === 'I' ? 'YES' : 'NO'

        await this.$emit('status', this.packageCustomer.status)
      } else {
        this.package = this.packageParameters.package
        this.packageCustomer = this.packageParameters.packageCustomer
        this.parameters = this.packageParameters.parameters
        this.taxPerItem = this.parameters.tax_type.value === 'I' ? 'YES' : 'NO'
        this.discountPerItem =
          this.parameters.discount_type.value === 'I' ? 'YES' : 'NO'
      }

      this.isRequestOnGoing = false
    },

    async back() {
      await this.$emit('back')
      this.package = ''
    },

    async submitPackage() {
      let response
      this.isLoading = true

      let message = this.isEdit
        ? this.$t('services.confirm_update')
        : this.$tc('customers.confirm_add_package', 1)

      swal({
        title: this.$t('general.are_you_sure'),
        text: message,
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: {
          cancel: true,
          confirm: true,
        },
      }).then(async (result) => {
        if (result) {
          if (this.isEdit) {
            let data = {
              id: this.$route.params.customer_package_id,
              parameters: this.parameters,
              packageCustomer: this.packageCustomer,
            }
            response = await this.updateService(data)
          } else {
            let data = {
              parameters: this.parameters,
              packageCustomer: this.packageCustomer,
            }
            response = await this.addPackage(data)
          }

          this.isLoading = false
          if (response.data.success) {
            if (this.isEdit) {
              window.toastr['success'](this.$tc('services.updated_message'))
              this.$router.push(
                `/admin/customers/${this.$route.params.id}/service/${response.data.service.id}/view`
              )
            } else {
              window.toastr['success'](this.$tc('customers.package_created'))
              this.$router.push(
                `/admin/customers/${this.$route.params.id}/service/${response.data.customer_package.id}/view`
              )
            }

            return true
          }

          window.toastr['error'](response.data.message)
          return true
        }
        this.isLoading = false
      })
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