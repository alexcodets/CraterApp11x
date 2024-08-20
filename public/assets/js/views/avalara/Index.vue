<template>
  <base-page v-if="isSuperAdmin" class="items">

    <sw-page-header :title="$t('avalara.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="#" :title="$tc('avalara.title', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalAvalaraConfigs"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="/admin/avalara/config/logs"
          variant="primary"
          class="ml-4"
        >
          <eye-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('avalara.logs') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="/admin/avalara/config/create"
          variant="primary"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('avalara.add_new_config') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group
          :label="$tc('avalara.filter.conexion')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.conexion"
            type="text"
            name="conexion"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('avalara.filter.user_name')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.user_name"
            type="text"
            name="user_name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('avalara.no_avalara_config')"
      :description="$t('avalara.list_of_avalara_configs')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/avalara/config/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('avalara.add_new_config') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
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
      </div>


      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.id')"
          show="hg"
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.item.id') }}</span>
            <router-link
              :to="{ path: `/admin/avalara/config/${row.id}/edit` }"
              class="font-medium text-primary-500"
            >
              {{ row.id }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.connection')"
          show="conexion"
        >
        <template slot-scope="row">            
            {{ row.conexion == 'production' ? $t('avalara.conexion_production') : $t('avalara.conexion_sandbox') }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.user_name')"
          show="user_name"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.item.status') }}</span>
            <sw-badge
              :bg-color="
                $utils.getBadgeStatusColor(row.status == 'A' ? 'A' : 'I').bgColor
              "
              :color="
                $utils.getBadgeStatusColor(row.status == 'A' ? 'A' : 'I').color
              "
            >
              {{ row.status == 'A' ? 'Active' : 'Inactive' }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.client_id')"
          show="client_id"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.url')"
          show="url"
        />

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('general.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />

             <!--  <sw-dropdown-item
                tag-name="router-link"
                :to="`config/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item> -->

              <sw-dropdown-item
                tag-name="router-link"
                :to="`/admin/avalara/config/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }} </sw-dropdown-item>
              <sw-dropdown-item @click="removeAvalaraConfig(row.id)">
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
import AstronautIcon from '@/components/icon/AstronautIcon'
import {
  EyeIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon
} from '@vue-hero-icons/solid'

export default {
  components: {
    EyeIcon,
    AstronautIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      filters: {
        conexion: '',
      },
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
    showEmptyScreen() {
      return !this.totalAvalaraConfigs && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedAvalaraConfigs
      },
      set: function (val) {
        this.selectedUser(val)
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    },
  },
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllAvalaraConfigs()
    }
  },

  methods: {
    ...mapActions('avalara', [
      'fetchAvalaraConfigs',
      'selectAvalaraConfig',
      'resetSelectedAvalaraConfigs',
      'selectAllAvalaraConfigs',
      'deleteAvalaraConfig',
      'cloneAvalaraConfig',
      'setSelectAllState',
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        conexion: this.filters.conexion !== null ? this.filters.conexion : '',
        user_name:
          this.filters.user_name !== null ? this.filters.user_name : '',
        client_id:
          this.filters.client_id !== null ? this.filters.client_id : '',
        url: this.filters.url !== null ? this.filters.url : '',
        host: this.filters.host !== null ? this.filters.host : '',
        status: this.filters.status !== null ? this.filters.status : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true

      let response = await this.fetchAvalaraConfigs(data)

      this.isRequestOngoing = false

      return {
        data: response.data.list.data,
        pagination: {
          totalPages: response.data.list.last_page,
          currentPage: page,
        },
      }
    },

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
        email: '',
        phone: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeAvalaraConfig(id) {
      let pack = []
      pack.push(id)

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('avalara.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteAvalaraConfig(pack)
          console.log('delete', res);
          if (res.data.success) {
            window.toastr['success'](this.$tc('avalara.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('avalara.user_attached_message'),
              this.$t('general.action_failed')
            )
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
