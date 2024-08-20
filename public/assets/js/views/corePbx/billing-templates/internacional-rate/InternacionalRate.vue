<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('corePbx.menu_title.templates')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('corePbx.menu_title.Internacional', 2)"
          active
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          v-show="totalInternacionals"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <!-- $t('general.filter') -->
        <sw-button
          size="lg"
          class="ml-4"
          variant="primary-outline"
         @click="addPaymentMode"
        >
          {{ 'Import' }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups`"
          size="lg"
          class="ml-4"
          variant="primary-outline"
        >
          {{ $t('corePbx.internacional.prefix_groups') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="international-rate/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('corePbx.internacional.add_internacional') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">

        <sw-input-group
          :label="$tc('didFree.item.prefijo')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.prefix"
            :placeholder="$t('didFree.item.prefijo')"
            type="text"
            name="prefix"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group> 

        <sw-input-group
          :label="$tc('didFree.item.name')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.name"
            :placeholder="$t('didFree.item.name')"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group> 

        <sw-input-group
          :label="$tc('settings.company_info.country')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-select
            v-model="filters.country_id"
            :options="countryNone"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            class="mt-2"
            label="name"
            track-by="id"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('didFree.item.custom_destination')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-select
            v-model="filters.prefixrate_groups_id"
            :options="prefixrate_groups"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            class="mt-2"
            label="name"
            track-by="id"
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

    <!-- EMPTY LIST -->
    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('corePbx.internacional.no_rate')"
      :description="$t('corePbx.internacional.list_of_rate')"
    >
      <satellite-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/international-rate/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.internacional.add_new_rate') }}
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
          {{ $t('general.showing') }}: <b>{{ internacionals.length }}</b>
          {{ $t('general.of') }} <b>{{ totalInternacionals }}</b>
        </p>
        <sw-transition type="fade">
          <sw-dropdown v-if="selectedExtensions.length">
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
        <!-- <sw-table-column
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
        </sw-table-column> -->

        <!-- Nueva Columna -->
        <!-- <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.number')"
          show="extensions_number"
        >
          <template slot-scope="row">
            <span> {{ $t('corePbx.extensions.extension_id') }} </span>

            <div v-html="row.extensions_number" />
          </template>
        </sw-table-column> -->
        <!-- Nueva Columna -->

        <sw-table-column :sortable="true" :label="$t('corePbx.internacional.prefix')" show="prefix">
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.prefix') }}</span>
              {{ row.prefix }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('corePbx.internacional.name')" show="name">
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.name') }}</span>
              {{ row.name }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('corePbx.internacional.country')" show="country_id">
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.country') }}</span>
              {{ getCountry(row.country_id ? row.country_id : 0 ) }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('corePbx.internacional.rate_per_minute')" show="rate_per_minute">
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.rate_per_minute') }}</span>
              {{ parseFloat(row.rate_per_minute) }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('corePbx.internacional.status')" show="status">
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.status') }}</span>
              {{ getStatus(row.status) }}
          </template>
        </sw-table-column>

       <!--  <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prepaidpostpaid')"
          show="status_payment"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.prepaidpostpaid') }}</span>

            <span>
              {{ row.status_payment ? row.status_payment : 'Not selected' }}
            </span>
          </template>
        </sw-table-column> -->

       <!--  <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.description')"
          show="description"
        >
          <template slot-scope="row">
            <span> {{ $t('corePbx.extensions.description') }} </span>

            <div v-html="row.description" />
          </template>
        </sw-table-column> -->

       <!--  <sw-table-column
          :sortable="true"
          :label="$t('items.added_on')"
          sort-as="created_at_no_timezone"
          show="created_at_no_timezone"
        >
          <template slot-scope="row">
            <span>{{ $t('items.added_on') }}</span>
            <span>
              {{
                row.created_at_no_timezone
                  ? row.created_at_no_timezone
                  : 'Not selected'
              }}
            </span>
          </template>
        </sw-table-column> -->

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('items.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`international-rate/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeExtension(row.id)">
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

export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      isLoadingDelete: false,
      prefixrate_groups:[],
      countryNone: [
        { code: '', id: 999999, name:'None',phonecode:0 },
      ],
      country: null,

      filters: {
        prefix:null,
        name:null,
        country_id: null,
        prefixrate_groups_id: null
      },
    }
  },

  computed: {
    ...mapGetters(['countries']),
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('internacionalrate', ['totalInternacionals', 'internacionals','selectedInternacionals']),
    ...mapGetters('extensions', [
      'totalExtensions',
      'extensions',
      'selectedExtensions',
    ]),

    selectField: {
      get: function () {
        return this.selectedExtensions
      },
      set: function (val) {
        this.selectExtension(val)
      },
    },

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalInternacionals && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },

 async mounted() {
   this.countryNone=[...this.countryNone,...this.countries]
      let res = await this.CargarCustomDestination();
      this.prefixrate_groups=[...res.data.internacional];
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('internacionalrate', ['fetchInternacionals', 'deleteInternacional','CargarCustomDestination']),
    ...mapActions('extensions', [
      'fetchExtensions',
      'selectExtension',
      'deleteExtension',
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },
    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        country_id: '',
        name:'',
        country_id: '',
        prefixrate_groups_id: ''
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    /*CONSULTAR DATA*/
    async fetchData({ page, filter, sort }) {


/*       
      console.log('CONSULTA:', data)
      this.isRequestOngoing = true
      let response = await this.fetchExtensions(data)
      this.isRequestOngoing = false */
      let data = {
        prefix: this.filters.prefix !== null ? this.filters.prefix : '',
        name: this.filters.name !== null ? this.filters.name : '',
        country_id: this.filters.country_id !== null ? this.filters.country_id.id : '',
        prefixrate_groups_id: this.filters.prefixrate_groups_id !== null ? this.filters.prefixrate_groups_id.id : '',
       
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchInternacionals(data)
      this.isRequestOngoing = false
      return {
        data: response.data.internacionals.data || {},
        pagination: {
          totalPages: response.data.internacionals.last_page,
          currentPage: page,
          count: response.data.internacionals.total,
        },
      }
    },

    /* ELIMINAR*/
    async removeExtension(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.internacional.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          try {
            this.isLoadingDelete = true
            await this.deleteInternacional(id)
            window.toastr['success'](this.$tc('corePbx.internacional.deleted_message', 1))
            this.$refs.table.refresh()
          }catch (error) {
            if (error.message === 'user_attached') {
            window.toastr['error'](
              this.$tc('packages.user_attached_message'),
              this.$t('general.action_failed')
            )
            } else {
              window.toastr['error'](
                this.$t('general.action_failed_message')
              )
            }
          }finally {
            this.isLoadingDelete = false
          }
        }
      })
    },

    getStatus(status){
      if(status==='A'){
        return 'Active'
      }
      if(status==='I'){
        return 'Inactive'
      }  
    },
    getCountry(id){
      let country = this.countries.filter((country)=>country.id==id);
      return country.length>0 ? country[0].name : "None" 
    },
    addPaymentMode() {
      this.openModal({
        title: 'Import Custom Destinations',
        componentName: 'IndexCsv',
        refreshData: this.$refs.table.refresh,
      })
    },
  },
}
</script>