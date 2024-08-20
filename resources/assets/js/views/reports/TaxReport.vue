<template>
  <div class="grid gap-8 md:grid-cols-12">
    <div class="col-span-8 mt-12 md:col-span-4">
      <div class="grid grid-cols-12">
        <sw-input-group
          :label="$t('reports.taxes.date_range')"
          :error="dateRangeError"
          class="col-span-12 md:col-span-8"
        >
          <sw-select
            v-model="selectedRange"
            :options="dateRange"
            :allow-empty="false"
            :show-labels="false"
            class="mt-2"
            @input="onChangeDateRange"
          />
        </sw-input-group>
      </div>
      <div class="grid grid-cols-1 mt-6 md:gap-10 md:grid-cols-2">
        <sw-input-group
          :label="$t('reports.taxes.from_date')"
          :error="fromDateError"
        >
          <sw-date-picker
            v-model="formData.from_date"
            :invalid="$v.formData.from_date.$error"
            :config="{
              altInput: true,
              enableTime: false,
              time_24hr: false,
              altFormat: 'm/d/Y',
              dateFormat: 'Y-m-d',
            }"
            class="mt-2"
            @input="$v.formData.from_date.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('reports.taxes.to_date')"
          :error="toDateError"
          class="mt-5 md:mt-0"
        >
          <sw-date-picker
            v-model="formData.to_date"
            :invalid="$v.formData.to_date.$error"
            :config="{
              altInput: true,
              enableTime: false,
              time_24hr: false,
              altFormat: 'm/d/Y',
              dateFormat: 'Y-m-d',
            }"
            class="mt-2"
            @input="$v.formData.to_date.$touch()"
          />
        </sw-input-group>
      </div>

      <sw-divider class="mb-5 md:mb-8 gap-4" style="margin-top: 1em" />

      <div class="grid grid-cols-12 mt-6 md:mt-8">
        <sw-input-group
          :label="$t('reports.sales.report_type')"
          class="col-span-12 md:col-span-8"
        >
          <sw-select
            v-model="selectedType"
            :options="reportTypes"
            :allow-empty="false"
            :show-labels="false"
            class="mt-2"
            :placeholder="$t('reports.sales.report_type')"
          />
        </sw-input-group>

        <br />
        <br />

        <sw-input-group
          :label="$t('reports.taxes.include_cdr')"
          class="col-span-12 md:col-span-8 mt-5"
        >
          <sw-checkbox
            v-model="include_cdr"
            variant="primary"
            size="sm"
            class="hidden md:inline"
          />
          <!-- @change="" -->
        </sw-input-group>
      </div>

      <sw-divider class="mb-5 md:mb-8 gap-4" style="margin-top: 1em" />

      <label
        class="text-sm leading-snug text-black cursor-pointer"
        style="float: right; margin-bottom: 1em"
        @click="clearFilter"
        >{{ $t('general.clear_all') }}</label
      >

      <div class="grid grid-cols-1 mt-6 md:gap-10 md:grid-cols-2">
        <sw-input-group :label="$t('customers.country')" class="md:col-span-3">
          <sw-select
            v-model="country"
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

        <sw-input-group :label="$t('customers.state')" class="md:col-span-3">
          <sw-select
            v-model="state"
            :options="states"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_state')"
            label="name"
            track-by="id"
            @select="stateSelected($event)"
          />
        </sw-input-group>

        <sw-input-group :label="$t('customers.county')" class="md:col-span-3">
          <sw-input
            v-model="county"
            name="county"
            type="text"
            :autocomplete="false"
          />
        </sw-input-group>

        <sw-input-group :label="$t('customers.city')" class="md:col-span-3">
          <sw-input
            v-model="city"
            name="city"
            type="text"
            :autocomplete="false"
          />
        </sw-input-group>

        <sw-input-group :label="$t('customers.category')" class="md:col-span-3">
          <span
            v-if="category"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 523px; right: 28px; z-index: 999999"
            @click="deleteCategory()"
          >
            <x-circle-icon class="h-5" />
          </span>

          <sw-select
            v-model="category"
            :options="categories"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_category')"
            label="name"
            track-by="id"
            z-index="9999"
            @select="categorySelected($event)"
          />
        </sw-input-group>

        <!-- <div class="grid grid-cols-1 mt-6 md:gap-10"> -->
        <sw-input-group v-if="allow_invoice_form_pos" :label="$t('core_pos.store')"  class="md:col-span-3">
          <sw-select v-model="stores_selected" :options="stores" :searchable="true" :show-labels="false"
            :allow-empty="true" :multiple="true" class="mt-2" track-by="id" label="name" :tabindex="1" />
        </sw-input-group>
      <!-- </div> -->

        <sw-input-group :label="$t('customers.agency')" class="md:col-span-3">
          <span
            v-if="agency"
            class="absolute text-gray-400 cursor-pointer"
            style="top: 81.1%; right: 28px; z-index: 999999"
            @click="deleteAgency()"
          >
            <x-circle-icon class="h-5" />
          </span>

          <sw-select
            v-model="agency"
            :options="agencies"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :placeholder="$t('general.select_agency')"
            label="name"
            track-by="id"
            z-index="9999"
            @select="agencySelected($event)"
          />
        </sw-input-group>
      </div>

      <sw-divider class="mb-5 md:mb-8 gap-4" style="margin-top: 1em" />
      <sw-button
        variant="primary-outline"
        class="content-center hidden mt-0 w-md md:flex md:mt-8"
        @click="getReports()"
      >
        {{ $t('reports.update_report') }}
      </sw-button>
    </div>
    <div class="col-span-8 mt-0">
      <div class="flex flex-nowrap justify-end w-full py-4">
        <sw-button variant="primary" @click="onDownloadCsv()">
          <download-icon class="h-5 mr-1" />
          {{ $t('general.download_csv') }}
        </sw-button>
      </div>

      <iframe
        :src="getReportUrl"
        class="
          hidden
          w-full
          h-screen
          border-gray-100 border-solid
          rounded
          md:flex
        "
      />
      <a
        class="
          flex
          items-center
          justify-center
          h-10
          px-5
          py-1
          text-sm
          font-medium
          leading-none
          text-center text-white
          whitespace-nowrap
          rounded
          md:hidden
          bg-primary-500
        "
        @click="viewReportsPDF"
      >
        <document-text-icon />
        <span>{{ $t('reports.view_pdf') }}</span>
      </a>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

