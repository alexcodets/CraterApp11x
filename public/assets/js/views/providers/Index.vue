<template>
  <base-page class="customer-create">
    <sw-page-header :title="$t('providers.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
        <sw-breadcrumb-item
          :title="$tc('providers.provider', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalProviders"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="providers/create"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('providers.new_provider') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('providers.titl')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.title"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('providers.first_name')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.first_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('providers.last_name')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.last_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('providers.email')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.email"
            type="text"
            name="name"
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
      :title="$t('providers.no_providers')"
      :description="$t('providers.list_of_providers')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/providers/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('providers.add_new_provider') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ providers.length }}</b>
          {{ $t('general.of') }} <b>{{ totalProviders }}</b>
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
          :label="$t('providers.prov_number')"
          show="providers_number"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.prov_number') }}</span>
            <span> {{ row.providers_number }}
            </span>
          </template>
        </sw-table-column>   

        <sw-table-column
          :sortable="true"
          :label="$t('providers.titl')"
          show="title"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.titl') }}</span>
            <router-link
              :to="{ path: `providers/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.title }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('providers.first_name')"
          show="first_name"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.first_name') }}</span>
            <span> {{ row.first_name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('providers.last_name')"
          show="last_name"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.last_name') }}</span>
            <span> {{ row.last_name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('providers.email')"
          show="email"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.email') }}</span>
            <span class="boxtext"> {{ row.email }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('providers.phone')"
          show="phone"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.phone') }}</span>
            <span> {{ row.phone }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('providers.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`providers/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`providers/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeProvider(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </base-page>
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
        title: '',
        first_name: '',
        last_name: '',
        email: '',
      },
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalProviders && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('providers', [
      'providers',
      'totalProviders',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('providers', [
      'fetchProviders',
      'deleteProvider',
    ]),
    ...mapActions('notification', ['showNotification']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        title: this.filters.title,
        first_name: this.filters.first_name,
        last_name: this.filters.last_name,
        email: this.filters.email,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchProviders(data)
      console.log('fetchProviders', response);
      this.isRequestOngoing = false

      return {
        data: response.data.providers.data,
        pagination: {
          totalPages: response.data.providers.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        title: '',
        first_name: '',
        last_name: '',
        email: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeProvider(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteProvider({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('providers.deleted_message', 1))
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
