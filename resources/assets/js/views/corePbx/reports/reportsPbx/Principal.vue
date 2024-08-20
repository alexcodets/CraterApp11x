<template >
  <!-- <base-page v-if="isSuperAdmin" > -->
  <div :class="{ 'xl:pl-64': showSideBar }">
    <sw-page-header :title="$t('reports.reports_PBX')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="#" :title="$tc('reports.reports_PBX')" active />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/packages`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <div class="mr-3 hidden xl:block">
          <sw-button
            class=""
            variant="primary-outline"
            @click="toggleListCustomers"
          >
            {{ $t('tickets.departaments.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>
      </template>
    </sw-page-header>

    <slide-x-left-transition>
      <Sidebart-departaments v-show="showSideBar" />
    </slide-x-left-transition>


    <div class="relative flex flex-wrap bg-gray-200 mt-4 rouded-lg p-4">
      <sw-input-group
        :label="$t('general.date_range')"
        class="mt-2 md:w-1/2 md:pr-2"
      >
        <date-range-picker
          class="w-full rounded date-range-picker"
          v-model="filters.dateRange"
          time-picker
          :time-picker-increment="5"
          time-picker24-hour
          append-to-body
          :ranges="rangesPresets"
        >
          <template v-slot:input="picker">
            {{ formatDate(picker) }}
          </template>
          <div slot="footer" slot-scope="data" class="slot flex mb-1 mr-1">
            <div class="ml-auto flex items-center">
              <sw-button
                @click="data.clickApply"
                v-if="!data.in_selection"
                :variant="'primary-outline'"
                size="sm"
                class="ml-2"
              >
                {{ $t('general.search') }}
              </sw-button>
            </div>
          </div>
        </date-range-picker>
      </sw-input-group>

      <sw-input-group :label="$t('general.type')" class="mt-2 md:w-1/2 md:pr-2">
        <sw-select
          v-model="typeSearch"
          :options="dcrTypeOptions"
          :group-select="false"
          :searchable="true"
          :show-labels="false"
          :placeholder="$t('general.type')"
          :allow-empty="false"
        />
      </sw-input-group>

      <sw-input-group
        v-if="typeSearch == 'Department'"
        :label="$t('general.departments')"
        class="mt-2 md:w-1/3 md:pr-2"
      >
        <sw-select
          v-model="filters.departments"
          :options="departmentOptions"
          :group-select="false"
          :searchable="true"
          :multiple="true"
          :show-labels="false"
          :placeholder="$t('general.departments')"
          :allow-empty="true"
          label="name"
          track-by="id"
        />
      </sw-input-group>

      <sw-input-group
        v-if="typeSearch == 'Tenant' || typeSearch == 'Extension'"
        :label="$t('general.pbx_tenant')"
        class="mt-2 md:w-1/3 md:pr-2"
      >
        <sw-select
          v-model="filters.tenant"
          :options="pbxTenantsOptions"
          :group-select="false"
          :searchable="true"
          :multiple="false"
          :show-labels="false"
          :placeholder="$t('general.pbx_tenant')"
          :allow-empty="false"
          @select="getExtensions"
          label="name"
        />
      </sw-input-group>

      <sw-input-group
        v-if="typeSearch == 'Extension'"
        :label="$t('general.extension')"
        class="mt-2 md:w-1/3 md:pr-2"
      >
        <sw-select
            v-model="filters.extension"
            :options="extensionOptions"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :multiple="true"
            class="mt-2"
            track-by="id"
            label="name"
          />
        <!-- <template v-slot:options="{ data }">
          <sw-button
            @click="data.clickApply"
            v-if="!data.in_selection"
            :variant="'primary-outline'"
            size="sm"
            class="ml-2"
          >
            {{ $t('general.search') }}
          </sw-button>
        </template> -->
      </sw-input-group>

      <sw-input-group
        v-if="typeSearch == 'Tenant' || typeSearch == 'Extension'"
        :label="$t('general.include_suspended_services')"
        class="mt-2 md:w-1/4 md:pr-2"
      >
        <sw-checkbox v-model="includeServicesSuspended" />
      </sw-input-group>

      <label
        class="absolute text-sm leading-snug text-black cursor-pointer"
        @click="resetForm"
        style="top: 10px; right: 15px"
        >{{ $t('general.clear_all') }}
      </label>

      <div class="w-full flex flex-wrap items-end justify-end mt-3">
        <sw-button
          @click="searchReportCdrMetho()"
          :loading="isRequestOngoing"
          :disabled="isRequestOngoing"
          class="md:mx-1"
          :variant="'primary-outline'"
        >
          {{ $t('general.search') }}
        </sw-button>
      </div>
    </div>
    <div class="w-full flex flex-wrap items-end justify-end mt-3">
      <h4 class="text-lg text-primary mt-3">Total:</h4>
      <h4 class="text-lg text-primary mt-3 ml-1">{{quantityExtensions}}</h4>
    </div>
    <div class="overflow-x-auto">
      <table
        class="w-full item-table bg-white border border-gray-200 border-solid"
      >
        <thead>
          <tr>
            <th
              v-for="(head, headIndex) in reportsCDRDataHeader"
              :key="headIndex"
              class="
                px-5
                py-3
                text-sm
                not-italic
                font-medium
                leading-5
                text-left text-gray-700
                border-t border-b border-gray-200 border-solid
                bg-gray-50
              "
            >
              <span>
                <!-- {{ $tc('customer_ticket.departament') }} -->
                {{ head }}
              </span>
            </th>
          </tr>
        </thead>
        <tbody v-for="(tr, indexTr) in reportsCDRData" :key="indexTr" :class="{'bg-primary-100': indexTr%2!==0}">
          <tr class="py-5">
            <!-- extension -->
            <td
              rowspan="4"
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              <!-- <p>{{ tr.extension }}</p> -->
              {{ tr.extensionName }}
            </td>
            <!-- department -->
            <td
              rowspan="4"
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.department }}
            </td>
            <!-- service -->
            <td
              rowspan="4"
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.service }}
            </td>
            <!-- items.inbound -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
            {{  $t('general.inbound') }}
            </td>
            <!-- items.inbound.today -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.today }}
            </td>
            <!-- items.inbound.last24Hours -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.last24Hours }}
            </td>
            <!-- items.inbound.totalCalls -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.totalCalls  }}
            </td>
            <!-- items.inbound.AverageCallPerDay -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ (parseNumber(tr.items.inbound.totalCalls) / dayDiff).toFixed(2) }}
            </td>
            <!-- items.inbound.totalTime -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ parseNumber(tr.items.inbound.totalTime)}}
            </td>
            <!-- items.inbound.AverageTimePerCall  -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ (parseNumber(tr.items.inbound.totalTime) / parseNumber(tr.items.inbound.totalCalls > 0 ? tr.items.inbound.totalCalls : 1)).toFixed(2) }}
            </td>
            <!-- items.inbound.callAnswered -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.callAnswered }}
            </td>
            <!-- items.inbound.callUnaswered -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.callUnaswered }}
            </td>
            <!-- items.inbound.callBusy -->
            <!-- <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.callBusy }}
            </td> -->
             <!-- items.inbound.callFailed -->
            <!-- <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.inbound.callFailed }}
            </td> -->
          </tr>
          <!-- items.outbound -->
          <tr>
            <!-- items.outbound -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
            {{  $t('general.outbound') }}
            </td>
            <!-- items.outbound.today -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.today }}
            </td>
            <!-- items.outbound.last24Hours -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.last24Hours }}
            </td>
            <!-- items.outbound.totalCalls -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.totalCalls  }}
            </td>
            <!-- items.outbound.AverageCallPerDay -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ (parseNumber(tr.items.outbound.totalCalls) / dayDiff).toFixed(2) }}
            </td>
            <!-- items.outbound.totalTime -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ parseNumber(tr.items.outbound.totalTime) }}
            </td>
            <!-- items.outbound.AverageTimePerCall  -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ (parseNumber(tr.items.outbound.totalTime > 0 ? tr.items.outbound.totalTime : 0) / parseNumber(tr.items.outbound.totalCalls > 0 ? tr.items.outbound.totalCalls : 1 )).toFixed(2) }}
            </td>
            <!-- items.outbound.callAnswered -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.callAnswered }}
            </td>
            <!-- items.outbound.callUnaswered -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.callUnaswered }}
            </td>
            <!-- items.outbound.callBusy -->
            <!-- <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.callBusy }}
            </td> -->
             <!-- items.outbound.callFailed -->
            <!-- <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.outbound.callFailed }}
            </td> -->
          </tr>


          <!-- Total (items.global) -->
          <tr>
            <!-- items.global -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              Total
            </td>
            <!-- items.global.today -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.today }}
            </td>
            <!-- items.global.last24Hours -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.last24Hours }}
            </td>
            <!-- items.global.totalCalls -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.totalCalls  }}
            </td>
            <!-- items.global.AverageCallPerDay -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ (parseNumber(tr.items.global.totalCalls) / dayDiff ).toFixed(2) }}
            </td>
            <!-- items.global.totalTime -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ parseNumber(tr.items.global.totalTime) }}
            </td>
            <!-- items.global.AverageTimePerCall  -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ (parseNumber(tr.items.global.totalTime > 0 ? tr.items.global.totalTime : 0) / parseNumber(tr.items.global.totalCalls > 0 ? tr.items.global.totalCalls : 1)).toFixed(2) }}
            </td>
            <!-- items.global.callAnswered -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.callAnswered }}
            </td>
            <!-- items.global.callUnaswered -->
            <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.callUnaswered }}
            </td>
            <!-- items.global.callBusy -->
            <!-- <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.callBusy }}
            </td> -->
             <!-- items.global.callFailed -->
            <!-- <td
              class="
                p-5
                whitespace-nowrap
                border-t border-b border-gray-200 border-solid
              "
            >
              {{ tr.items.global.callFailed }}
            </td> -->
          </tr>
        </tbody>
      </table>
      <div v-if="notData && reportsCDRData.length == 0" class="flex flex-wrap justify-center items-center text-center p-3">
        <h2 class="text-xl font-bold text-primary mt-3"> {{  $t('general.warning_service20') }}</h2>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import SidebartDepartaments from '../SidebartTickets'
