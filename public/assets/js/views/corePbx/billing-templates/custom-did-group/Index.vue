<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="$t('corePbx.custom_did_groups.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('corePbx.corePbx')" to="/admin/corePBX"/>
        <sw-breadcrumb-item
          :title="$tc('corePbx.custom_did_groups.title')"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalCustomDidGroups"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold"/>
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/toll-free`"
          variant="primary-outline"
          size="lg"
          class="flex justify-center w-auto align-bottom ml-4"
        >
          Custom DIDs
        </sw-button>

        <sw-button
          size="lg"
          variant="primary"
          class="ml-4"
          @click="openImportModal"
        >
          <upload-icon class="h-4 mr-1 -ml-2 font-bold"/>
          {{ $t('general.import') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="custom-did-groups/create"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold"/>
          {{ $t('corePbx.custom_did_groups.add') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!--   Fitros     -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('corePbx.custom_did_groups.name')"
          class="flex-1 mt-2"
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
          :label="$t('corePbx.custom_did_groups.description')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.description"
            type="text"
            name="description"
            class="mt-2"
            autocomplete="off"
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
      >
        {{ $t('corePbx.custom_did_groups.add_new_custom_did_group') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <!--   Si hay informacion para la tabla     -->
    <div v-show="!showEmptyScreen" class="relative table-container">
      <!-- Fila de utilidades -->
      <div class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid">
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

            <sw-dropdown-item @click="'removeMultipleItemGroups'">
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
            >
              {{ row.name }}
            </router-link>
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
            <span>{{statusOptions[row.status]}}</span>
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
            <span>{{typeOptions[row.type]}}</span>
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
            <span v-html="row.description ? row.description : $t('item_groups.empty')"/>
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
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="clone(row)"
              >
                <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.clone') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`custom-did-groups/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeCustomDidGroup(row.id)">
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
import {mapActions, mapGetters} from 'vuex'
import { DocumentDuplicateIcon, UploadIcon } from '@vue-hero-icons/outline'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  PlusSmIcon, 
  ArrowLeftIcon
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
    AstronautIcon
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: false,
      isLoadingDelete: false,
      filters: {
        name: '',
        description: '',
      },
      statusOptions:{
        A: this.$t("general.active"),
        T: this.$t("general.inactive"),
      },
      typeOptions:{
        IN: this.$t("corePbx.custom_did_groups.international"),
        LO: this.$t("corePbx.custom_did_groups.local"),
        TF: this.$t("corePbx.custom_did_groups.toll_free"),
      },
    }
  },
  computed: {
    ...mapGetters('customDidGroup', [
      'customDidGroups',
      'totalCustomDidGroups',
      'selectedCustomDidGroups',
      'selectAllField'
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
    }
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  created() {
    window.hub.$on('newLoad', this.refreshTable)
  },
  methods: {
    ...mapActions('customDidGroup', [
      'fetchCustomDidGroups',
      'deleteCustomDidGroup',
      'selectAllCustomDidGroups',
      'setSelectAllState',
      'setClonedDidGroup'
    ]),

    ...mapActions('modal', ['openModal']),

    async fetchData({page, filter, sort}) {

      let data = {
        name: this.filters.name,
        description: this.filters.description,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchCustomDidGroups(data)
      this.isRequestOngoing = false

     console.log(response.data.customDidGroups.data);
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
        description: ''
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    async clone(data) {
      let group = {

      }
      this.setClonedDidGroup(data);
      this.$router.push('/admin/corePBX/billing-templates/custom-did-groups/create');
    },

    openImportModal() {
      this.openModal({
        title: this.$t('corePbx.custom_did_groups.modal_import_title'),
        componentName: 'CustomDidImportModal',
        data: {},
        variant: 'lg'
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
        }
      }).then(async (result) => {
        if (result) {
            try{
              this.isLoadingDelete = true
              await this.deleteCustomDidGroup({ids: [id]})
              window.toastr['success'](this.$tc('corePbx.custom_did_groups.deleted_message', 1))
              this.refreshTable()
            }catch(e){
              window.toastr['error'](e.message)
            }finally{
              this.isLoadingDelete = false
            }
         }
      })
    },

  }
}
</script>

<style scoped>

</style>