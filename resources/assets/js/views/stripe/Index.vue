<template>
  <sw-card variant="setting-card">
    <base-page class="customer-create">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <sw-page-header :title="$t('stripe.title')">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item
              :title="$t('settings.payment_gateways.title')"
              to="/admin/settings/payment-gateways"
            />
            <sw-breadcrumb-item :title="$tc('stripe.title', 2)" to="#" active />
          </sw-breadcrumb>
        </sw-page-header>

        <div class="flex flex-wrap items-center justify-end">
          <sw-button
            size="lg"
            variant="primary-outline"
            class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
            <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
          </sw-button>

          <sw-button
            @click="updateStatus"
            size="lg"
            variant="primary"
            class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          >
            {{ $t('stripe.update_status') }}
          </sw-button>

          <sw-button
            tag-name="router-link"
            to="stripe/create"
            size="lg"
            variant="primary"
            class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          >
            <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
            {{ $t('stripe.new_stripe') }}
          </sw-button>
        </div>
      </div>

      <slide-y-up-transition>
        <sw-filter-wrapper v-show="showFilters" class="mt-3">
          <sw-input-group
            :label="$t('stripe.public_key')"
            class="flex-1 mt-2 mr-4"
          >
            <sw-input
              v-model="filters.public_key"
              type="text"
              name="public_key"
              class="mt-2"
              autocomplete="off"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('stripe.enviroment')"
            class="flex-1 mt-2 mr-4"
          >
            <sw-input
              v-model="filters.enviroment"
              type="text"
              name="enviroment"
              class="mt-2"
              autocomplete="off"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('stripe.currency')"
            class="flex-1 mt-2 mr-4"
          >
            <sw-input
              v-model="filters.currency"
              type="text"
              name="currency"
              class="mt-2"
              autocomplete="off"
            />
          </sw-input-group>

          <label
            class="absolute text-sm leading-snug text-black cursor-pointer"
            style="top: 10px; right: 15px"
            @click="clearFilter"
            >{{ $t('general.clear_all') }}</label
          >
        </sw-filter-wrapper>
      </slide-y-up-transition>

      <sw-empty-table-placeholder
        v-show="showEmptyScreen"
        :title="$t('stripe.no_authorize')"
        :description="$t('stripe.list_of_authorize')"
      >
        <astronaut-icon class="mt-5 mb-4" />

        <sw-button
          slot="actions"
          tag-name="router-link"
          to="stripe/create"
          size="lg"
          variant="primary-outline"
        >
          {{ $t('stripe.add_new_stripe') }}
        </sw-button>
      </sw-empty-table-placeholder>

      <div v-show="!showEmptyScreen" class="relative table-container">
        <div
          class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
        >
          <p class="text-sm">
            {{ $t('general.showing') }}: <b>{{ stripesList.length }}</b>
            {{ $t('general.of') }} <b>{{ totalStripes }}</b>
          </p>
        </div>

        <sw-table-component
          ref="table"
          :show-filter="false"
          :data="fetchData"
          table-class="table"
        >
          <sw-table-column
            :sortable="true"
            :label="$t('stripe.public_key')"
            show="public_key"
          >
            <template slot-scope="row">
              <span>{{ $t('stripe.public_key') }}</span>
              <span v-html="row.public_key"> </span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('stripe.status')"
            show="status"
          >
            <template slot-scope="row">
              <span>{{ $t('stripe.status') }}</span>

              <div class="relative w-12">
                <sw-switch
                  v-model="row.status"
                  class="absolute"
                  style="top: -33px"
                  @change="selectStatus(row.id, row)"
                />
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('stripe.currency')"
            show="currency"
          >
            <template slot-scope="row">
              <span>{{ $t('stripe.currency') }}</span>
              <span v-html="row.currency"> </span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('stripe.enviroment')"
            show="enviroment"
          >
            <template slot-scope="row">
              <span>{{ $t('stripe.enviroment') }}</span>
              <span v-html="row?.environment"></span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown"
          >
            <template slot-scope="row">
              <span> {{ $tc('stripe.action') }} </span>

              <sw-dropdown>
                <dot-icon slot="activator" />

                <sw-dropdown-item
                  :to="`stripe/${row.id}/edit`"
                  tag-name="router-link"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>

                <sw-dropdown-item
                  :to="`stripe/${row.id}/view`"
                  tag-name="router-link"
                >
                  <eye-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.view') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="removeStripe(row.id)">
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.delete') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>
    </base-page>
  </sw-card>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../components/icon/AstronautIcon'

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
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      totalStripes: 0,
      stripesList: [],
      filters: {
        currency: '',
        public_key: '',
        enviroment: '',
      },
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalStripes && !this.isRequestOngoing
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

  methods: {
    ...mapActions('stripes', [
      'fetchStripes',
      'deleteStripe',
      'updateStripe',
    ]),
    ...mapActions('paymentGateways', [
      'fetchPaymentGateways',
      'updatePaymentGatewaysStatus',
      'updatePaymentGatewaysDefault',
      'fetchPaymentGatewaysIndex',
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      this.isRequestOngoing = true
      let data = {
        currency: this.filters.currency || '',
        public_key: this.filters.public_key || '',
        enviroment: this.filters.enviroment || '',
        orderByField: sort && sort.fieldName ? sort.fieldName : 'created_at',
        orderBy: sort && sort.order ? sort.order : 'asc',
        page,
        limit: 10,
      }

      let response = await this.fetchStripes(data)

      let transformedData = await this.transformData(response.data)
      this.totalStripes = response.meta.total
      this.stripesList = transformedData
      response.data = transformedData

      return {
        data: response.data || {},
        pagination: {
          totalPages: response.meta.last_page,
          currentPage: page,
          count: response.meta.total,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        currency: '',
        creator_id: '',
        public_key: '',
        enviroment: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async updateStatus() {
      let i = 0
      this.stripesList.forEach((data) => {
        if (data.status == true) {
          i++
        }
      })
      if (i > 1) {
        window.toastr['error'](this.$tc('stripe.status_error'))
        this.refreshTable()
      }
      if (i < 1) {
        window.toastr['error'](this.$tc('stripe.status_select'))
        this.refreshTable()
      }
      if (i == 1) {
        window.toastr['success'](this.$tc('stripe.success_status'))
        this.refreshTable()
      }
    },

    async selectStatus(id, row) {
      this.stripesList.forEach((data) => {
        if (data.id === id) {
          data.status = row.status
        }
      })
      const payload = {
        status: row.status,
        id: id
      }
      const res = await this.updateStripe(payload)
     // console.log('response =', res);
    //  console.log('payload =', payload);
    },

    async removeStripe(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteStripe(id)
          window.toastr['success'](this.$tc('stripe.deleted_message', 1))
          this.refreshTable()
          return true
        }
      })
    },

    async transformData(data) {
      return data.map((item) => {
        return {
          ...item,
          status: item.status === 'A',
        }
      })
    },
  },
}
</script>
