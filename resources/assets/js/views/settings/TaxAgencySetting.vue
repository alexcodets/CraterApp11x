<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.tax_agency.title') }}
        </h6>
        <!-- <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.tax_types.description') }}
        </p> -->
      </div>

      <div class="mt-4 lg:mt-0 lg:ml-2">
          <!-- v-show="totalCustomerEstimates" -->

          <sw-button  size="lg" variant="primary-outline" type="button" @click="backButton" >
          <span class="w-6 h-6 " />
            {{ $t('general.back') }}
          </sw-button>
          <sw-button  tag-name="router-link" size="lg" variant="primary-outline"  to="/admin/settings/tax-types">
            <currency-dollar-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('settings.tax_categories.taxes') }}
          </sw-button>
        <sw-button size="lg" variant="primary-outline"  @click="toggleFilter"  style="position: relative;top: -12%;" class="mr-3">
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button size="lg" variant="primary-outline" @click="openTaxModal" v-if="permissionModule.create">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.tax_agency.add_new_tax') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper
        v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >

        <sw-input-group
          :label="$t('settings.tax_agency.name')"
          color="black-light"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.name">
            <!-- <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" /> -->
          </sw-input>
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.tax_agency.number')"
          color="black-light"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.number">
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.country')"
          class="mt-2 xl:ml-8"
          color="black-light"
        >

          <sw-select
            v-model="filters.country"
            :options="countries"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_country')"
            label="name"
            track-by="id"
            @select="countrySelected($event)"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.state')"
          class="mt-2 xl:ml-8"
          color="black-light"
        >
          <!-- <span
            v-if="formData.state"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 470px; right: 28px; z-index: 999999"
            @click="deleteState()"
          >
            <x-circle-icon class="h-5" />
          </span> -->

          <sw-select
            v-model="filters.state"
            :options="states"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_state')"
            label="name"
            track-by="id"
          />
            <!-- @select="stateSelected($event)" -->
        </sw-input-group>

        <label class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter">
            {{ $t('general.clear_all') }}
        </label>
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-table-component
      ref="table"
      :show-filter="false"
      :data="fetchData"
      table-class="table"
      variant="gray"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.tax_agency.name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_agency.name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_agency.number')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_agency.number') }}</span>
          <span class="mt-6">{{ row.number }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_agency.email')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_agency.email') }}</span>
          <span class="mt-6">{{ row.email }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_agency.phone')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_agency.phone') }}</span>
          <span class="mt-6">{{ row.phone }}</span>
        </template>
      </sw-table-column>


      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_agency.action') }}</span>
          <sw-dropdown>
            <dot-icon slot="activator" />

            <sw-dropdown-item @click="editTaxAgency(row.id)" v-if="permissionModule.update">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeTax(row.id)" v-if="permissionModule.delete">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>

  </sw-card>

</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon, HashtagIcon, FilterIcon, XIcon, CurrencyDollarIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    HashtagIcon,
    FilterIcon,
    XIcon,
    CurrencyDollarIcon
  },

  data() {
    return {
      countries: [],
      isRequestOnGoing: false,
      showFilters: false,
      states: [],
      formData: {
        tax_per_item: false,
      },
      filters: {
        name: '',
        number: '',
        country: [],
        state: [],
        
      },
      permissionModule:{
          create: false,
          update: false,
          delete: false,
          read: false
        }
    }
  },

  computed: {
    ...mapGetters('taxAgency', [
      /* 'fetchTaxAgency',  */
      // 'getTaxTypeById'
    ]),

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },

  mounted() {
    // this.getTaxSetting()
  },

  created() {
    this.loadData()
    this.permissionsUserModule()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('taxAgency', [
      'resetSelectedTaxAgency',
      'fetchTaxAgencies',
      'fetchTaxAgency',
      'deleteTaxAgency'
    ]),
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),
    ...mapActions('user', ['getUserModules']),

    async loadData(){
      let res = await window.axios.get('/api/v1/countries')
      if (res) {    
        this.countries = res.data.countries
      }

    },
    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name,
        number: this.filters.number,
        country_id: this.filters.country ? this.filters.country.id : '',
        state_id: this.filters.state ? this.filters.state.id : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchTaxAgencies(data)

      return {
        data: response.data.taxAgencies.data,
        pagination: {
          totalPages: response.data.taxAgencies.last_page,
          currentPage: page,
          count: response.data.taxAgencies.count,
        },
      }
    },
    clearFilter() {
      this.filters = {
        name: '',
        number: '',
        country: null,
        state: null,
      }
      this.$refs.table.refresh()
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    async setTax(val) {
      let data = {
        settings: {
          tax_per_item: this.formData.tax_per_item ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    async countrySelected(country) {
      // console.log('country: ', country);
      let res = await window.axios.get('/api/v1/states/' + country.code)
      this.states = res.data.states
      return true
    },
    async removeTax(id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.tax_agency.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteTaxAgency(id)
        
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.tax_agency.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](this.$t('settings.tax_agency.already_in_use'))
        }
      })
    },
    openTaxModal() {
      this.openModal({
        title: this.$t('settings.tax_agency.add_tax'),
        componentName: 'TaxAgencyModal',
        refreshData: this.$refs.table.refresh,
        variant: 'lg'
      })
    },   
    async editTaxAgency(id) {
      let response = await this.fetchTaxAgency(id)
      this.openModal({
        title: this.$t('settings.tax_types.edit_tax'),
        componentName: 'TaxAgencyModal',
        id: id,
        data: response.data.taxAgency,
        refreshData: this.$refs.table.refresh,
      })
    },
    setFilters() {
      this.resetSelectedTaxAgency()
      this.$refs.table.refresh()
    },

    async permissionsUserModule(){
      const data = {
         module: "tax_types" 
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
         const modulePermissions = permissions.permissions[0]
          if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          }else if(modulePermissions.access == 0 ){
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if(permissions.super_admin == true){
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      }else if(permissions.exist == true ){
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions.create == 1){
            this.permissionModule.create = true
        }
        if(modulePermissions.update == 1){
            this.permissionModule.update = true
        }
        if(modulePermissions.delete == 1){
            this.permissionModule.delete = true
        }
        if(modulePermissions.read == 1){
            this.permissionModule.read = true
        }
      }

    },

    backButton(){
      this.$utils.cancelFormOrBack(this, this.$router, 'back')
    }
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
}
</script>
