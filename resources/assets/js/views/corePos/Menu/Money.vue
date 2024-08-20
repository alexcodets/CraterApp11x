<template>
  <div>
    <div>
      <sw-page-header :title="$t('core_pos.money')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('core_pos.money', 2)"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          @click="toggleFilter"
          size="lg"
          type="button"
          variant="primary-outline"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button size="lg" class=" ml-2" variant="primary" @click="addMoney">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t("core_pos.add_money") }}
        </sw-button>
      </template>
    </sw-page-header>

      <!-- <sw-divider class="mt-6 mb-8" />
      <h1 class="ml-2"> {{ $t("core_pos.money") }} </h1>

      <div class="flex flex-wrap justify-end mt-4 lg:flex-nowrap">

        <sw-button @click="toggleFilter" size="lg" type="button" variant="primary-outline">
          {{ $t("general.filter") }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button size="lg" class=" ml-2" variant="primary" @click="addMoney">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t("core_pos.add_money") }}
        </sw-button> -->
      <!-- </div> -->
    </div>
    <!-- Filters 31 Jul Alejo-->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">

        <sw-input-group :label="$t('core_pos.name')" class="flex-1 mt-2 ml-0 lg:ml-6">
          <sw-input v-model="filters.money_name" type="text" class="mt-2" autocomplete="off" />
        </sw-input-group>

        <sw-input-group :label="$t('core_pos.is_coin')" class="flex-1 mt-3 ml-0 lg:ml-6">
          <sw-select v-model="filters.is_coin" :options="is_coin_options" :searchable="true" :show-labels="false"
            :placeholder="$t('core_pos.is_coin')" :allow-empty="false" track-by="value" label="text"
            style="min-width: 300px" />
        </sw-input-group>

        <sw-input-group :label="$t('core_pos.currency')" class="flex-1 mt-2 ml-0 lg:ml-6">
          <!-- <sw-input v-model="filters.currency_name" type="text" class="mt-2" autocomplete="off" /> -->
          <sw-select v-model="filters.currency_name" :options="currencies_option" :searchable="true"
            :show-labels="false" :allow-empty="true" class="" track-by="id"
            label="name" :tabindex="11" />
        </sw-input-group>

        <label class="absolute text-sm leading-snug text-black cursor-pointer" style="top: 10px; right: 15px"
          @click="clearFilter">{{ $t("general.clear_all") }}</label>
      </sw-filter-wrapper>

    </slide-y-up-transition>
    <!-- /Filters 31 Jul Alejo-->

    <div>
      <sw-table-component ref="table" :show-filter="false" :data="fetchData" table-class="table">
        <sw-table-column :sortable="true" :label="$t('core_pos.name')" sort-as="name" show="name" />
        <sw-table-column :sortable="true" :label="$t('core_pos.amount')" sort-as="amount" show="amount" />
        <sw-table-column :sortable="true" :label="$t('core_pos.is_coin')" sort-as="is_coin" show="is_coin">
          <template slot-scope="row">
            <p v-if="row.is_coin" class="text-sm  leading-5 text-black non-italic">
              {{ $t('core_pos.is_coin_yes') }}
            </p>
            <p v-else class="text-sm  leading-5 text-black non-italic">
              {{ $t('core_pos.is_coin_no') }}
            </p>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('core_pos.currency')" sort-as="currency_id" show="currency_id">
          <template slot-scope="row">
            <p class="text-sm  leading-5 text-black non-italic">
              {{ getCurrency(row.currency_id ? row.currency_id : 0) }}
            </p>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="false">
          <template slot-scope="row">
            <sw-dropdown>
              <dot-icon slot="activator" />

              <span>
                <sw-dropdown-item @click="updateMoney(row)">
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="deleteMoney(row.id)">
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  Delete
                </sw-dropdown-item>
              </span>

            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { email, required } = require('vuelidate/lib/validators')
import CapsuleIcon from '@/components/icon/CapsuleIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CapsuleIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    ClipboardListIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      currencies_option: {},
      isLoading: false,
      isRequestOngoing: true,
      showSideBar: true,
      email: null,
      ticket_prefix: null,
      showFilters: false,
      filters: {
        money_name: '',
        amount: '',
        is_coin: {},
        currency_name: {},
      },
      is_coin_options: [
        {
          text: "Si",
          value: 1,
        },
        {
          text: "No",
          value: 0,
        }
      ]
    }
  },

  computed: {
    ...mapGetters('customer', ['customers']),
    ...mapGetters('users', ['users']),
    ...mapGetters(['currencies']),
    ...mapGetters('ticketDepartament', ['departaments']),

    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  async mounted() {
  },

  destroyed() { },

  async created() {
    this.loadData()
  },

  validations: {
    email: {
      required,
      email,
    },
    ticket_prefix: {
      required,
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('company', ['updateCompanySettings']),
    // ...mapActions('corePos', ['getMoney']),

    ...mapActions('ticketDepartament', [
      'fetchDepartaments',
      'fetchDepartament',
    ]),
    ...mapActions('users', ['fetchUsers']),
    ...mapActions(['fetchCurrencies']),
    
    async loadData(){
      let responseCurrencies = await this.fetchCurrencies()
      this.currencies_option = responseCurrencies.data.currencies
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        money_name: this.filters.money_name == null ? "" : this.filters.money_name,
        amount: this.filters.amount,
        is_coin: this.filters.is_coin.value,
        currency_name: Object.keys(this.filters.currency_name).length  === 0 ? "" : String(this.filters.currency_name.name),
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      //
      let res = await window.axios.post(`/api/v1/core-pos/money/getMoney`, data)
      // let res = await this.getMoney(data)
      return {
        data: res.data.pos_money.data,
        pagination: {
          totalPages: res.data.pos_money.last_page,
          currentPage: page,
        },
      }
    },

    clearFilter() {
      this.filters = {
        money_name: "",
        amount: "",
        is_coin: "",
        currency_id: "",
        currency_name: "",
      }
    },

    setFilters() {
      this.refreshTable()
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async addMoney() {
      this.openModal({
        title: 'Create Money',
        componentName: 'MoneyModal',
        refreshData: this.$refs.table.refresh,
      })
    },

    async updateMoney(data) {
      this.openModal({
        title: 'Edit Money',
        componentName: 'MoneyModal',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    async deleteMoney(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: 'You will not be able to retrieve this record',
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await window.axios.get(`/api/v1/core-pos/money/deleteMoney/${id}`)

          if (res.data.success) {
            window.toastr['success'](res.data.message)
            this.$refs.table.refresh()
            return true
          }

          window.toastr['error']('Error')
          return true
        }
      })
    },

    getCurrency(id) {
      let currency = this.currencies.filter((currency) => currency.id == id);
      return currency.length > 0 ? currency[0].name : "None"
    },

    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
    },

  },
}
</script>