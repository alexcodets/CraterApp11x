<template>
  <base-page class="customer-create">
    <!------------------- Cabecera  ----------------->
    <sw-page-header :title="$t('core_pos.main.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard" />
        <sw-breadcrumb-item
          :title="$t('core_pos.main.title')"
          to="cash-register.main"
          active
        />
      </sw-breadcrumb>

      <!-- <template slot="actions">
                <sw-button size="lg" variant="primary-outline" @click="toggleFilter">
                    {{ $t('general.filter') }}
                    <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
                </sw-button>
                <sw-button tag-name="router-link" to="create-cash-register" size="lg" variant="primary" class="ml-4">
                    <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
                    {{ $t('core_pos.add_cash_register') }}
                </sw-button>
            </template> -->
    </sw-page-header>

    <hr />
    <!-- INVOICES -->
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
      <!-- Amount Invoices POS -->
      <router-link
        slot="item-title"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-3 xl:p-4"
        to="#"
      >
        <div>
          <span
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            <span
              v-html="
                $utils.formatMoney(total_amount_invoices, defaultCurrency)
              "
            />
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            {{ $t('core_pos.main.total_amount_invoices') }}
          </span>
        </div>
        <div class="flex items-center">
          <dollar-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>

      <!-- Customers -->
      <router-link
        slot="item-title"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
        to="#"
      >
        <div>
          <span
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ quantity_invoices }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            {{ $t('core_pos.main.quantity_invoices') }}
          </span>
        </div>
        <div class="flex items-center">
          <contact-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>

      <router-link
        slot="item-title"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
        to="#"
      >
        <div>
          <span
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ quantity_cash_register }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            {{ $t('core_pos.main.quantity_cash_register') }}
          </span>
        </div>
        <div class="flex items-center">
          <contact-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>

      <router-link
        slot="item-title"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
        to="#"
      >
        <div>
          <span
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ quantity_payments }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            {{ $t('core_pos.main.quantity_payments') }}
          </span>
        </div>
        <div class="flex items-center">
          <contact-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>
    </div>
    <!--CASH HISTORY  -->
    <sw-card class="flex flex-col mt-3">
      <div class="tabs mb-5 grid col-span-12 pt-6">
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck5"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck5"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('core_pos.cash_history', 2) }}
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
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="tableHistories"
                :show-filter="false"
                :data="fetchCashHistories"
                table-class="tableHistories"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.cash_register')"
                  :filterable="true"
                  show="cash_register_name"
                >
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.cash_register') }}</span>
                    <router-link
                      :to="{
                        path: `/admin/corePOS/cash-register/${row.cash_register_id}/view`,
                      }"
                      class="font-medium text-primary-500"
                    >
                      {{ row.cash_register_name }}
                    </router-link>
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.reference')"
                  :filterable="true"
                  show="ref"
                />
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.open_date')"
                  :filterable="true"
                  show="open_date"
                />
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.cash_received')"
                  :filterable="true"
                  show="cash_received"
                >
                  <template slot-scope="row">
                    <span>{{
                      $t('core_pos.open_close_cash_modal.cash_received')
                    }}</span>
                    <div
                      v-html="
                        $utils.formatMoney(
                          row.cash_received * 100,
                          defaultCurrency
                        )
                      "
                    />
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.initial_amount')"
                  :filterable="true"
                  show="initial_amount"
                >
                  <template slot-scope="row">
                    <span>{{
                      $t('core_pos.open_close_cash_modal.initial_amount')
                    }}</span>
                    <div
                      v-html="
                        $utils.formatMoney(
                          row.initial_amount * 100,
                          defaultCurrency
                        )
                      "
                    />
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.close_date')"
                  :filterable="true"
                  show="close_date"
                >
                  <template slot-scope="row">
                    <span>{{
                      $t('core_pos.open_close_cash_modal.close_date')
                    }}</span>
                    <span>{{
                      row.close_date != null ? row.close_date : 'N/A'
                    }}</span>
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.final_amount')"
                  :filterable="true"
                  show="final_amount"
                >
                  <template slot-scope="row">
                    <span>{{
                      $t('core_pos.open_close_cash_modal.final_amount')
                    }}</span>
                    <div
                      v-html="
                        $utils.formatMoney(
                          row.final_amount * 100,
                          defaultCurrency
                        )
                      "
                    />
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.status')"
                  :filterable="true"
                >
                  <template slot-scope="row">
                    <span>{{
                      $t('core_pos.open_close_cash_modal.status')
                    }}</span>
                    <span v-if="row.close_date == null">
                      <sw-badge
                        :bg-color="getStatusHistory('INCOMPLETE').bgColor"
                        :color="getStatusHistory('INCOMPLETE').color"
                      >
                        {{ $t('core_pos.incomplete') }}
                      </sw-badge>
                    </span>
                    <span v-else>
                      <sw-badge
                        :bg-color="getStatusHistory('COMPLETE').bgColor"
                        :color="getStatusHistory('COMPLETE').color"
                      >
                        {{ $t('core_pos.complete') }}
                      </sw-badge>
                    </span>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
    <!-- INCOME/ WITHDRAWAL CASH  -->
    <sw-card class="flex flex-col mt-3">
      <div class="tabs mb-5 grid col-span-12 pt-6">
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck5"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck5"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('core_pos.income_withdrawal_cash', 2) }}
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
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchDataIncomeWithdrawalCash"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.cash_register')"
                  :filterable="true"
                  show="cash_register_name"
                >
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.cash_register') }}</span>
                    <router-link
                      :to="{
                        path: `/admin/corePOS/cash-register/${row.cash_register_id}/view`,
                      }"
                      class="font-medium text-primary-500"
                    >
                      {{ row.cash_register_name }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.reference')"
                  :filterable="true"
                  show="ref"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.created_at')"
                  :filterable="true"
                  show="created_at"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.user_name')"
                  :filterable="true"
                  show="user_name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.amount')"
                  :filterable="true"
                  show="amount"
                >
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.amount, defaultCurrency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('core_pos.open_close_cash_modal.status')"
                  :filterable="true"
                >
                  <template slot-scope="row">
                    <span>{{
                      $t('core_pos.open_close_cash_modal.status')
                    }}</span>
                    <span>
                      <sw-badge
                        :bg-color="getStatusIncomeWithdrawal(row.type).bgColor"
                        :color="getStatusIncomeWithdrawal(row.type).color"
                      >
                        {{ $t(getStatusIncomeWithdrawal(row.type).text) }}
                      </sw-badge>
                    </span>
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
import { mapActions, mapGetters } from 'vuex'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import DollarIcon from '../../../components/icon/DollarIcon'
import ContactIcon from '../../../components/icon/ContactIcon'
import InvoiceIcon from '../../../components/icon/InvoiceIcon'
import EstimateIcon from '../../../components/icon/EstimateIcon'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'
import { CheckCircleIcon, XCircleIcon } from '@vue-hero-icons/outline'
import AstronautIcon from '../../../components/icon/AstronautIcon'

export default {
  components: {
    AstronautIcon,
    ChevronDownIcon,
    PlusSmIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    CheckCircleIcon,
    XCircleIcon,
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      total_amount_invoices: 0,
      quantity_invoices: 0,
      quantity_payments: 0,
      quantity_cash_register: 0,
      fetch_cash_histories: {},
    }
  },
  async created() {
    const response = await window.axios('/api/v1/core-pos/dashboard')

    this.total_amount_invoices = response.data.data.invoices_amount
    this.quantity_invoices = response.data.data.quantity_invoices
    this.quantity_payments = response.data.data.quantity_payments
    this.quantity_cash_register = response.data.data.quantity_cash_register

    this.fetch_cash_histories = response.data.data.cash_histories
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency']),
  },

  methods: {
    getStatusHistory(status) {
      switch (status) {
        case 'COMPLETE':
          return {
            bgColor: '#D5EED0',
            color: '#276749',
          }
        case 'INCOMPLETE':
          return {
            bgColor: '#E1E0EA',
            color: '#1A1841',
          }
      }
    },

    getStatusIncomeWithdrawal(status) {
      switch (status) {
        case 'I':
          return {
            bgColor: '#D5EED0',
            color: '#276749',
            text: 'core_pos.income',
          }
        case 'R':
          return {
            bgColor: '#E1E0EA',
            color: '#1A1841',
            text: 'core_pos.withdrawal',
          }
      }
    },

    async fetchCashHistories({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      let resCashHistory = await window.axios.post(
        '/api/v1/dashboard-pos/cash-histories',
        data
      )
      return {
        data: resCashHistory.data.data.data,
        pagination: {
          totalPages: resCashHistory.data.data.last_page,
          currentPage: page,
          count: resCashHistory.data.data.total,
        },
      }
    },

    async fetchDataIncomeWithdrawalCash({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      let resIncomeWithdrawalCash = await window.axios.post(
        '/api/v1/dashboard-pos/income-withdrawal',
        data
      )

      return {
        data: resIncomeWithdrawalCash.data.data.data,
        pagination: {
          totalPages: resIncomeWithdrawalCash.data.data.last_page,
          currentPage: page,
          count: resIncomeWithdrawalCash.data.data.total,
        },
      }
    },
  },
}
</script>
