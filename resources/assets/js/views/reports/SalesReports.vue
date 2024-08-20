<template>
  <div class="grid gap-8 md:grid-cols-12">
    <div class="col-span-8 mt-12 md:col-span-4">
      <div class="grid grid-cols-12">
        <sw-input-group :label="$t('reports.sales.date_range')" :error="dateRangeError" class="col-span-12 md:col-span-8">
          <sw-select v-model="selectedRange" :options="dateRange" :allow-empty="false" :show-labels="false" class="mt-2"
            @input="onChangeDateRange" />
        </sw-input-group>
      </div>
      <div class="grid grid-cols-1 mt-6 md:gap-10 md:grid-cols-2">
        <sw-input-group :label="$t('reports.sales.from_date')" :error="fromDateError">
          <base-date-picker v-model="formData.from_date" :invalid="$v.formData.from_date.$error" :calendar-button="true"
            calendar-button-icon="calendar" class="mt-2" @input="$v.formData.from_date.$touch()" />
        </sw-input-group>

        <sw-input-group :label="$t('reports.sales.to_date')" :error="toDateError" class="mt-5 md:mt-0">
          <base-date-picker v-model="formData.to_date" :invalid="$v.formData.to_date.$error" :calendar-button="true"
            calendar-button-icon="calendar" class="mt-2" @input="$v.formData.to_date.$touch()" />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 mt-6 md:gap-10">
        <sw-input-group :label="$t('reports.sales.report_type')" class="mt-1">
          <sw-select v-model="selectedType" :options="reportTypes" :allow-empty="false" :show-labels="false" class="mt-2"
            :placeholder="$t('reports.sales.report_type')" @input="getInitialReport" />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 mt-6 md:gap-10">
        <sw-input-group :label="'Paid Status'" class="mt-1">
          <sw-select v-model="paidStatusSelected" :options="paidStatusOptions" :searchable="true" :show-labels="true"
            :multiple="true" :placeholder="'Select a Status'" class="mt-1" label="name" track-by="value" />
        </sw-input-group>
      </div>

      <div v-if="allow_invoice_form_pos" class="grid grid-cols-1 mt-6 md:gap-10">
        <sw-input-group :label="$t('core_pos.store')"  class="mt-1">
          <sw-select v-model="stores_selected" :options="stores" :searchable="true" :show-labels="false"
            :allow-empty="true" :multiple="true" class="mt-2" track-by="id" label="name" :tabindex="1" />
        </sw-input-group>
      </div>

      <!-- selectedType == "By Customer" -->
      <div v-if="selectedType === 'By Customer'">
        <div class="grid grid-cols-1 mt-6 md:gap-10 ">
          <sw-input-group :label="$t('expenses.customer')">
            <sw-select ref="baseSelect" v-model="customersSelected" :options="customers" :searchable="true"
              :show-labels="true" :multiple="true" :placeholder="$t('customers.select_a_customer')" class="mt-1"
              label="name" track-by="id" />
          </sw-input-group>

          <sw-input-group :label="$t('general.user')">
              <sw-select
                ref="baseSelect"
                v-model="usersSelected"
                :options="users"
                :searchable="true"
                :show-labels="true"   
                :multiple="true"          
                :placeholder="$t('general.select_user')"             
                class="mt-1"
                label="name"
                track-by="id"
              />
          </sw-input-group>

        </div>

        <div class="grid grid-cols-1 mt-6 md:gap-10">
          <sw-input-group :label="$t('customers.country')" class="md:col-span-3">
            <span v-if="country" class="absolute text-gray-400 cursor-pointer"
              style="top: 38px; right: 5px; z-index: 999999;" @click="country = null; states = []; state = null">
              <x-circle-icon class="h-5" />
            </span>
            <sw-select v-model="country" :options="countries" :searchable="true" :show-labels="false" :allow-empty="true"
              :placeholder="$t('general.select_country')" label="name" track-by="id" @select="countrySelected($event)" />
          </sw-input-group>

          <sw-input-group :label="$t('customers.state')" class="md:col-span-3">
            <span v-if="state" class="absolute text-gray-400 cursor-pointer"
              style="top: 38px; right: 5px; z-index: 999999;" @click="state = null">
              <x-circle-icon class="h-5" />
            </span>
            <sw-select v-model="state" :options="states" :searchable="true" :show-labels="false" :allow-empty="true"
              :placeholder="$t('general.select_state')" label="name" track-by="id" @select="stateSelected($event)" />
          </sw-input-group>
        </div>
      </div>
      <!--  -->

      <!-- selectedType == "By Item" -->
      <div v-else>
        <div class="grid grid-cols-1 mt-6 md:gap-10 ">

          <sw-input-group :label="$t('expenses.customer')">
            <sw-select ref="baseSelect" v-model="customersByItemSelected" :options="customersByItem" :searchable="true"
              :show-labels="true" :multiple="true" :placeholder="$t('customers.select_a_customer')" class="mt-1"
              label="name" track-by="id" />
          </sw-input-group>

          <sw-input-group :label="'User'">
            <sw-select ref="baseSelect" v-model="usersByItemSelected" :options="usersByItem" :searchable="true"
              :show-labels="true" :multiple="true" :placeholder="'Select an user'" class="mt-1" label="name"
              track-by="id" />
          </sw-input-group>

          <sw-input-group :label="$t('items.unit')">
            <sw-select v-model="filters.unitsSelected" :options="units" :searchable="true" :show-labels="true"
              :multiple="true" :placeholder="$t('items.select_a_unit')" class="mt-1" label="name" track-by="id">
            </sw-select>
          </sw-input-group>

          <sw-input-group :label="'Item Category'">
            <sw-select v-model="filters.itemsCategoriesSelected" :options="itemsCategories" :searchable="true"
              :show-labels="true" :multiple="true" :placeholder="'Select an Item Category'" class="mt-1" label="name"
              track-by="id" />
          </sw-input-group>

          <sw-input-group :label="$t('items.items_groups')">
            <sw-select v-model="filters.itemsGroupsSelected" :options="itemsGroups" :searchable="true" :show-labels="true"
              :multiple="true" :placeholder="'Select an unit item group'" class="mt-1" label="name" track-by="id" />
          </sw-input-group>

          <sw-input-group :label="'Item'">
            <sw-select ref="baseSelect" v-model="itemsSelected" :options="items" :searchable="true" :show-labels="true"
              :multiple="true" :placeholder="$t('customers.select_a_customer')" class="mt-1" label="name" track-by="id" />
          </sw-input-group>
        </div>
      </div>
      <!--  -->


      <sw-button variant="primary-outline" class="content-center hidden mt-0 w-md md:flex md:mt-8" type="submit"
        @click.prevent="getReports()">
        {{ $t('reports.update_report') }}
      </sw-button>
    </div>
    <div class="col-span-8 mt-0 md:mt-12">
      <iframe :src="getReportUrl" class="hidden w-full h-screen border-gray-100 border-solid rounded md:flex" />
      <a class="flex items-center justify-center h-10 px-5 py-1 text-sm font-medium leading-none text-center text-white whitespace-nowrap rounded md:hidden bg-primary-500"
        @click="viewReportsPDF">
        <document-text-icon />

        <span>{{ $t('reports.view_pdf') }}</span>
      </a>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { DocumentTextIcon, XCircleIcon } from '@vue-hero-icons/solid'
