<template>
  <sw-card variant="setting-card">
  <base-page class="customer-create">
    <sw-page-header :title="$t('authorize.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('settings.payment_gateways.title')" to="/admin/settings/payment-gateways" />
        <sw-breadcrumb-item
          :title="$tc('authorize.title', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalAuthorizations"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>
        
        <sw-button
          tag-name="router-link"
          to="authorize/create"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('authorize.new_authorize') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">

        <sw-input-group
          :label="$t('authorize.login_id')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.login_id"
            type="text"
            name="login_id"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('authorize.test_mode')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.test_mode"
            type="text"
            name="test_mode"
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
      :title="$t('authorize.no_authorize')"
      :description="$t('authorize.list_of_authorize')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/settings/authorize/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('authorize.add_new_authorize') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ authorizations.length }}</b>
          {{ $t('general.of') }} <b>{{ totalAuthorizations }}</b>
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
          :filterable="true"
          :label="$t('authorize.login_id')"
          show="login_id"
        >
          <template slot-scope="row">
            <span>{{ $t('authorize.login_id') }}</span>
            <router-link
              :to="{ path: `authorize/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.login_id }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('authorize.test_mode')"
          show="test_mode"
        >
          <template slot-scope="row">
            <span>{{ $t('authorize.test_mode') }}</span>
            <span v-if="row.test_mode">
              {{
                $t('authorize.yes')
              }}
            </span>
            <span v-else>
              {{
                $t('authorize.no')
              }}
            </span>
          </template>
        </sw-table-column>

        <!-- <sw-table-column
          :sortable="true"
          :label="$t('authorize.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('authorize.status') }}</span>
            
              <div class="relative w-12">
                <sw-switch
                  v-model="row.status"
                  class="absolute"
                  style="top: -20px"
                />
              </div>
          </template>
        </sw-table-column> -->

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $tc('authorize.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`authorize/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`authorize/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeAuthorize(row.id)">
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
      filters: {
        currency: '',
        payment_API: '',
        payment_account_validation_mode: '',
        test_mode: '',
        developer_mode: '',
      },
      data: {
        currency: '',
        developer_mode: '',
        id: '',
        login_id: '',
        payment_API: '',
        payment_account_validation_mode: '',
        status: '',
        test_mode: '',
      }
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalAuthorizations && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('authorizations', [
      'authorizations',
      'totalAuthorizations',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('authorizations', [
      'fetchAuthorizations',
      'deleteAuthorization',
      'updateAuthorizeStatus',
    ]),
    ...mapActions('notification', ['showNotification']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {

      this.isRequestOngoing = true
      let response = await this.fetchAuthorizations()
      this.isRequestOngoing = false

      this.data = response.data.authorize.data

      return {
        data: response.data.authorize.data,
        pagination: {
          totalPages: response.data.authorize.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        login_id: '',
        test_mode: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) this.clearFilter()
      this.showFilters = !this.showFilters
    },
    
    async updateStatus() {
      let i = 0      
      this.data.forEach(data => {
        if (data.status == true) {
          i++
        }
      });
      if (i > 1) {
        window.toastr['error'](this.$tc('authorize.status_error'))
      } 
      if (i < 1) {
        window.toastr['error'](this.$tc('authorize.status_select'))
      }
      if (i == 1) {
        let response = await this.updateAuthorizeStatus(this.data)
        window.toastr['success'](this.$tc('authorize.success_status'))
        location.reload()
      }
    },

    async selectStatus(id, row) {
      this.data.forEach(data => {
        if (data.id === id) {
          data.status = row.status
        }
      });
    },
    async removeAuthorize(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('authorize.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteAuthorization({ id })

          if (res.status === 200) {
            window.toastr['success'](this.$tc('authorize.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },
  },
}
</script>
