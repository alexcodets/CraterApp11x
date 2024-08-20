<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('corePbx.menu_title.custom_did')">
    
    
      <template slot="actions">
        <!-- <sw-button
          v-show="totalDIDTOLLFREE"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button> -->

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/custom-did-groups`"
          size="lg"
          class="ml-4"
          variant="primary-outline"
        >
          {{ $t('corePbx.did.custom_did_groups') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="toll-free/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('corePbx.didFree.add_did_free') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!-- EMPTY LIST -->

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('corePbx.no_didFree')"
      :description="$t('corePbx.list_of_didFree')"
    >
      <satellite-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/toll-free/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.didFree.add_new_didFree') }}
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
          {{ $t('general.showing') }}: <b>{{ totalDIDTOLLFREE }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedDIDTOLLFREE.length">
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

            <sw-dropdown-item @click="removeMultipleItems">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <base-loader v-if="isLoadingDelete" />  
      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="custom-control custom-checkbox">
            <sw-checkbox
              :id="row.id"
              v-model="selectField"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>

        
     
       <!-- <sw-table-column
          :sortable="true"
          :label="$t('didFree.item.did_id')"
          show="hg"
        >
      
        <template slot-scope="row">
          {{ row.id }}
        </template>
        </sw-table-column> -->

        <sw-table-column
          :sortable="true"
          :label="$t('didFree.item.prefijo')"
          show="prefijo"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('did.item.category')"
          show="category"
        >
          <template slot-scope="row">
            {{ row.category == null ? 'No status' : row.category_name }}
          </template>
        </sw-table-column>
        
        <sw-table-column
          :sortable="true"
          :label="$t('did.item.price')"
          show="price"
        >
          <template slot-scope="row">
            {{ row.rate_per_minute == null ? 'No price' : row.rate_per_minute }}
          </template>
        </sw-table-column>
        

        <sw-table-column
          :sortable="true"
          :label="$t('did.item.status')"
          show="status"
        >
          <template slot-scope="row">
            {{ statusOptions[row.status] }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('packages.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`toll-free/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>


              <sw-dropdown-item @click="removeDID(row.id)">
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
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
import SatelliteIcon from '../../../../components/icon/SatelliteIcon.vue'
import BaseLoader from '../../../../components/base/BaseLoader.vue'

export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    BaseLoader,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      isLoadingDelete: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      statusOptions:{
        A: this.$t("general.active"),
        T: this.$t("general.inactive"),
      },
      filters: {
        status: '',
      },
      
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('didtollfree', ['tollfree', 'totalDIDTOLLFREE', 'selectedDIDTOLLFREE', 'selectAllField']),

    selectField: {
      get: function () {
        return this.selectedDID
      },
      set: function (val) {
        this.selectDID(val)
      },
    },

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    showEmptyScreen() {
      return !this.totalDIDTOLLFREE && !this.isRequestOngoing
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

  destroyed() {
    if (this.selectAllField) {
      this.selectAllDID()
    }
  },

  methods: {
    ...mapActions('didtollfree', [
      'deleteMultipleDID',
      'deleteDIDTOLLFREE',
      'selectDID',
      'fetchDIDTOLLFREEs',
      'selectAllDID',
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        status: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    async fetchData({ page, filter, sort }) {
  
      let data = {
        status: this.filters.status !== null ? this.filters.status : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchDIDTOLLFREEs(data)
      this.isRequestOngoing = false
      

          
      return {
        data: response.data.profileDIDTOLLFREE.data || {},
        pagination: {
          totalPages: response.data.profileDIDTOLLFREE.last_page,
          currentPage: page,
          count: response.data.profileDIDTOLLFREE.total,
        },
      }
    },

    async removeDID(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('didFree.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {

        try {
          if (willDelete) {
            this.isLoadingDelete = true
            await this.deleteDIDTOLLFREE(id)
            window.toastr['success'](this.$tc('didFree.deleted_message', 1))
            this.$refs.table.refresh()
          }
        } catch (error) {
          if (error.message === 'user_attached') {
            window.toastr['error'](
              this.$tc('did.user_attached_message'),
              this.$t('general.action_failed')
            )
          }
          window.toastr['error'](error.message)
        } finally {
          this.isLoadingDelete = false
        }
      })
    },

  },
}
</script>