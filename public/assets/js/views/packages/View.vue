<template>
  <base-page class="item-create">
    <form action="" @submit.prevent="submitPackage">
      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
      <sw-page-header :title="$tc('packages.view_package')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('packages.package_name', 2)"
            to="/admin/packages"
          />
          <sw-breadcrumb-item
            :title="$t('packages.view_package')"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>
      <sw-card>
        <!-- Basic  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.basic') }}
          </h6>

          <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
            <sw-divider class="col-span-12" />
            <h1 class="col-span-12" v-text="$t('packages.lang_name')"></h1>
            <sw-input-group
              :label="$t('packages.name_package')"
              class="md:col-span-3"
            >
              <!-- <sw-input v-model="formData.name" focus type="text" name="name" disabled/> -->
              <div>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic"
                  v-text="formData.name"
                ></p>
              </div>
            </sw-input-group>
<!--
            <div class="tabs mb-5 grid col-span-12">
              <div class="border-b tab">
                <div class="border-l-2 border-transparent relative">
                  <input
                    class="
                      w-full
                      absolute
                      z-10
                      cursor-pointer
                      opacity-0
                      h-5
                      top-6
                    "
                    type="checkbox"
                    checked
                    id="chck1"
                  />
                  <header
                    class="
                      col-span-5
                      flex
                      justify-between
                      items-center
                      p-3
                      pl-0
                      pr-8
                      cursor-pointer
                      select-none
                      tab-label
                    "
                    for="chck1"
                  >
                    <span class="text-grey-darkest font-thin text-xl">
                      Description
                    </span>
                    <div
                      class="
                        rounded-full
                        border border-grey
                        w-7
                        h-7
                        flex
                        items-center
                        justify-center
                        test
                      "
                    >
                  
                      <svg
                        aria-hidden="true"
                        class=""
                        data-reactid="266"
                        fill="none"
                        height="24"
                        stroke="#606F7B"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        viewbox="0 0 24 24"
                        width="24"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <polyline points="6 9 12 15 18 9"></polyline>
                      </svg>
                    </div>
                  </header>
                  <div class="tab-content">
                    <div class="pl-0 pr-8 pb-5 text-grey-darkest">
                      <ul class="pl-0">
                        <li class="pb-2">
                          <sw-tabs :active-tab="activeTab">
                            <sw-tab-item title="HTML">
                              <div class="p-10">
                                <p
                                  class="
                                    text-sm
                                    font-bold
                                    leading-5
                                    text-black
                                    non-italic
                                  "
                                  v-text="formData.descriptionHTML"
                                ></p>
                              </div>
                            </sw-tab-item>
                            <sw-tab-item title="Text">
                              <div class="p-10">
                                <p
                                  class="
                                    text-sm
                                    font-bold
                                    leading-5
                                    text-black
                                    non-italic
                                  "
                                  v-html="formData.descriptionText"
                                ></p>
                              </div>
                            </sw-tab-item>
                          </sw-tabs>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