import CapsuleIcon from '@/components/icon/CapsuleIcon'

import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

import moment from 'moment'

import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CapsuleIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    ClipboardListIcon,
    SidebartDepartaments,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    DateRangePicker,
  },
  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: false,
      dcrTypeOptions: ['Department', 'Tenant', 'Extension'],
      pbxTenantSelect: {},
      filters: {
        dateRange: {
          startDate:moment().subtract(29, 'days').startOf('day').toDate(),
          endDate: moment().endOf('day').toDate(),
        },
        departments: [],
        tenant: {},
        extension: []
      },
      pbxTenantsOptions: [],
      typeSearch: 'Department',
      departmentOptions: [],
      extensionOptions: [],
      includeServicesSuspended: false,
      showSideBar: true,
      notData: false,
      rangesPresets: {
        Today: [
          moment().startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        Yesterday: [
          moment().subtract(1, 'days').startOf('day').toDate(),
          moment().subtract(1, 'days').endOf('day').toDate(),
        ],
        'Last 7 days': [
          moment().subtract(6, 'days').startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        'Last 30 days': [
          moment().subtract(29, 'days').startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        'Last 90 days': [
          moment().subtract(89, 'days').startOf('day').toDate(),
          moment().endOf('day').toDate(),
        ],
        'This month': [
          moment().startOf('month').toDate(),
          moment().endOf('month').toDate(),
        ],
        'Last month': [
          moment().subtract(1, 'month').startOf('month').toDate(),
          moment().subtract(1, 'month').endOf('month').toDate(),
        ],
      },
      reportsCDRDataHeader: [
        this.$t('general.extension'),
        this.$t('general.departments'),
        this.$t('navigation.services'),
        this.$t('general.type'),
        this.$t('general.today'),
        this.$t('general.last_t'),
        this.$t('general.qty_calls'),
        this.$t('general.avg_calls'),
        this.$t('general.total_time'),
        this.$t('general.avg_time'),
        this.$t('general.answered'),
        this.$t('general.not_answered'),
        // 'Busy',
        // 'Failed',
      ],

      reportsCDRData: [],
      quantityExtensions: 0,
    }
  },
  watch: {
    typeSearch(val) {
      if (val == 'Department') {
        this.getDepartments()
      } else if (val == 'Tenant') {
        this.getPbxTenants()
      } else if (val == 'Extension') {
        this.getPbxTenants()
        this.getExtensions(this.filters?.tenant)
      }
      this.resetForm()
    },
  },

  computed: {
    ...mapGetters('users', ['users']),
    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
    dayDiff() {
      /*console.log(moment(this.filters.dateRange.endDate).diff(
        moment(this.filters.dateRange.startDate),
        'days'
      ), "moment")*/
      return moment(this.filters.dateRange.endDate).diff(
        moment(this.filters.dateRange.startDate),
        'days'
      ) || 1
    },
  },

  mounted() {},

  destroyed() {},

  async created() {
    this.getDepartments()
  },

  methods: {
    ...mapActions('customSearch', [
      'fetchCustomSearch',
      'indexPbxTenants',
      'indexExtensions',
      'fetchReportCdr',
    ]),
    ...mapActions('users', ['fetchUsers']),

    async searchReportCdrMetho() {
      try {
        this.isRequestOngoing = true
        this.quantityExtensions = 0
        let payload = ""
        let filter = {}
        filter.typeSearch = this.typeSearch
        if (
          this.filters.dateRange.startDate &&
          this.filters.dateRange.endDate
        ) {
          filter.startDate = moment(this.filters.dateRange.startDate).format('YYYY-MM-DD_HH:mm:ss') 
          filter.endDate = moment(this.filters.dateRange.endDate).format('YYYY-MM-DD_HH:mm:ss')
        }
        if (
          filter.typeSearch == 'Department' &&
          this.filters.departments.length > 0
        ) {
          filter.departments = this.filters.departments.map(item => item.id)
          payload = `typeSearch=${filter.typeSearch}&startDate=${filter.startDate}&endDate=${filter.endDate}`

          for (let i = 0; i < filter.departments.length; i++) {
            payload += `&departments[${i}][id]=${filter.departments[i]}`
          }

        }
        if (filter.typeSearch == 'Tenant' && this.filters.tenant) {
          filter.tenant = []
          filter.tenant.push({
            tenant_id: this.filters.tenant.tenantid,
            pbx_server_id: this.filters.tenant.pbx_server_id,
          })
          payload = `typeSearch=${filter.typeSearch}&startDate=${filter.startDate}&endDate=${filter.endDate}&tenant[0][tenantid]=${this.filters.tenant.tenantid}&tenant[0][pbx_server_id]=${this.filters.tenant.pbx_server_id}&tenant[0][tenant_id]=${this.filters.tenant.tenantid}&includeServicesSuspended=${this.includeServicesSuspended ? 1 : 0}`
        }
        if (
          filter.typeSearch == 'Extension' &&
          this.filters.extension.length > 0
        ) {
          filter.extension = this.filters.extension
          payload = `typeSearch=${filter.typeSearch}&startDate=${filter.startDate}&endDate=${filter.endDate}`

          for (let i = 0; i < filter.extension.length; i++) {
            payload += `&extensions[${i}][id]=${filter.extension[i].id}`
          }
        }
        filter.includeServicesSuspended = this.includeServicesSuspended
        // filter  pasar a form data para que sepa que es un form data
        const { data } = await this.fetchReportCdr(payload)
        if(data.success){
          this.reportsCDRData = data.data || []
          this.quantityExtensions = data.extensions.quantity || 0
          this.notData = false
        }else{
          this.reportsCDRData = []
          this.notData = true
          // window.toastr['error'](data.message)
        }
        //console.log(this.reportsCDRData);
      } catch (e) {
        console.log(e)
        window.toastr['error'](e.message)
      }finally{
        this.isRequestOngoing = false
      }
    },

    formatDate(dateRange) {
      const start = moment(dateRange.startDate).format('MM/DD/YYYY HH:mm')
      const end = moment(dateRange.endDate).format('MM/DD/YYYY HH:mm')
      return `${start} - ${end}`
    },

    clearFilter() {
      this.filters = {
        dateRange: {
          startDate: '',
          endDate: '',
        },
      }
      this.typeSearch = 'Department'
    },
    async getDepartments() {
      try {
        this.isRequestOngoing = true
        const data = {
          orderByField: 'created_at',
          orderBy: 'desc',
          limit: 1000,
        }
        const response = await this.fetchCustomSearch(data)

        this.departmentOptions = response.data.customSearches.data
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },
    async getPbxTenants() {
      try {
        const filters = {
          includeServicesSuspended: this.includeServicesSuspended,
        }
        const response = await this.indexPbxTenants(filters)
        this.pbxTenantsOptions = Object.values(response.data.data)
      } catch (e) {
        console.log(e)
      }
    },
    async getExtensions(tenant) {
      try {
        // si viene vacio el tenant, no se busca por tenant
        if (tenant) {
          const response = await this.indexExtensions(tenant)
          this.extensionOptions = Object.values(response.data.data)
        } else {
          this.extensionOptions = []
        }
      } catch (error) {
        console.log(error)
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
    },
    parseNumber(number) {
      if (number) {
        return parseFloat(number).toFixed(2)
      }
      return 0
    },
    resetForm() {
      this.filters.departments = []
      this.filters.tenant = null
      this.filters.extension = []
    },
  },
}
</script>