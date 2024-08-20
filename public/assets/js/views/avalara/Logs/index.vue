<template>
  <base-page v-if="isSuperAdmin" class="items">

    <sw-page-header :title="$t('avalara.avalara_logs')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="/admin/avalara/configs" :title="$tc('avalara.title', 2)" />
        <sw-breadcrumb-item to="#" :title="$t('avalara.logs')" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          variant="primary-outline"
          size="lg"
          @click="showFilters = !showFilters"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper
         v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >
        <sw-input-group :label="$tc('customers.customer', 1)" class="mt-2">
          <base-customer-select
            ref="customerSelect"
            @select="onSelectCustomer"
            @deselect="clearCustomerSearch"
          />
        </sw-input-group>
      <sw-input-group :label="$t('general.from')" class="mt-2 xl:ml-8">
          <base-date-picker
            v-model="filters.from_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
            @input="searchFilter()"
          />
        </sw-input-group>

          <div
          class="
            hidden
            w-8
            h-0
            mx-4
            border border-gray-400 border-solid
            xl:block
          "
          style="margin-top: 3.5rem"
        />

        <sw-input-group :label="$t('general.to')" class="mt-2 xl:ml-8">
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
            @input="searchFilter()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('invoices.invoice_number')"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.invoice_number" @input="searchFilter()">
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
        </sw-input-group>

        <sw-input-group
          :label="$t('avalara.pbx_services')"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.pbx_services_number" @input="searchFilter()">
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          @click="clearFilter"
          style="top: 10px; right: 15px"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>


    <sw-empty-table-placeholder
      v-if="!showEmptyScreen && lenght === 0"
      :title="$t('avalara.no_avalara_logs')"
      :description="$t('avalara.list_of_avalara_logs')"
    >
      <astronaut-icon class="mt-5 mb-4" />
    </sw-empty-table-placeholder>

    <div class="relative table-container" >
      <!-- <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ avalaraConfigs.length }}</b>

          {{ $t('general.of') }}

          <b>{{ totalAvalaraConfigs }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedAvalaraConfigs.length">
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleAvalaraConfigs">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div> -->


      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
      <!-- payload
      invoices
      pbx_services
      customers
      date -->

        <!-- payload -->
        <sw-table-column
          :sortable="true"
          :label="$t('avalara.payload')"
          
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.payload') }}</span>
            <sw-button
              variant="primary-outline"
              @click="showModalJson(row.response)"
            >
              <eye-icon class="w-4 h-4 mr-1 -ml-2" />
              {{ $t('avalara.json') }}
            </sw-button>
          </template>
        </sw-table-column>

        <!-- invoice -->
        <sw-table-column
          :sortable="true"
          :label="$t('avalara.invoice')"
          
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.invoice') }}</span>
            <router-link
              v-if="row.invoice"
              :to="row.invoice.deleted_at == null ? { path: `/admin/invoices/${row.invoice.id}/view` } : ''"
              class="font-medium text-primary-500"
            >
              {{ row.invoice.invoice_number }}
            </router-link>
          </template>
        </sw-table-column>

        <!-- pbx_services -->
        <sw-table-column
          :sortable="true"
          :label="$t('avalara.pbx_services')"
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.pbx_services') }}</span>
            <router-link
              v-if="row.pbx_service_id !== null"
              :to="row.pbx_service.deleted_at == null ? { path: `/admin/customers/${row.customer.id}/pbx-service/${row.pbx_service.id}/view` } : ''"
              class="font-medium text-primary-500"
            >
              {{ row.pbx_service.pbx_services_number }}
            </router-link>
            <p v-else>
              N/A
            </p>
          </template>
        </sw-table-column>

        <!-- Customer -->
        <sw-table-column
          :sortable="true"
          :label="$t('avalara.customer')"
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.customer') }}</span>
            <router-link
              v-if="row.customer"
              :to="row.customer.deleted_at == null ? { path: `admin/customers/${row.customer.id}/view` } : '#'"
              class="font-medium text-primary-500"
            >
              {{ row.customer.customcode }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.date')"
          show="formattedCreatedAt"
        />
      </sw-table-component>

      <sw-modal ref="seenJsonModal" variant="primary">
        <template v-slot:header>
          <div
            class="absolute flex content-center justify-center w-5 cursor-pointer"
            style="top: 20px; right: 15px"
            @click="closeModal"
          >
            <x-icon />
          </div>
          <span>{{ $t('avalara.seen_json') }}</span>
        </template>
      <!-- {{component}} -->
        <div>
          <!-- jsonPayload -->
          <pre class="text-sm overflow-x-auto px-5 h-full w-full" style="height:450px;">
            <code>{{ jsonPayload }}</code>
          </pre>
        </div>
      </sw-modal>


    </div>
  </base-page>
</template>

<script>
const _ = require('lodash');
import AstronautIcon from '@/components/icon/AstronautIcon'
import { mapActions, mapGetters } from 'vuex'
import {
  EyeIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    EyeIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
    AstronautIcon,
    HashtagIcon
  },

  data: () => ({
    showFilters: false,
    filters: {
        customer: '',
        from_date: '',
        to_date: '',
        invoice_number: '',
        pbx_services_number: '',
      },
    showEmptyScreen: false,
    lenght: 0,
    jsonPayload: '',
  }),
  watch: {
    showFilters(val){
      if(!val){
        this.clearFilter()
      }
    }
  },


  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('avalara', [
      'selectedAvalaraConfigs',
      'totalAvalaraConfigs',
      'avalaraConfigs',
      'selectAllField',
    ]),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('avalara', [
      'fetchAvalaraLogs',
    ]),
    async fetchData( { page, sort } ) {
      try {
        this.showEmptyScreen = true

        const filters = this.filters

        const data = {
          ...filters,
          orderByField: sort.fieldName || 'created_at',
          orderBy: sort.order || 'desc',
          page : page || 1,
        }

        console.log('data:', data);
        const response = await this.fetchAvalaraLogs(data)
        this.lenght = response.data.total
        return {
          data: response.data.data,
          pagination: {
            totalPages: response.data.last_page,
            currentPage: page,
          },
        }
        
      } catch (error) {
        console.log(error)
      }finally{
        this.showEmptyScreen = false
      }      
    },
    showModalJson(value) {
      this.jsonPayload = JSON.stringify(JSON.parse(value), null, 2)
      this.showModal()
    },
    clearFilter() {
      this.filters = {
        customer: '',
        from_date: '',
        to_date: '',
        invoice_number: '',
        pbx_services_number: '',
      }
      this.searchFilter()
    },
    onSelectCustomer(customer) {
      this.filters.customer = customer.id
      this.searchFilter()
    },
    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.searchFilter()
    },
    searchFilter() {
      this.$refs.table.refresh()
    },
    closeModal(){
      this.$refs.seenJsonModal.close()
    },
    showModal(){
       this.$refs.seenJsonModal.open()
    }

    
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    // use debounce to prevent fetching data on every keyup
    this.searchFilter = _.debounce(this.searchFilter, 600)
  },

}
</script>

<style>

</style>