<style>
.table-responsive-item2 {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.tablemin {
  min-width: 900px;
}

/* Additional media query for finer control (optional) */
@media (max-width: 768px) {
  .table-responsive-item2 {
    /* Adjust table width as needed for smaller screens */
    width: 100%; /* Example adjustment */
  }
}
</style>

<template>
  <base-page class="item-create">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

    <!------------------- Cabecera  ----------------->
        
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard" />
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

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/packages`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/packages/${$route.params.id}/edit`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          v-if="permissionModule.update"
        >
          {{ $t('general.edit') }}
        </sw-button>
        <!-- @click="removeticketdepart($route.params.id)" -->
        <sw-button
          @click="removePackage($route.params.id)"
          slot="activator"
          variant="primary"
          v-if="permissionModule.delete"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.delete') }}
        </sw-button>
      </div>
    </div>

    
    <sw-card>
      <!-- Basic  -->
      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('packages.basic') }}
        </h6>

        <sw-divider class="col-span-12" />
        <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
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

          <sw-input-group
            :label="$t('corePbx.packages.type')"
            class="md:col-span-2"
          >
            <div>
              <div
                class="text-sm font-bold leading-5 text-black non-italic"
                v-if="formData.status_payment.text == 'Postpaid'"
              >
                {{ $t('packages.item.postpaid') }}
              </div>
              <div
                class="text-sm font-bold leading-5 text-black non-italic"
                v-if="formData.status_payment.text == 'Prepaid'"
              >
                {{ $t('packages.item.prepaid') }}
              </div>
            </div>
          </sw-input-group>

          <sw-input-group :label="$t('packages.status')" class="md:col-span-2">
            <div>
              <div v-if="formData.status.text == 'Active'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                  :color="$utils.getBadgeStatusColor('COMPLETED').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.active') }}
                </sw-badge>
              </div>
              <div v-if="formData.status.text == 'Inactive'">
                <sw-badge
                  :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                  :color="$utils.getBadgeStatusColor('OVERDUE').color"
                  class="px-3 py-1"
                >
                  {{ $t('general.inactive') }}
                </sw-badge>
              </div>
            </div>
          </sw-input-group>

          <sw-divider class="my-5 col-span-12 opacity-0" />
        </div>

        <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
          <sw-input-group
            :label="$t('customers.tax_type')"
            class="md:col-span-2"
          >
            <div>
              <p
                class="text-sm font-bold leading-5 text-black non-italic"
                v-text="formData.apply_tax_type"
              ></p>
            </div>
          </sw-input-group>
        </div>
      </div>

      <sw-divider class="col-span-12 my-5" />
      <!-- Group  -->
      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('packages.title_group') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6 mt-1"
        >
          <ul class="list-disc">
            <div v-for="item in groupLeft" :key="item.id">
              <li>
                <router-link
                  :to="`/admin/groups/${item.id}/view`"
                  class="font-bold"
                  style="cursor: pointer"
                  >{{ item.name }}</router-link
                >
              </li>
            </div>
          </ul>
          <!--

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
        --></div>
      </div>

      <br />
      <sw-divider class="col-span-12 my-5" />
      <!-- Group discount -->
      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('packages.title_discount') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <div class="col-span-3">
            <div class="flex mt-3 mb-4">
              <div>
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('packages.apply_general_discount') }}:
                </p>
              </div>
              <div class="ml-1 font-bold">
                <!-- <div class="ml-4"> -->
                <p v-if="checkonDiscounts">
                  {{ $t('packages.discount_enabled') }}
                </p>
                <p v-else>{{ $t('packages.discount_disabled') }}</p>
              </div>
            </div>
          </div>
          <div class="col-span-3">
            <div class="flex mt-3 mb-4">
              <div class="relative" style="width: 8em">
                <label> {{ $t('packages.title_discounts') }}<br /> </label>
                {{ formData.discounts.text }}
              </div>
              <div class="relative" style="width: 8em; margin-left: 30px">
                <label> {{ $t('packages.value_discount') }}:<br /> </label>
                {{ value_discount }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <sw-divider class="col-span-12 my-5" />
      <!-- Tax  -->
      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 sw-section-title lg:col-span-1">
          {{ $t('packages.taxes') }}
        </h6>

        <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
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
                    $utils.getBadgeStatusColor(row.compound_tax ? 'YES' : 'NO')
                      .bgColor
                  "
                  :color="
                    $utils.getBadgeStatusColor(row.compound_tax ? 'YES' : 'NO')
                      .color
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
        <div class="grid col-span-12 gap-y-6 gap-x-4 md:grid-cols-6">
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
                class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-full text-indigo-100 bg-indigo-700 border border-indigo-700"
              >
                <div
                  class="text-xs text-base leading-none max-w-full flex-initial py-2 pl-2"
                  v-text="item.name"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Items tanla-->
      <div class="table-responsive-item2">
        <div class="tablemin">
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
                <span>
                  {{ $tc('items.item', 1) }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ $t('packages.item.quantity') }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ $t('packages.item.price') }}
                </span>
              </th>
              <th
                v-if="discountPerItem === 'YES'"
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
              >
                <span>
                  {{ $t('packages.item.discount') }}
                </span>
              </th>
              <th
                class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
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


      <!------------ Packages ------------>

      <div
        class="tabs mb-5 grid col-span-12 border-t-2 border-solid pt-6"
        style="border-top-color: #f9fbff"
        v-if="permissionModule.accessNormalServices"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services') }}
              </span>
              <div
                class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
              >
                <!-- icon by feathericons.com -->
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
            <div class="tab-content-customer">
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeTabServices"
                  @update="setStatusFilter"
                >
                  <sw-tab-item :title="$t('customers.active')" filter="A" />
                  <sw-tab-item :title="$t('customers.pending')" filter="P" />
                  <sw-tab-item :title="$t('customers.suspend')" filter="S" />
                  <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
                </sw-tabs>
              </div>
              <sw-table-component
                ref="services_table"
                :show-filter="false"
                :data="fetchPackagesServicesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                  show="code"
                >
                  <template slot-scope="row">
                    <span>{{ $t('services.service_number') }}</span>
                    <router-link
                      :to="`/admin/customers/${row.customer_id}/service/${row.id}/view`"
                      class="font-medium text-primary-500"
                      v-if="permissionModule.readNormalServices"
                    >
                      {{ row.code }}
                    </router-link>
                    <span v-else>
                      {{ row.code }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ row.term }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="activation_date"
                  show="formattedActivationDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="formattedRenewalDate"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                      <sw-dropdown-item
                        :to="{
                          name: 'invoices.create',
                          query: {
                            from: 'customer',
                            code: row.code,
                            customer_packages_id: row.id,
                            customer_id: row.customer_id,
                            package_id: row.package_id,
                          },
                        }"
                        tag-name="router-link"
                        v-if="permissionModule.createInvoices"
                      >
                        <calculator-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('invoices.new_invoice') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                        :to="`/admin/customers/${row.customer_id}/service/${row.id}/view`"
                        tag-name="router-link"
                        v-if="permissionModule.readNormalServices"
                      >
                        <cog-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.manage') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
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
      activeTabServices: this.$t('customers.active'),
      statusServices: { name: 'Active', value: 'A' },
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
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
        readNormalServices: false,
        accessNormalServices: false,
        createInvoices: false,
      },
    }
  },
  created() {
    this.permissionsUserModule()
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
      'fetchPackagesServices',
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

    ...mapActions('pack', ['deletePackage']),
    ...mapActions('user', ['getUserModules']),

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
        .then(async ([res1, res2]) => {})
        .catch((error) => {})
    },

    async fetchInitialItemGroups() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let res = await this.fetchItemGroups(data)
      this.item_groups = res.data.response
      this.itemGroupsFetch = res.data.response
    },

    async fetchPackagesServicesData({ page, filter, sort }) {
      let data = {
        package_id: this.$route.params.id,
        status: this.statusServices.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchPackagesServices(data)
      this.isRequestOngoing = false

      let list = response.data.packagesList.data.map((pack) => {
        let discount_type = ''

        switch (pack.discount_type) {
          case 'N':
            discount_type = 'None'
            break
          case 'G':
            discount_type = 'General'
            break
          case 'I':
            discount_type = 'By item'
            break
        }

        return {
          ...pack,
          discount_type_name: discount_type,
        }
      })
      return {
        data: list,
        pagination: {
          totalPages: response.data.packagesList.last_page,
          currentPage: page,
          count: response.data.packagesList.count,
        },
      }
    },

    async removePackage(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('packages.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          try {
            let res = await this.deletePackage(id)

            if (res.data.success) {
              window.toastr['success'](this.$tc('packages.deleted_message', 1))
              this.$router.push({ name: 'packages.index' })
              return true
            }
            if (res.data.error === 'user_attached') {
              window.toastr['error'](
                this.$tc('packages.user_attached_message'),
                this.$t('general.action_failed')
              )
              return true
            }
            window.toastr['error'](res.data.message)
            return true
          } catch (e) {
            window.toastr['error'](this.$t('general.action_failed'))
          }
        }
      })
    },

    setStatusFilter(val) {
      if (this.activeTabServices === val.title) {
        return true
      }
      this.activeTabServices = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.statusServices = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.statusServices = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.statusServices = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.statusServices = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.$refs.services_table.refresh()
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
      let myArray = this.formData.taxes

      for (var i = myArray.length - 1; i >= 0; --i) {
        if (myArray[i].id == tax.id) {
          myArray.splice(i, 1)
        }
      }

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
    },
    taxSeleted(val) {
      const isId = (element) => element.id == val.id
      const index = this.formData.taxes.findIndex(isId)
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

      let ultraid = this.$route.params.id
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

        item.quantity = item.quantity
        item.id = item.id
        item.item_id = item.items_id
        item.price = item.price
        item.discount_type = item.discount_type
        item.discount = item.discount
        item.discount_val = item.discount_val
        item.tax = item.tax
        item.description = item.description
        item.total = item.total
        item.totalTax = 0
        item.totalSimpleTax = 0
        item.totalCompoundTax = 0
        itemsArray.push(item)
      })

      let arrayItemGroups = [],
        arrayItemGroupsItems = [],
        arrayItemGroupsSelect = []
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
    },
    async fetchTax() {
      let data = {
        orderByField: 'created_at',
        orderBy: 'asc',
      }

      let response = await this.fetchTaxTypes(data)
      let taxes = response.data.taxTypes.data
      taxes.forEach((element) => {
        element.name_por = `${element.name} - ${element.percent}%`
      })
      this.taxes = taxes
      this.taxesFetch = taxes
    },

    async permissionsUserModule() {
      const data = {
        module: 'packages',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions == null) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.access == 0) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true
        }
      }

      const dataNormalServices = {
        module: 'services_normal',
      }
      const permissionsNormalServices = await this.getUserModules(
        dataNormalServices
      )

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsNormalServices.super_admin == true) {
        this.permissionModule.readNormalServices = true
        this.permissionModule.accessNormalServices = true
      } else if (permissionsNormalServices.exist == true) {
        const modulePermissions = permissionsNormalServices.permissions[0]
        if (modulePermissions.access == 1) {
          this.permissionModule.accessNormalServices = true
        }
        if (modulePermissions.read == 1 && modulePermissions.access == 1) {
          this.permissionModule.readNormalServices = true
        }
      }

      const dataInvoices = {
        module: 'invoices',
      }
      const permissionsInvoices = await this.getUserModules(dataInvoices)

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissionsInvoices.super_admin == true) {
        this.permissionModule.createInvoices = true
      } else if (permissionsInvoices.exist == true) {
        const modulePermissions = permissionsInvoices.permissions[0]
        if (modulePermissions.read == 1 && modulePermissions.access == 1) {
          this.permissionModule.createInvoices = true
        }
      }
    },
  },
  capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1)
  },
  refreshTable() {
    this.$refs.table.refresh()
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