-->
            <sw-input-group
              :label="$t('corePbx.packages.type')"
              class="md:col-span-2"
            >
              <div>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic"
                  v-text="formData.status_payment.text"
                ></p>
              </div>
            </sw-input-group>

            <sw-input-group
              :label="$t('packages.status')"
              class="md:col-span-2"
            >
              <div>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic"
                  v-text="formData.status.text"
                ></p>
              </div>
            </sw-input-group>

            <sw-input-group :label="$t('packages.qty')" class="md:col-span-3">
              <div>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic"
                  v-text="qty"
                ></p>
              </div>
            </sw-input-group>

            <sw-input-group
              :label="$t('packages.client_qty')"
              class="md:col-span-2"
            >
              <div>
                <p
                  class="text-sm font-bold leading-5 text-black non-italic"
                  v-text="client_qty"
                ></p>
              </div>
            </sw-input-group>

            <sw-divider class="my-0 col-span-12 opacity-0" />

            <!-- <div class="flex mt-2 col-span-12">
              <div class="relative w-12">
                <sw-switch
                  disabled
                  v-model="upgrades_use_renewal"
                  class="absolute"
                  style="top: -20px"
                />
              </div>

              <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('packages.upgrades_use_renewal') }}
                </p>

                <p
                  class="p-0 m-0 text-xs leading-4 text-gray-500"
                  style="max-width: 480px"
                >
                  {{ $t('packages.upgrades_use_renewal_description') }}
                </p>
              </div>
            </div> -->
          </div>
        </div>

        <!-- Group  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_group') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-divider class="col-span-12" />

            <div class="col-span-3">
              <h6>{{ $t('packages.member_groups') }}</h6>
              <div
                class="grid gap-6 grid-col-1 md:grid-cols-2"
                style="
                  height: 100px;
                  overflow-y: scroll;
                  border: 1px solid;
                  border-radius: 5px;
                  border-color: #cbd5e0;
                  border-width: 1px;
                  border-style: solid;
                "
              >
                <ul>
                  <li v-for="item in groupLeft" :key="item.id">
                    <div>
                      {{ item.name }}
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <div class="col-span-3">
              <h6>{{ $t('packages.available_groups') }}</h6>
              <div
                class="grid gap-6 grid-col-1 md:grid-cols-2"
                style="
                  height: 100px;
                  overflow-y: scroll;
                  border: 1px solid;
                  border-radius: 5px;
                  border-color: #cbd5e0;
                  border-width: 1px;
                  border-style: solid;
                "
              >
                <ul>
                  <li v-for="item in groupRight" :key="item.id">
                    <div>
                      {{ item.name }}
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <br /><br />

          <!-- Group discount -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.title_discount') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <div class="col-span-3">
              <sw-divider class="col-span-12" />
              <div class="flex mt-3 mb-4">
                <div class="relative w-12">
                  <sw-switch
                    v-model="checkonDiscounts"
                    class="absolute"
                    style="top: -20px"
                  />
                </div>
                <div class="ml-4">
                  <p
                    class="p-0 mb-1 text-base leading-snug text-black box-title"
                  >
                    {{ $t('packages.apply_general_discount') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="col-span-3">
              <div class="flex mt-3 mb-4">
                <div class="relative" style="width: 8em">
                  <label>
                    {{ $t('packages.title_discounts') }}<br />
                  </label>
                  {{ formData.discounts.text }}
                </div>
                <div class="relative" style="width: 8em; margin-left: 30px">
                  <label>
                    {{ $t('packages.value_discount') }}:<br />
                  </label>
                  {{ value_discount }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tax  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.taxes') }}
          </h6>

          <div
            class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-divider class="col-span-12" />
            <sw-table-component
              class="col-span-12"
              ref="table"
              :show-filter="false"
              :data="formData.taxes"
              table-class="table"
              variant="gray"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('settings.tax_types.tax_name')"
                show="name"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.tax_name') }}</span>
                  <span class="mt-6">{{ row.name }}</span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.compound_tax')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.compound_tax') }}</span>
                  <sw-badge
                    :bg-color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).bgColor
                    "
                    :color="
                      $utils.getBadgeStatusColor(
                        row.compound_tax ? 'YES' : 'NO'
                      ).color
                    "
                  >
                    {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
                  </sw-badge>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :filterable="true"
                :label="$t('settings.tax_types.percent')"
              >
                <template slot-scope="row">
                  <span>{{ $t('settings.tax_types.percent') }}</span>
                  {{ row.percent }} %
                </template>
              </sw-table-column>
            </sw-table-component>
            <div class="col-span-12"></div>
          </div>
        </div>

         <br /><br />

        <!-- Items -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <sw-divider class="col-span-12" />

          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('packages.packages_items') }}
          </h6>
          <div
            class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group
              :label="$t('packages.item_groups')"
              class="md:col-span-3"
              required
            >
            </sw-input-group>
            <div
              class="col-span-12"
              v-if="
                undefined !== formData.item_groups &&
                formData.item_groups.length > 0
              "
            >
              <div class="flex flex-wrap justify-start">
                <div
                  v-for="(item, index) in formData.item_groups"
                  :key="index"
                  class="
                    flex
                    justify-center
                    items-center
                    m-1
                    font-medium
                    py-1
                    px-2
                    bg-white
                    rounded-full
                    text-indigo-100
                    bg-indigo-700
                    border border-indigo-700
                  "
                >
                  <div
                    class="
                      text-xs text-base
                      leading-none
                      max-w-full
                      flex-initial
                      py-2
                      pl-2
                    "
                    v-text="item.name"
                  />
                </div>
              </div>
            </div>
            <div class="grid col-span-12">
              <!-- Items -->
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
                      class="
                        px-5
                        py-3
                        text-sm
                        not-italic
                        font-medium
                        leading-5
                        text-left text-gray-700
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      <span>
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
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      <span>
                        {{ $t('packages.item.quantity') }}
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
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      <span>
                        {{ $t('packages.item.price') }}
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
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      <span>
                        {{ $t('packages.item.discount') }}
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
                        border-t border-b border-gray-200 border-solid
                      "
                    >
                      <span class="column-heading">
                        {{ $t('packages.item.amount') }}
                      </span>
                    </th>
                  </tr>
                </thead>
                <package-item
                  v-for="(item, index) in formData.items"
                  :key="item.id"
                  :index="index"
                  :item-data="item"
                  :currency="currency"
                  :package-items="formData.items"
                  :tax-per-item="taxPerItem"
                  :discount-per-item="discountPerItem"
                  :isView="true"
                  @remove="removeItem"
                  @update="updateItem"
                  @itemValidate="checkItemsData"
                />
              </table>
            </div>
          </div>
        </div>

        <!-- Amount -->
         <!--
        <div class="grid grid-cols-5 gap-4 mb-8">
          <sw-divider class="col-span-12" />

          <h6 class="col-span-5 sw-section-title lg:col-span-1"></h6>
          <div
            class="
              col-span-5
              lg:col-span-4
              gap-y-6 gap-x-4
              md:grid-cols-6
              flex
              wrap
              content-end
              justify-end
            "
          >
            <div
              class="
                px-5
                py-4
                mt-6
                bg-white
                border border-gray-200 border-solid
                rounded
                package-total
                lg:mt-0
              "
            >
              <div class="flex items-center justify-between w-full">
                <label
                  class="
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('estimates.sub_total') }}</label
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
                  <div v-html="$utils.formatMoney(subtotal, currency)" />
                </label>
              </div>
              <template v-if="false">
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
                    <div v-html="$utils.formatMoney(tax.amount, currency)" />
                  </label>
                </div>
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
                    >{{ $t('packages.title_discount') }}</label
                  >
                  <div class="flex" style="width: 125px" role="group">
                    <sw-input
                      v-model="value_discount"
                      class="border-r-0 rounded-tr-sm rounded-br-sm"
                      disabled
                    />
                    <sw-input
                      v-model="formData.discounts.value"
                      class="border-l-0 rounded-tr-sm rounded-br-sm"
                      disabled
                    />
                    <!--  <sw-dropdown position="bottom-end">
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
                          formData.discount_type == 'fixed'
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

                <div v-if="taxPerItem === 'NO' || taxPerItem === null">
                  <tax
                    v-for="(tax, index) in formData.taxes"
                    :index="index"
                    :total="subtotalWithDiscount"
                    :key="tax.id"
                    :tax="tax"
                    :taxes="formData.taxes"
                    :currency="currency"
                    :total-tax="totalSimpleTax"
                    @remove="removeEstimateTax"
                    @update="updateTax"
                  />
                </div>

                <sw-popup
                  v-if="taxPerItem === 'NO' || taxPerItem === null"
                  ref="taxModal"
                  class="my-3 text-sm font-semibold leading-5 text-primary-400"
                >
                  <div slot="activator" class="float-right pt-2 pb-4">
                    + {{ $t('estimates.add_tax') }}
                  </div>
                  <tax-select-popup
                    :taxes="formData.taxes"
                    @select="onSelectTax"
                  />
                </sw-popup>
              </template>
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
                    m-0
                    text-sm
                    font-semibold
                    leading-5
                    text-gray-500
                    uppercase
                  "
                  >{{ $t('estimates.total') }}
                  {{ $t('estimates.amount') }}:</label
                >
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
                  <div v-html="$utils.formatMoney(total, currency)" />
                </label>
              </div>
            </div>
          </div>
        </div>  -->
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import Guid from 'guid'
import RightArrow from '@/components/icon/RightArrow'
import LeftArrow from '@/components/icon/LeftArrow'
import TaxStub from '../../stub/tax'
import { mapActions, mapGetters } from 'vuex'
import draggable from 'vuedraggable'
import PackageItem from './Item'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')

