<template>
  <base-page v-if="isSuperAdmin" class="items">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('leads.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
          <sw-breadcrumb-item to="#" :title="$tc('leads.title', 2)" active />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="!showEmptyScreen"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          v-if="permissionModule.create"
          tag-name="router-link"
          to="leads/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('leads.add_lead') }}
        </sw-button>
        <sw-button
          tag-name="router-link"
          to="search"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('leads.search_lead_customer') }}
        </sw-button>
      </div>
    </div>
    <!-- Filters -->
    <slide-y-up-transition>
      <sw-filter-wrapper
        v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >
        <div class="w-100" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$tc('leads.name')">
            <sw-input
              v-model="filters.company_name"
              type="text"
              name="name"
              class="mt-2"
              autocomplete="off"
              style="min-width: 300px"
            />
          </sw-input-group>

          <sw-input-group :label="$tc('leads.email')">
            <sw-input
              v-model="filters.email"
              type="text"
              name="name"
              class="mt-2"
              autocomplete="off"
              style="min-width: 300px"
            />
          </sw-input-group>

          <sw-input-group :label="$tc('leads.phone')">
            <sw-input
              v-model="filters.phone"
              type="text"
              name="name"
              class="mt-2"
              autocomplete="off"
              style="min-width: 300px"
            />
          </sw-input-group>
        </div>

        <div class="w-100" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$t('leads.status')" class="mt-2">
            <sw-select
              v-model="filters.status"
              :options="status_options"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('leads.status')"
              :allow-empty="false"
              track-by="value"
              label="text"
              style="min-width: 300px"
            />
          </sw-input-group>

          <sw-input-group :label="$t('leads.last_contacted_date')" class="mt-2">
            <base-date-picker
              v-model="filters.last_contacted_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>
        <div class="w-100" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$t('leads.customer_type')" class="mt-2">
            <sw-select
              v-model="filters.customer_type"
              :options="customer_types_options"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('leads.customer_type')"
              :allow-empty="false"
              track-by="value"
              label="text"
              style="min-width: 300px"
            />
          </sw-input-group>

          <sw-input-group :label="$t('leads.followupdate')" class="mt-2">
            <base-date-picker
              v-model="filters.followup_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>

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
      :title="$t('leads.no_leads')"
      :description="$t('leads.list_of_leads')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        v-if="permissionModule.create"
        slot="actions"
        tag-name="router-link"
        to="leads/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('leads.add_lead') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <!-- <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ users.length }}</b>

          {{ $t('general.of') }}

          <b>{{ totalUsers }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedUsers.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleUsers">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllUsers"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllUsers"
        />
      </div> -->

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
              :id="row.company_name"
              v-model="selectField"
              :value="row.company_name"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('leads.name')"
          show="company_name"
        >
          <template slot-scope="row">
            <span>{{ $t('leads.name') }}</span>
            <router-link
              v-if="permissionModule.read"
              :to="{ path: `leads/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              <span v-if="row.customer_type === 'B'">
                {{ row.company_name }}
              </span>
              <span v-else>
                {{
                  row.first_name !== null
                    ? row.first_name
                    : 'N/A' + ' ' + row.last_name !== null
                    ? row.last_name
                    : 'N/A'
                }}
              </span>
            </router-link>
            <p v-if="permissionModule.read == false">
              <span v-if="row.customer_type === 'B'">
                {{ row.company_name }}
              </span>
              <span v-else>
                {{
                  row.first_name !== null
                    ? row.first_name
                    : 'N/A' + ' ' + row.last_name !== null
                    ? row.last_name
                    : 'N/A'
                }}
              </span>
            </p>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('leads.email')"
          show="email"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('leads.phone')"
          show="phone"
        >
          <template slot-scope="row">
            <span>{{ $t('leads.phone') }}</span>
            <span>{{ row.phone ? row.phone : 'No Contact' }} </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('leads.customer_type')"
          sort-as="customer_type"
          show="customer_type"
        >
          <template slot-scope="row">
            <span>{{ row.customer_type }}</span>
            <span v-if="row.customer_type === 'N'">None</span>
            <span v-else-if="row.customer_type === 'B'">Business</span>
            <span v-else>Residential</span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('leads.last_contacted_date')"
          show="last_contacted_date"
        >
          <template slot-scope="row">
            <span>{{ $t('leads.last_contacted_date') }}</span>
            <span
              >{{ row.formattedLastDate ? row.formattedLastDate : 'N/A' }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('leads.followupdate')"
          show="followup_date"
        >
          <template slot-scope="row">
            <span>{{ $t('leads.followupdate') }}</span>
            <span
              >{{ row.formattedFollowDate ? row.formattedFollowDate : 'N/A' }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('leads.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />
              <sw-dropdown-item
                v-if="permissionModule.read"
                tag-name="router-link"
                :to="`leads/${row.id}/view`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="permissionModule.update"
                tag-name="router-link"
                :to="`leads/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="sendSMSLead(row)">
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('leads.send_lead_sms') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="sendEmailLead(row)">
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('leads.send_email_sms') }}
              </sw-dropdown-item>

              <div v-if="row.status === 'A' && permissionModule.update">
                <sw-dropdown-item @click="convertCustomer(row)">
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('leads.convert_customer') }}
                </sw-dropdown-item>
              </div>
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
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  EyeIcon,
  PaperAirplaneIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    AstronautIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
    EyeIcon,
    PaperAirplaneIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      showEmptyScreen: false,
      filters: {
        company_name: '',
        email: '',
        type: null,
        status: null,
        customer_type: null,
        last_contacted_date: null,
        followup_date: null,
      },
      lead_type_options: [
        {
          text: 'Prepaid',
          value: 'Prepaid',
        },
        {
          text: 'Postpaid',
          value: 'Postpaid',
        },
      ],
      status_options: [
        {
          text: this.$t('leads.status_options.active'),
          value: 'A',
        },
        {
          text: this.$t('leads.status_options.completed'),
          value: 'C',
        },
      ],
      customer_types_options: [
        {
          text: this.$t('leads.customer_types_options.business'),
          value: 'B',
        },
        {
          text: this.$t('leads.customer_types_options.residential'),
          value: 'R',
        },
      ],
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('users', ['users', 'selectedUsers', 'selectAllField']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedUsers
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
  async created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
  async mounted() {
    this.permissionsUserModule()
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  methods: {
    ...mapActions('lead', ['fetchLeads']),

    ...mapActions('user', ['getUserModules']),

    ...mapActions('modal', ['openModal']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        company_name: this.filters.company_name
          ? this.filters.company_name
          : '',
        email: this.filters.email ? this.filters.email : '',
        status: this.filters.status ? this.filters.status.value : '',
        customer_type: this.filters.customer_type
          ? this.filters.customer_type.value
          : '',
        type: this.filters.type ? this.filters.type.value : '',
        phone: this.filters.phone ? this.filters.phone : '',
        last_contacted_date: this.filters.last_contacted_date
          ? this.filters.last_contacted_date
          : '',
        followup_date: this.filters.followup_date
          ? this.filters.followup_date
          : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = null

      this.isRequestOngoing = true

      try {
        response = await this.fetchLeads(data)
      } catch (error) {}
      this.isRequestOngoing = false
      this.showEmptyScreen = response.data.leads.data == 0 ?? false
      return {
        data: response.data.leads.data,
        pagination: {
          totalPages: response.data.leads.last_page,
          currentPage: page,
        },
      }
    },

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        company_name: '',
        email: '',
        type: null,
        status: null,
        customer_type: null,
        phone: null,
        last_contacted_date: null,
        followup_date: null,
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    convertCustomer(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('leads.convert_message'),
        icon: 'warning',
        buttons: true,
        // showCancelButton: true,
      }).then(async (confirm) => {
        if (confirm) {
          this.$emit('lead_to_customer', row)
          this.$router.push({ name: 'customers.create', params: row })
        }
      })
    },

    async permissionsUserModule() {
      const data = {
        module: 'lead',
      }
      const permissions = await this.getUserModules(data)

      // Valida que el usuario tenga permiso para ingresar al módulo
      if (permissions.super_admin === false) {
        if (permissions.exist === false) {
          // console.log('Redirigiendo al dashboard de administrador...')
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions === null || modulePermissions.access === 0) {
            // console.log('Redirigiendo al dashboard de administrador...')
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // Valida que el usuario tenga los permisos create, read, delete, update
      if (permissions.super_admin === true) {
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist === true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create === 1) {
          //console.log('El usuario tiene permiso de creación.')
          this.permissionModule.create = true
        }
        if (modulePermissions.update === 1) {
          //console.log('El usuario tiene permiso de actualización.')
          this.permissionModule.update = true
        }
        if (modulePermissions.delete === 1) {
          //console.log('El usuario tiene permiso de eliminación.')
          this.permissionModule.delete = true
        }
        if (modulePermissions.read === 1) {
          //console.log('El usuario tiene permiso de lectura.')
          this.permissionModule.read = true
        }
      }
    },

    async sendSMSLead(lead) {
      this.openModal({
        title: this.$t('leads.send_lead_sms'),
        componentName: 'SendLeadSMSModal',
        id: lead.id,
        data: lead,
        variant: 'lg',
      })
    },

    async sendEmailLead(lead) {
      this.openModal({
        title: this.$t('leads.send_email_sms'),
        componentName: 'SendLeadEmailModal',
        id: lead.id,
        data: lead,
        variant: 'lg',
      })
    },
  },
}
</script>