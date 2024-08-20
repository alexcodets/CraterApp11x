<template>
  <div class="relative">
    <!-- Page Header -->

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('corePbx.custom_did_groups.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('corePbx.corePbx')"
            to="/admin/corePBX"
          />
          <sw-breadcrumb-item
            :title="$tc('corePbx.custom_did_groups.title')"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalCustomDidGroups"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/toll-free`"
          variant="primary-outline"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.read"
        >
          {{ $t('corePbx.custom_did_groups.custom_dids') }}
        </sw-button>

        <sw-button
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="openImportModal"
          v-if="permissionModule.create"
        >
          <upload-icon class="h-4 mr-1 -ml-2 font-bold" />
          {{ $t('general.import') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="custom-did-groups/create"
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('corePbx.custom_did_groups.add') }}
        </sw-button>
      </div>
    </div>

    <!--   Fitros     -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('corePbx.custom_did_groups.name')"
          class="flex-1 mt-2 mr-2"
        >
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('corePbx.custom_did_groups.status')"
          class="flex-1 mt-2 mr-2"
        >
          <sw-select
            v-model="filters.status"
            :options="statusOptionsA"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('corePbx.custom_did_groups.status')"
            label="name"
            class="mt-2"
            @click="filter = !filter"
          />
        </sw-input-group>

        <!--
        <sw-input-group
          :label="$t('corePbx.custom_did_groups.description')"
          class="flex-1 mt-2 mr-2"
        >
          <sw-input
            v-model="filters.description"
            type="text"
            name="description"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>
        --->

        <sw-input-group
          :label="$t('corePbx.custom_did_groups.type')"
          class="flex-1 mt-2"
        >
          <sw-select
            v-model="filters.type"
            :options="typeOptionsA"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('corePbx.custom_did_groups.type')"
            label="name"
            class="mt-2"
            @click="filter = !filter"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t('general.clear_all') }}
        </label>
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <!--   Si la tabla esta vacia     -->
    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('corePbx.custom_did_groups.no_custom_did_groups')"
      :description="$t('corePbx.custom_did_groups.list_of_custom_did_group')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/custom-did-groups/create"
        size="lg"
        variant="primary-outline"
        v-if="permissionModule.create"
      >
        {{ $t('corePbx.custom_did_groups.add_new_custom_did_group') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <!--   Si hay informacion para la tabla     -->
    <div v-show="!showEmptyScreen" class="relative table-container">
      <!-- Fila de utilidades -->
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <!-- Informacion de paginacion -->
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ customDidGroups.length }}</b>
          {{ $t('general.of') }} <b>{{ totalCustomDidGroups }}</b>
        </p>

        <!-- Dropdown para eliminar multiples grupos -->
        <sw-transition type="fade">
          <sw-dropdown v-if="selectedCustomDidGroups.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item
              @click="'removeMultipleItemGroups'"
              v-if="permissionModule.delete"
            >
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <!-------------------------- Tabla -------------------------->
      <base-loader v-if="isLoadingDelete" />
      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.custom_did_groups.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.custom_did_groups.name') }}</span>
            <router-link
              :to="{ path: `custom-did-groups/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.name }}
            </router-link>
            <span v-else>
              {{ row.name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.custom_did_groups.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.custom_did_groups.status') }}</span>
            <div v-if="row.status == 'A'">
              <sw-badge
                :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                :color="$utils.getBadgeStatusColor('COMPLETED').color"
                class="px-3 py-1"
              >
                {{ $t('general.active') }}
              </sw-badge>
            </div>
            <div v-if="row.status == 'I'">
              <sw-badge
                :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                :color="$utils.getBadgeStatusColor('OVERDUE').color"
                class="px-3 py-1"
              >
                {{ $t('general.inactive') }}
              </sw-badge>
            </div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.custom_did_groups.type')"
          show="type"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.custom_did_groups.type') }}</span>
            <span>{{ typeOptions[row.type] }}</span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.custom_did_groups.description')"
          show="description"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.custom_did_groups.description') }}</span>
            <span
              v-html="
                row.description ? row.description : $t('item_groups.empty')
              "
            />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`custom-did-groups/${row.id}/edit`"
                tag-name="router-link"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="clone(row)"
                v-if="permissionModule.create"
              >
                <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.clone') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`custom-did-groups/${row.id}/view`"
                tag-name="router-link"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="removeCustomDidGroup(row.id)"
                v-if="permissionModule.delete"
              >
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { DocumentDuplicateIcon, UploadIcon } from '@vue-hero-icons/outline'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  PlusSmIcon,
  ArrowLeftIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../../../components/icon/AstronautIcon'

export default {
  components: {
    PlusSmIcon,
    ArrowLeftIcon,
    DocumentDuplicateIcon,
    UploadIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    AstronautIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: false,
      isLoadingDelete: false,
      filters: {
        name: '',
        description: '',
        status: null,
        type: null,
      },
      statusOptions: {
        A: this.$t('general.active'),
        T: this.$t('general.inactive'),
      },
      statusOptionsA: [
        { name: this.$t('general.active'), value: 'A' },
        { name: this.$t('general.inactive'), value: 'I' },
      ],
      typeOptions: {
        IN: this.$t('corePbx.custom_did_groups.international'),
        LO: this.$t('corePbx.custom_did_groups.local'),
        TF: this.$t('corePbx.custom_did_groups.toll_free'),
      },
      typeOptionsA: [
        {
          name: this.$t('corePbx.custom_did_groups.international'),
          value: 'IN',
        },
        { name: this.$t('corePbx.custom_did_groups.local'), value: 'LO' },
        { name: this.$t('corePbx.custom_did_groups.toll_free'), value: 'TF' },
      ],
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
      },
    }
  },
  computed: {
    ...mapGetters('customDidGroup', [
      'customDidGroups',
      'totalCustomDidGroups',
      'selectedCustomDidGroups',
      'selectAllField',
    ]),

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    showEmptyScreen() {
      return !this.totalCustomDidGroups && !this.isRequestOngoing
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
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  created() {
    this.permissionsUserModule()
    window.hub.$on('newLoad', this.refreshTable)
  },
  methods: {
    ...mapActions('customDidGroup', [
      'fetchCustomDidGroups',
      'deleteCustomDidGroup',
      'selectAllCustomDidGroups',
      'setSelectAllState',
      'setClonedDidGroup',
    ]),

    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name,
        description: this.filters.description,
        status: this.filters.status !== null ? this.filters.status.value : '',
        type: this.filters.type !== null ? this.filters.type.value : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchCustomDidGroups(data)
      this.isRequestOngoing = false

      return {
        data: response.data.customDidGroups.data,
        pagination: {
          totalPages: response.data.customDidGroups.last_page,
          currentPage: page,
        },
      }
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
        description: '',
        status: null,
        type: null,
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    async clone(data) {
      let group = {}
      this.setClonedDidGroup(data)
      this.$router.push(
        '/admin/corePBX/billing-templates/custom-did-groups/create'
      )
    },

    openImportModal() {
      this.openModal({
        title: this.$t('corePbx.custom_did_groups.modal_import_title'),
        componentName: 'CustomDidImportModal',
        data: {},
        variant: 'lg',
      })
    },

    async removeCustomDidGroup(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.custom_did_groups.confirm_delete', 1),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        buttons: {
          cancel: true,
          confirm: true,
        },
      }).then(async (result) => {
        if (result) {
          try {
            this.isLoadingDelete = true
            let res = await this.deleteCustomDidGroup({ id: id })

            if (res.data.success) {
              window.toastr['success'](res.data.message)
              this.refreshTable()
            } else {
              window.toastr['error'](res.data.message)
            }
          } catch (e) {
            window.toastr['error']('Error')
          } finally {
            this.isLoadingDelete = false
          }
        }
      })
    },

    async permissionsUserModule() {
      const data = {
        module: 'pbx_custom_did',
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
    },
  },
}
</script>

<style scoped>
</style>