import {
  TrashIcon,
  PencilIcon,
  PlusIcon,
  ShoppingCartIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    draggable,
    PackageItem,
    TrashIcon,
    PencilIcon,
    ShoppingCartIcon,
    PlusIcon,
    RightArrow,
    LeftArrow,
  },
  data() {
    return {
      itemGroupsFetch: [],
      item_groups: [],
      taxesFetch: [],
      taxes: [],
      tax: null,
      ultraid: 0,
      discountPerItem: null,
      taxPerItem: null,
      selectedCurrency: '',
      value_discount: 0,
      checkonDiscounts: false,
      groupRight: [],
      groupLeft: [],
      groupRightTax: [],
      groupLeftTax: [],
      isRequestOnGoing: true,
      isLoading: false,
      EstimateFields: [],
      isLoading: false,
      activeTab: 'Text',
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
        {
          value: 'R',
          text: 'Restricted',
        },
      ],
      status_payment: [
        { value: 'R', text: 'Prepaid' },
        { value: 'O', text: 'Postpaid' },
      ],
      client_qty: '',
      qty: '',
      upgrades_use_renewal: false,
      formData: {
        name: '',
        descriptionHTML: null,
        descriptionText: null,
        password: null,
        phone: null,
        status: {
          value: 'A',
          text: 'Active',
        },
        status_payment: { value: 'R', text: 'Prepaid' },
        discounts: {
          value: 'fixed',
          text: 'Fixed',
        },
        client_qty: null,
        qty: null,
        upgrades_use_renewal: false,
        taxes: [],
        items: [],
        item_groups: [],
      },
    }
  },
  watch: {
    subtotal(newValue) {
      if (this.formData.discount_type === 'percentage') {
        this.formData.discount_val = (this.formData.discount * newValue) / 100
      }
    },
  },
  computed: {
    currency() {
      return this.selectedCurrency
    },
    subtotalWithDiscount() {
      let result = 0
      if (this.formData.discounts.value == 'fixed') {
        result = Math.round(this.subtotal - this.formData.discount_val)
      } else {
        result = Math.round((this.subtotal * this.formData.discount_val) / 100)
      }

      return result
    },
    total() {
      return this.subtotalWithDiscount + this.totalTax
    },
    totalTax() {
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      }

      return window._.sumBy(this.formData.items, function (tax) {
        return tax.tax
      })
    },
    subtotal() {
      return this.formData.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },
  },
  methods: {
    ...mapActions('pack', [
      'fetchPackage',
      'fetchItemGroups',
      'fetchGroupMembership',
      'fetchGroupTaxMembership',
    ]),

    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType',
      'fetchTaxTypes',
    ]),

    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    async fetchInitialItems() {
      // if (!this.isEdit) {
      let response = await this.fetchCompanySettings([
        'discount_per_item',
        'tax_per_item',
      ])

      if (response.data) {
        this.discountPerItem = response.data.discount_per_item
        this.taxPerItem = response.data.tax_per_item
      }
      // }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
        }),
        this.fetchCompanySettings(['estimate_auto_generate']),
      ])
        .then(async ([res1, res2]) => {
          // console.log('REs 1', res1)
          // this.itemsF = res1.data.items.data
          // if (res5.data) {
          //   this.discountPerItem = res5.data.discount_per_item
          //   this.taxPerItem = res5.data.tax_per_item
          // }
        })
        .catch((error) => {
          console.log(error)
        })
    },

    async fetchInitialItemGroups() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let res = await this.fetchItemGroups(data)
      // console.log('fetchInitialItemGroups', res.data.response)
      this.item_groups = res.data.response
      this.itemGroupsFetch = res.data.response
    },

    removeItem(index) {
      this.formData.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.formData.items[data.index], { ...data.item })
    },

    onDiscounts(val) {
      this.showSelectdiscounts = !val
    },
    removeTax(tax) {
      // console.log('removeTax', tax)
      let myArray = this.formData.taxes

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

      /* let reformattedArray = this.formData.taxes.map((obj) => {
        if (obj.id != tax.id) {
          return obj
        }
      })
      array = reformattedArray.filter(function (element) {
        return element !== undefined
      }) */
      this.formData.taxes = myArray

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.taxes = filterByReference(this.taxesFetch, this.formData.taxes)
      this.tax = null
      // console.log('Format Taxes', this.formData.taxes)
    },
    taxSeleted(val) {
      // console.log('taxSeleted', val)
      const isId = (element) => element.id == val.id
      const index = this.formData.taxes.findIndex(isId)
      // console.log('find tax', index)
      if (index == -1) {
        this.formData.taxes.push(val)
      } else {
        window.toastr['error']('This tax was already selected')
        return false
      }

      const filterByReference = (arr1, arr2) => {
        let res = []
        res = arr1.filter((el) => {
          return !arr2.find((element) => {
            return element.id === el.id
          })
        })
        return res
      }

      this.taxes = filterByReference(this.taxesFetch, this.formData.taxes)
      setTimeout(() => {
        this.tax = null
      }, 500)
    },

    checkItemsData(index, isValid) {
      this.formData.items[index].valid = isValid
    },

    async loadPackage() {
      let vm = this
      let res = await this.fetchPackage(this.$route.params.id)
      let ultraid = this.$route.params.id;
      console.log(ultraid);
      let resGroupRight = await this.fetchGroupMembership()
      this.groupRight = resGroupRight
      this.groupLeft = res.data.response.groupLeft

      let resGroupRightTax = await this.fetchGroupTaxMembership()
      this.groupRightTax = resGroupRightTax
      this.groupLeftTax = res.data.response.groupLeftTax
      this.formData = res.data.response

      /* if (this.isEdit || this.isCopy) { */
        for (var i = 0; i < this.groupLeft.length; i++) {
          for (var j = 0; j < this.groupRight.length; j++) {
            if (this.groupLeft[i].id === this.groupRight[j].id) {
              this.groupRight.splice(j, 1)
            }
          }
        }
     /*  } */
      if (res.data.response.type || res.data.response.discount) {
        this.value_discount = res.data.response.discount_general
        vm.formData.discount_val = res.data.response.discount_general
        this.checkonDiscounts = true
      }
      this.formData.taxes = res.data.response.tax_types
      let {
        qty,
        client_qty,
        status,
        status_payment,
        upgrades_use_renewal,
        html,
        text,
        tax_types,
        items,
        item_groups,
      } = res.data.response

      let itemsArray = []
      items.forEach((item) => {
        if (item.taxes.length == 0) {
          item.taxes = [{ ...TaxStub, id: Guid.raw() }]
        }
        item.quantity = item.pivot.quantity
        item.id = item.pivot.id
        item.item_id = item.pivot.items_id
        item.price = item.pivot.price
        item.discount_type = item.pivot.discount_type
        item.discount = item.pivot.discount
        item.discount_val = item.pivot.discount_val
        item.tax = item.pivot.tax
        item.description = item.pivot.description
        item.total = item.pivot.total
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.totalCompoundTax = 0
        itemsArray.push(item)
      })

      let arrayItemGroups = [],
        arrayItemGroupsItems = [],
        arrayItemGroupsSelect = []
      // console.log('item_groups.length', item_groups.length)
      if (item_groups.length > 0) {
        item_groups.forEach((itemG) => {
          for (let i = 0; i < vm.itemGroupsFetch.length; i++) {
            const element = vm.itemGroupsFetch[i]
            if (element.id === itemG.item_group_id) {
              for (let h = 0; h < itemsArray.length; h++) {
                const item = itemsArray[h]
                if (item.item_group_id == itemG.id) {
                  arrayItemGroupsItems.push(item)
                }
              }
              element.items = arrayItemGroupsItems
              arrayItemGroups.push(element)
            } else {
              arrayItemGroupsSelect.push(element)
            }
          }
        })
        vm.formData.item_groups = arrayItemGroups
        vm.item_groups = arrayItemGroupsSelect
      }

      this.qty = qty
      this.client_qty = client_qty
      this.formData.taxes = tax_types
      this.formData.items = itemsArray
      // console.log('Form Items', this.formData.items)
      ;(this.formData.status = this.status.filter(
        (element) => element.value == status
      )[0]),
        (this.formData.status_payment = this.status_payment.filter(
          (element) => element.value == status_payment
        )[0]),
        (this.formData.descriptionHTML = html)
      if (text != null) {
        this.formData.descriptionText = text.replace(/\<b\>/gi, '')
      }
      this.upgrades_use_renewal = upgrades_use_renewal == 1 ? true : false
      this.isRequestOnGoing = false

      // console.log('formData', this.formData)
    },
    async fetchTax() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let response = await this.fetchTaxTypes(data)
      // console.log('fetchTax', response.data.taxTypes.data)
      let taxes = response.data.taxTypes.data
      taxes.forEach((element) => {
        element.name_por = `${element.name} - ${element.percent}%`
      })
      this.taxes = taxes
      this.taxesFetch = taxes
    },
  },
  mounted() {
    this.fetchInitialItemGroups()
    this.fetchTax()
    this.fetchInitialItems()
    this.loadPackage()
    return true
  },
}
</script>


<style lang="scss">
.package-create-page {
  .package-foot {
    .package-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .package-foot {
      .package-total {
        min-width: 384px;
      }
    }
  }
}

// Dropdown
.tab {
  overflow: hidden;
}
.tab-content {
  max-height: 0;
  transition: all 0.5s;
}
input:checked + .tab-label .test {
  background-color: #000;
}
input:checked + .tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}
input:checked + .tab-label::after {
  transform: rotate(90deg);
}
input:checked ~ .tab-content {
  max-height: 100vh;
}
</style>