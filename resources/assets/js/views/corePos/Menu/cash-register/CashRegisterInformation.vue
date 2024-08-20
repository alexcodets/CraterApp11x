<template>
  <base-page class="customer-create">
    <sw-page-header class="mb-3" :title="$tc('core_pos.cash_register')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/cash-register" :title="$tc('core_pos.cash_register', 2)" />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button tag-name="router-link" :to="`/admin/corePOS/cash-register`" class="mr-3" variant="primary-outline">
          {{ $t('general.back') }}
        </sw-button>

        <sw-button tag-name="router-link" :to="`/admin/corePOS/create-cash-register/${cash_register.id}/edit`"
          class="mr-3" variant="primary-outline">
          {{ $t('general.edit') }}
        </sw-button>

        <sw-dropdown position="bottom-end">
          <sw-button slot="activator" class="mr-3" variant="primary">
            {{ $t('customers.new_transaction') }}
          </sw-button>
          <div v-if="permissionModule.accessOpenCloseCashRegister">

            <sw-dropdown-item v-if="status_cash_register == 0" @click="cashRegisterModal">
              <check-circle-icon class="h-5 mr-1 text-gray-600" />
            {{ $t('core_pos.open_cash_register') }}
          </sw-dropdown-item>
          <sw-dropdown-item v-else @click="cashRegisterModal">
            <x-circle-icon class="h-5 mr-1 text-gray-600" />
            {{ $t('core_pos.close_cash_register') }}
          </sw-dropdown-item>
        </div>

          <sw-dropdown-item v-if="status_cash_register && permissionModule.accessIncomeWithdrawalCashRegister" @click="openModalIncomeCash">
            <trending-down-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('core_pos.title_income_cash') }}
          </sw-dropdown-item>
          <sw-dropdown-item v-if="status_cash_register && permissionModule.accessIncomeWithdrawalCashRegister" @click="openModalWithdrawalCash">
            <trending-up-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('core_pos.title_withdrawal_cash') }}
          </sw-dropdown-item>
          <sw-dropdown-item tag-name="router-link" :to="`/admin/corePOS/cash-register/${this.$route.params.id}/report`">
            <book-open-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('core_pos.cash_register_report') }}
          </sw-dropdown-item>
        </sw-dropdown>

      </template>
    </sw-page-header>

    <!-- information cash register -->
    <sw-card class="flex flex-col mt-1">
      <div class="">
        <div class="col-span-12">
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('core_pos.basic_info') }}
          </p>
          <div class="grid grid-cols-1 gap-4 mt-5 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
            <div>
              <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800">
                {{ $t('core_pos.user_name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ user.name }}
              </p>
            </div>
            <div>
              <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800">
                {{ $t('core_pos.name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ cash_register.name }}
              </p>
            </div>

            <div>
              <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800">
                {{ $t('core_pos.device') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ cash_register.device }}
              </p>
            </div>
            <div>
              <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800">
                {{ $t('core_pos.description') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ cash_register.description }}
              </p>
            </div>
            <div>
              <p class="mb-1 text-sm font-bold font-normal leading-5 non-italic text-primary-800">
                {{ $t('core_pos.open_close_cash_modal.status') }}
              </p>
              <div>
                <sw-badge :bg-color="getStatus(status_cash_register).bgColor"
                  :color="getStatus(status_cash_register).color">
                  {{ $t(getStatus(status_cash_register).text) }}
                </sw-badge>
              </div>
            </div>
          </div>
        </div>
      </div>
    </sw-card>

    <!-- CASH HISTORY  -->
    <sw-card class="flex flex-col mt-3">
      <div class="tabs mb-5 grid col-span-12 pt-6">
        <div class="border-b tab">
          <div class="relative">
            <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" id="chck5" />
            <header class="col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              " for="chck5">
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('core_pos.cash_history', 2) }}
              </span>
              <div class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                ">
                <!-- icon by feathericons.com -->
                <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                  xmlns="http://www.w3.org/2000/svg">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content-customer">
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component ref="table" :show-filter="false" :data="fetchData" table-class="table">
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.reference')"
                  :filterable="true" show="ref" />
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.open_date')"
                  :filterable="true" show="open_date" />
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.cash_received')"
                  :filterable="true" show="cash_received">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.cash_received') }}</span>
                    <div v-html="$utils.formatMoney(row.cash_received * 100, user.currency)" />
                  </template>
                </sw-table-column>
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.initial_amount')"
                  :filterable="true" show="initial_amount">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.initial_amount') }}</span>
                    <div v-html="$utils.formatMoney(row.initial_amount * 100, user.currency)" />
                  </template>
                </sw-table-column>
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.other_income')"
                  :filterable="true" show="other_income">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.other_income') }}</span>
                    <div v-html="$utils.formatMoney(row.other_income * 100, user.currency)" />
                  </template>
                </sw-table-column>
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.close_date')"
                  :filterable="true" show="close_date">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.close_date') }}</span>
                    <span>{{ row.close_date != null ? row.close_date : 'N/A' }}</span>
                  </template>
                </sw-table-column>
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.final_amount')"
                  :filterable="true" show="final_amount">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.final_amount') }}</span>
                    <div v-html="$utils.formatMoney(row.final_amount * 100, user.currency)" />
                  </template>
                </sw-table-column>
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.status')" :filterable="true">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.status') }}</span>
                    <span v-if="row.close_date == null">
                      <sw-badge :bg-color="getStatusHistory('INCOMPLETE').bgColor"
                        :color="getStatusHistory('INCOMPLETE').color">
                        {{ $t('core_pos.incomplete') }}
                      </sw-badge>
                    </span>
                    <span v-else>
                      <sw-badge :bg-color="getStatusHistory('COMPLETE').bgColor"
                        :color="getStatusHistory('COMPLETE').color">
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

    <!-- INCOME/WITHDRAWAL CASH  -->
    <sw-card class="flex flex-col mt-3">
      <div class="tabs mb-5 grid col-span-12 pt-6">
        <div class="border-b tab">
          <div class="relative">
            <input class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4" type="checkbox" id="chck5" />
            <header class="col-span-5
                  flex
                  justify-between
                  items-center
                  py-3
                  cursor-pointer
                  select-none
                  tab-label
                " for="chck5">
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('core_pos.income_withdrawal_cash', 2) }}
              </span>
              <div class="
                    rounded-full
                    border border-grey
                    w-7
                    h-7
                    flex
                    items-center
                    justify-center
                    test
                  ">
                <!-- icon by feathericons.com -->
                <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                  xmlns="http://www.w3.org/2000/svg">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content-customer">
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component ref="table" :show-filter="false" :data="fetchDataIncomeWithdrawalCash"
                table-class="table">
                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.reference')"
                  :filterable="true" show="ref" />


                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.created_at')"
                  :filterable="true" show="created_at" />

                <sw-table-column :sortable="true" :label="$t('core_pos.user_name')" :filterable="true" show="user_name" />


                <sw-table-column :sortable="true" :label="$t('core_pos.amount')" :filterable="true" show="amount">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.amount') }}</span>
                    <div v-html="$utils.formatMoney(row.amount, user.currency)" />
                  </template>
                </sw-table-column>


                <sw-table-column :sortable="true" :label="$t('core_pos.open_close_cash_modal.status')" :filterable="true">
                  <template slot-scope="row">
                    <span>{{ $t('core_pos.open_close_cash_modal.status') }}</span>
                    <span>
                      <sw-badge :bg-color="getStatusIncomeWithdrawal(row.type).bgColor"
                        :color="getStatusIncomeWithdrawal(row.type).color">
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
import moment from 'moment'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'
import { CheckCircleIcon, XCircleIcon, TrendingDownIcon, TrendingUpIcon, BookOpenIcon } from "@vue-hero-icons/outline"
export default {
  components: {
    BookOpenIcon,
    ChevronDownIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    CheckCircleIcon,
    XCircleIcon,
    TrendingDownIcon,
    TrendingUpIcon
  },
  data() {
    return {
      cash_register: {},
      status_cash_register: 0,
      user: {},
      cash_history_id: null,
      cash_history: {},
      permissionModule: {
        accessOpenCloseCashRegister: false,
        accessIncomeWithdrawalCashRegister: false
      }
    }
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  created() {
    this.loadData()
    this.permissionsUserModule()
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency', 'defaultCurrencyForInput']),
  },
  methods: {
    ...mapActions('modal', ['openModal', 'closeModal']),
    ...mapActions('user', ['getUserModules']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async loadData() {
      let cashRegisterId = this.$route.params.id
      let resCashRegister = await window.axios.get(
        `/api/v1/core-pos/cash-register/getCashRegister/${cashRegisterId}`
      )
      this.cash_register = resCashRegister.data.cash_register
      this.user = resCashRegister.data.users[0]
      this.status_cash_register = resCashRegister.data.cash_register.open_cash
      this.cash_history_id =
        resCashRegister.data.last_cash_register != null
          ? resCashRegister.data.last_cash_register.id
          : null

      this.cash_history = resCashRegister.data.last_cash_register
    },

    setFilters() {
      this.refreshTable()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        cashRegisterId: this.$route.params.id,
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      let resCashHistory = await window.axios.get(
        `/api/v1/core-pos/get-cash-history`,
        { params: data }
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
        cash_register_id: this.$route.params.id,
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      let resIncomeWithdrawalCash = await window.axios.get(
        `/api/v1/core-pos/show-income-withdrawal`,
        { params: data }
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

    getStatus(isOpen) {
      switch (isOpen) {
        case 1:
          return {
            bgColor: '#D5EED0',
            color: '#276749',
            text: 'core_pos.open_close_cash_modal.opened',
          }
        case 0:
          return {
            bgColor: '#FED7D7',
            color: '#c53030',
            text: 'core_pos.open_close_cash_modal.closed',
          }
        default:
          return {
            bgColor: '#FED7D7',
            color: '#c53030',
            text: 'core_pos.open_close_cash_modal.closed',
          }
      }
    },

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
            text: 'core_pos.income'
          }
        case 'R':
          return {
            bgColor: '#E1E0EA',
            color: '#1A1841',
            text: "core_pos.withdrawal"
          }
      }
    },

    formatDate(row) {
      return moment(row.created_at).format('YYYY-MM-DD HH:mm:ss')
    },

    deleteCashRegister() {
      //console.log('delete')
    },

    openModalIncomeCash() {
      const data = {
        type: 'I',
        cash_register_id: this.cash_register.id,
        cash_histories_id: this.cash_history_id,
      }
      this.openModal({
        title: this.$t('core_pos.title_income_cash'),
        componentName: 'IncomeWithdrawalCashModal',
        data: data,
      })
    },

    openModalWithdrawalCash() {
      const data = {
        type: 'R',
        cash_register_id: this.cash_register.id,
        cash_histories_id: this.cash_history_id,
      }
      this.openModal({
        title: this.$t('core_pos.title_withdrawal_cash'),
        componentName: 'IncomeWithdrawalCashModal',
        data: data,
      })
    },

    cashRegisterModal() {
      let title = 'core_pos.open_cash_register'

      const data = {
        cash_history: this.cash_history != null ? [this.cash_history] : [],
        id: this.cash_register.id
      }

      if (this.status_cash_register == 1) {
        title = 'core_pos.close_cash_register'
      }

      this.openModal({
        title: this.$t(title),
        componentName: 'openCloseCashRegisterModal',
        data: data
      })
    },

    async permissionsUserModule() {
      const data = {
        module: "open_close_cash_register",
      };
      const permissions = await this.getUserModules(data);
      // valida que el usuario tenga el permiso access
      if (permissions.super_admin == true) {
        this.permissionModule.accessOpenCloseCashRegister = true;
      } else if (permissions.exist == true && permissions.permissions[0] != null) {
        const modulePermissions = permissions.permissions[0];
        if (modulePermissions.access == 1) {
          this.permissionModule.accessOpenCloseCashRegister = true;
        }
      }
      const dataIncomeWithdrawal = {
        module: "income_withdrawal_cash",
      };
      const permissionsIncomeWithdrawal = await this.getUserModules(dataIncomeWithdrawal);
      // valida que el usuario tenga el permiso access
      if (permissionsIncomeWithdrawal.super_admin == true) {
        this.permissionModule.accessIncomeWithdrawalCashRegister = true;
      } else if (permissionsIncomeWithdrawal.exist == true && permissionsIncomeWithdrawal.permissions[0] != null) {
        const modulePermissions = permissionsIncomeWithdrawal.permissions[0];
        if (modulePermissions.access == 1) {
          this.permissionModule.accessIncomeWithdrawalCashRegister = true;
        }
      }
    },
  },
}
</script>