import moment from 'moment'

const { required } = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentTextIcon,
    XCircleIcon
  },

  data() {
    return {
      paidStatusOptions: [
        {
          name: 'All',
          value: 'ALL'
        },
        {
          name: 'Unpaid',
          value: 'UNPAID'
        },
        {
          name: 'Partially Paid',
          value: 'PARTIALLY_PAID'
        },
        {
          name: 'Paid',
          value: 'PAID'
        },
      ],
      paidStatusSelected: [],
      reportTypes: ['By Customer', 'By Item'],
      selectedType: 'By Customer',
      dateRange: [
        'Today',
        'This Week',
        'This Month',
        'This Quarter',
        'This Year',
        'Previous Week',
        'Previous Month',
        'Previous Quarter',
        'Previous Year',
        'Custom',
      ],
      selectedRange: 'This Month',
      range: new Date(),
      formData: {
        from_date: moment().startOf('month').format('YYYY-MM-DD'),
        to_date: moment().endOf('month').format('YYYY-MM-DD'),
      },
      url: null,
      customerSiteURL: null,
      itemsSiteURL: null,
      //
      customers: [],
      customersSelected: [],
      users: [],
      usersSelected: [],
      //    
      filters: {
        unitsSelected: [],
        itemsCategoriesSelected: [],
        itemsGroupsSelected: [],
      },
      itemsSelected: [],
      //
      customersByItem: [],
      customersByItemSelected: [],
      usersByItem: [],
      usersByItemSelected: [],
      units: [],
      itemsCategories: [],
      itemsGroups: [],
      items: [],
      //
      countries: [],
      states: [],
      country: null,
      state: null,
      country_id: null,
      state_id: null,
      stores: [],
      stores_selected: [],
      allow_invoice_form_pos: false
    }
  },

  validations: {
    range: {
      required,
    },
    formData: {
      from_date: {
        required,
      },
      to_date: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters('company', ['getSelectedCompany']),
    //...mapGetters('customer', ['customers']),

    getReportUrl() {
      return this.url
    },

    dateRangeError() {
      if (!this.$v.range.$error) {
        return ''
      }

      if (!this.$v.range.required) {
        return this.$t('validation.required')
      }
    },

    fromDateError() {
      if (!this.$v.formData.from_date.$error) {
        return ''
      }

      if (!this.$v.formData.from_date.required) {
        return this.$t('validation.required')
      }
    },

    toDateError() {
      if (!this.$v.formData.to_date.$error) {
        return ''
      }

      if (!this.$v.formData.to_date.required) {
        return this.$t('validation.required')
      }
    },

    customerDateRangeUrl() {
      if (this.customersSelected.length > 0 || this.usersSelected.length > 0 || this.country != null ||
        this.paidStatusSelected.length > 0 || this.stores_selected.length > 0) {
        let val = "";

        if (this.customersSelected.length > 0) {
          val += `&customer=${this.customersSelected
            .map((item) => item.id)
            .join(",")}`;
        }

        if (this.usersSelected.length > 0) {
          val += `&users_id=${this.usersSelected
            .map((user) => user.id)
            .join(",")}`;
        }
        if (this.stores_selected.length > 0) {
          val += `&stores_id=${this.stores_selected
            .map((store) => store.id)
            .join(",")}`;

        }

        if (this.paidStatusSelected.length > 0) {
          val += `&paid_status=${this.paidStatusSelected
            .map((paid_status) => paid_status.value)
            .join(",")}`;
        }

        if (this.country) {
          this.country_id = this.country.id
          val += `&country=${this.country_id}`
        }

        if (this.state) {
          this.state_id = this.state.id
          val += `&state=${this.state_id}`
        }

        return `${this.customerSiteURL}?from_date=${moment(this.formData.from_date)
          .format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format('YYYY-MM-DD')}${val}`
      }

      return `${this.customerSiteURL}?from_date=${moment(
        this.formData.from_date
      ).format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format(
        'YYYY-MM-DD'
      )}`
    },

    itemDateRangeUrl() {
      if (this.paidStatusSelected.length > 0 ||
        this.filters.unitsSelected.length > 0 ||
        this.filters.itemsCategoriesSelected.length > 0 ||
        this.filters.itemsGroupsSelected.length > 0 ||
        this.itemsSelected.length > 0 ||
        // customers - users
        this.customersByItemSelected.length > 0 ||
        this.usersByItemSelected.length > 0 || 
        this.stores_selected.length > 0
      ) {
        let val = "";

        if (this.customersByItemSelected.length > 0) {
          val += `&customers_id=${this.customersByItemSelected
            .map((customer) => customer.id)
            .join(",")}`;
        }

        if (this.usersByItemSelected.length > 0) {
          val += `&users_id=${this.usersByItemSelected
            .map((user) => user.id)
            .join(",")}`;
        }
        
        if (this.stores_selected.length > 0) {
          val += `&store_id=${this.store_selected
            .map((store) => store.id)
            .join(",")}`;
        }

        if (this.paidStatusSelected.length > 0) {
          val += `&paid_status=${this.paidStatusSelected
            .map((paid_status) => paid_status.value)
            .join(",")}`;
        }

        if (this.filters.unitsSelected.length > 0) {
          val += `&units_id=${this.filters.unitsSelected
            .map((unit) => unit.id)
            .join(",")}`;
        }

        if (this.filters.itemsCategoriesSelected.length > 0) {
          val += `&categories_id=${this.filters.itemsCategoriesSelected
            .map((category) => category.id)
            .join(",")}`;
        }

        if (this.filters.itemsGroupsSelected.length > 0) {
          val += `&groups_id=${this.filters.itemsGroupsSelected
            .map((group) => group.id)
            .join(",")}`;
        }

        if (this.itemsSelected.length > 0) {
          val += `&item=${this.itemsSelected
            .map((item) => item.id)
            .join(",")}`;
        }

        return `${this.itemsSiteURL}?from_date=${moment(this.formData.from_date)
          .format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format('YYYY-MM-DD')}${val}`
      }

      return `${this.itemsSiteURL}?from_date=${moment(
        this.formData.from_date
      ).format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format(
        'YYYY-MM-DD'
      )}`
    },
  },

  watch: {
    range(newRange) {
      this.formData.from_date = moment(newRange).startOf('year').format('YYYY-MM-DD')
      this.formData.to_date = moment(newRange).endOf('year').format('YYYY-MM-DD')
    },

    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  async mounted() {

    const modules = ['corePOS']
    const modulesArray = await this.getModules(modules)

    var moduleCorePos = null

    if (typeof modulesArray.modules != 'undefined') {
      moduleCorePos = modulesArray.modules.find(
        (element) => element.name === 'corePOS'
      )
    }

    if (moduleCorePos && moduleCorePos.status == 'A') {
      let res = await this.fetchCompanySettings(['allow_invoice_form_pos'])
      this.allow_invoice_form_pos =
        res.data.allow_invoice_form_pos == '0' ? false : true
    } else {
      this.allow_invoice_form_pos = false
    }
   
    this.customerSiteURL = `/reports/sales/customers/${this.getSelectedCompany.unique_hash}`
    this.itemsSiteURL = `/reports/sales/items/${this.getSelectedCompany.unique_hash}`

    let res = await window.axios.get('/api/v1/countries')
    if (res) {
      this.countries = res.data.countries
    }

      let dataStore = {
        limit: 'all'
      }
      
      const responseStore = await this.fetchStores(dataStore)
      this.stores = responseStore.data.stores.data
      
    // By Item  
    const response_items = await this.getItems()

    this.customersByItem = [...response_items.data.customers_by_item]
    this.usersByItem = [...response_items.data.users_by_item]

    this.units = [...response_items.data.units]
    this.itemsCategories = [...response_items.data.items_categories]
    this.itemsGroups = [...response_items.data.items_groups]
    if(!Object.keys(response_items.data.items).length === 0)
    {
      this.items = [...response_items.data.items]
    }

    // By Customer
    const response_customers = await this.getCustomers()
    this.customers = [...response_customers.data.customers]
    this.users = [...response_items.data.users_by_item]

    if (response_customers.data.customers.length > 0) {
      this.customersSelected = [response_customers.data.customers[0]]
    }

    

    this.getInitialReport()
    
  },

  methods: {
    ...mapActions('salesReport', ['loadLinkByCustomer', 'loadLinkByItems']),
    ...mapActions('taxAgency', ['fetchTaxAgencies']),
    ...mapActions('item', ['getItems', 'getItemsByFilters']),
    ...mapActions('customer', ['getCustomers']),
    ...mapActions('corePos', ['fetchStores']),
    ...mapActions('modules', ['getModules']),
    ...mapActions('company', ['fetchCompanySettings']),

    async setFilters() {
      let data = {
        units_id: this.filters.unitsSelected.length > 0
          ? this.filters.unitsSelected.map(unit => {
            return unit.id
          })
          : null,
        categories_id: this.filters.itemsCategoriesSelected.length > 0
          ? this.filters.itemsCategoriesSelected.map(category => {
            return category.id
          })
          : null,
        groups_id: this.filters.itemsGroupsSelected.length > 0
          ? this.filters.itemsGroupsSelected.map(group => {
            return group.id
          })
          : null,
      }
      let response = await this.getItemsByFilters(data)
      this.itemsSelected = []
      this.items = [...response.data.items]
    },

    getThisDate(type, time) {
      return moment()[type](time).format('YYYY-MM-DD')
    },

    getPreDate(type, time) {
      return moment().subtract(1, time)[type](time).format('YYYY-MM-DD')
    },

    onChangeDateRange() {
      switch (this.selectedRange) {
        case 'Today':
          this.formData.from_date = moment().format('YYYY-MM-DD')
          this.formData.to_date = moment().format('YYYY-MM-DD')
          break

        case 'This Week':
          this.formData.from_date = this.getThisDate('startOf', 'isoWeek')
          this.formData.to_date = this.getThisDate('endOf', 'isoWeek')
          break

        case 'This Month':
          this.formData.from_date = this.getThisDate('startOf', 'month')
          this.formData.to_date = this.getThisDate('endOf', 'month')
          break

        case 'This Quarter':
          this.formData.from_date = this.getThisDate('startOf', 'quarter')
          this.formData.to_date = this.getThisDate('endOf', 'quarter')
          break

        case 'This Year':
          this.formData.from_date = this.getThisDate('startOf', 'year')
          this.formData.to_date = this.getThisDate('endOf', 'year')
          break

        case 'Previous Week':
          this.formData.from_date = this.getPreDate('startOf', 'isoWeek')
          this.formData.to_date = this.getPreDate('endOf', 'isoWeek')
          break

        case 'Previous Month':
          this.formData.from_date = this.getPreDate('startOf', 'month')
          this.formData.to_date = this.getPreDate('endOf', 'month')
          break

        case 'Previous Quarter':
          this.formData.from_date = this.getPreDate('startOf', 'quarter')
          this.formData.to_date = this.getPreDate('endOf', 'quarter')
          break

        case 'Previous Year':
          this.formData.from_date = this.getPreDate('startOf', 'year')
          this.formData.to_date = this.getPreDate('endOf', 'year')
          break

        default:
          break
      }
    },

    setRangeToCustom() {
      this.selectedRange = 'Custom'
    },

    async getInitialReport() {
      if (this.selectedType === 'By Customer') {
        this.url = this.customerDateRangeUrl
        return true
      }
      this.url = this.itemDateRangeUrl
      return true
    },

    async viewReportsPDF() {
      let data = await this.getReports()
      window.open(this.getReportUrl, '_blank')
      return data
    },

    async getReports(isDownload = false) {
      this.$v.range.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      if (this.selectedType === 'By Customer') {
        this.url = this.customerDateRangeUrl
        return true
      }
      this.url = this.itemDateRangeUrl
      return true
    },

    downloadReport() {
      if (!this.getReports()) {
        return false
      }
      return this.getReportUrl
      /*
      window.open(this.getReportUrl + '&download=true')
      setTimeout(() => {
        if (this.selectedType === 'By Customer') {
          this.url = this.customerDateRangeUrl
          return true
        }
        this.url = this.itemDateRangeUrl
        return true
      }, 200)
      */
    },

    async countrySelected(country) {
      this.state = null
      const vm = this
      let res = await window.axios.get('/api/v1/states/' + country.code)
      vm.states = res.data.states

      let resAgencies = await this.fetchTaxAgencies({
        limit: 'all',
        country_id: country.id,
      })

      if (resAgencies) {
        this.agencies = resAgencies.data.taxAgencies.data
      }
      vm.isLoading = false
    },

    async stateSelected(state) {
      const vm = this
      let resAgencies = await this.fetchTaxAgencies({
        limit: 'all',
        state_id: state.id,
      })

      if (resAgencies) {
        this.agencies = resAgencies.data.taxAgencies.data
      }
    },

  },
}
</script>