import moment from 'moment'
const { required } = require('vuelidate/lib/validators')
import {
  XCircleIcon,
  DocumentTextIcon,
  DownloadIcon,
} from '@vue-hero-icons/solid'
import SwDatePicker from '@bytefury/spacewind/src/components/SwDatePicker'

export default {
  components: {
    DocumentTextIcon,
    XCircleIcon,
    DownloadIcon,
    SwDatePicker,
  },
  data() {
    return {
      reportTypes: ['By General', 'By Customer', 'By Item'],
      selectedType: 'By General',
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
      siteURL: null,
      typereport: 'general',
      country: null,
      country_id: null,
      state: null,
      state_id: null,
      city: null,
      county: null,
      category: null,
      category_id: null,
      agency: null,
      agency_id: null,
      countries: [],
      states: [],
      categories: [],
      agencies: [],
      isLoadingCategory: false,
      isLoadingAgency: false,
      include_cdr: null,
      stores: [],
      stores_selected: [],
      stores_id: '',
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

    dateRangeUrl() {
      return `${this.siteURL}?from_date=${moment(
        this.formData.from_date
      ).format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format(
        'YYYY-MM-DD'
      )}&type=${this.typereport}&include_cdr=${this.include_cdr}&country=${
        this.country_id
      }&state=${this.state_id}&city=${this.city}&county=${
        this.county
      }&taxagency=${this.agency_id}&taxcategory=${this.category_id}&stores_id=${this.stores_id}`
    },
  },

  watch: {
    range(newRange) {
      this.formData.from_date = moment(newRange).startOf('year').format('YYYY-MM-DD')
      this.formData.to_date = moment(newRange).endOf('year').format('YYYY-MM-DD')
    },
  },

  async mounted() {

       
    // start - is module corepos allowed
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
    // end - is module corepos allowed


    this.siteURL = `/reports/tax-summary/${this.getSelectedCompany.unique_hash}`
    this.url = this.dateRangeUrl
    this.typereport = 'general'
    this.fetchInitData()
  },

  methods: {
    ...mapActions('taxCategories', ['fetchTaxCategory', 'fetchTaxCategories']),
    ...mapActions('taxAgency', ['fetchTaxAgencies']),
    ...mapActions('reportsModule', ['getReportsTaxCsvData']),
    ...mapActions('modules', ['getModules']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('corePos', ['fetchStores']),

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
    async viewReportsPDF() {
      let data = await this.getReports()
      window.open(this.getReportUrl, '_blank')
      return data
    },
    async fetchInitData() {

      let dataStore = {
        limit: 'all'
      }
      
      const responseStore = await this.fetchStores(dataStore)
      this.stores = responseStore.data.stores.data

      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }

      let resCategories = await window.axios.get('/api/v1/tax-categories')
      // load agencies
      let resAgencies = await this.fetchTaxAgencies({ limit: 'all' })

      if (resCategories) {
        this.categories = resCategories.data.taxCategories.data
      }

      if (resAgencies) {
        this.agencies = resAgencies.data.taxAgencies.data
      }
    },
    async getReports(isDownload = false) {
      this.$v.range.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      if (this.selectedType == 'By General') {
        this.typereport = 'general'
      }

      if (this.selectedType == 'By Customer') {
        this.typereport = 'customer'
      }

      if (this.selectedType == 'By Item') {
        this.typereport = 'item'
      }

      if (this.country) {
        this.country_id = this.country.id
      }

      if (this.state) {
        this.state_id = this.state.id
      }

      if (this.agency) {
        this.agency_id = this.agency.id
      }

      if (this.category) {
        this.category_id = this.category.id
      }

      if (this.stores_selected.length > 0) {
          this.stores_id += this.stores_selected.map((store) => store.id).join(",");
      }

      this.url = this.dateRangeUrl
      return true
    },
    async countrySelected(country) {
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
    async deleteCategory() {
      this.isLoadingCategory = true
      this.category = null
      this.isLoadingCategory = false
    },
    categorySelected(category) {
      this.isLoadingCategory = true
      this.category = category
      this.isLoadingCategory = false
    },
    async deleteAgency() {
      this.isLoadingAgency = true
      this.agency = null
      this.isLoadingAgency = false
    },
    agencySelected(value) {
      this.isLoadingAgency = true
      this.agency = value
      this.isLoadingAgency = false
    },
    async clearFilter() {
      this.agency = null
      this.category = null
      this.country = null
      this.county = ''
      this.city = ''
      this.state = null
      this.country_id = null
      this.state_id = null
      this.agency_id = null
      this.category_id = null
      this.stores_selected = []
      this.stores_id = ''

      let resAgencies = await this.fetchTaxAgencies({ limit: 'all' })

      if (resAgencies) {
        this.agencies = resAgencies.data.taxAgencies.data
      }
    },
    downloadReport() {
      if (!this.getReports()) {
        return false
      }
      return this.getReportUrl
      /*
      window.open(this.url + '&download=true')
      setTimeout(() => {
        this.url = this.dateRangeUrl
      }, 200)
      */
    },
    async onDownloadCsv() {
      try {
        this.getReports()
        const payload = {
          params: {
            from_date: this.formData.from_date,
            to_date: this.formData.to_date,
            country_id: this.country_id,
            state_id: this.state_id,
            agency_id: this.agency_id,
            category_id: this.category_id,
            city: this.city,
            county: this.county,
          },
        }
        let res = await this.getReportsTaxCsvData(payload)
      } catch (e) {
      }

    },
  },
}
</script>
