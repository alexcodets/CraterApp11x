<template >
  <!-- <base-page v-if="isSuperAdmin" > -->
  <base-page class="tickets-customer">
    <sw-page-header :title="$t('customer_ticket.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('customer_ticket.title', 2)"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalCustomerTickets"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="tickets/add"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('tickets.add') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group
          :label="$t('customer_ticket.departament')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-select
            v-model="filters.departament"
            :options="departaments"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('customer_ticket.departament')"
            label="name"
            class="mt-2"
            @click="filter = !filter"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('expenses.from_date')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <base-date-picker
            v-model="filters.from_date"
            :calendar-button="true"
            class="mt-2"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('expenses.to_date')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            class="mt-2"
            calendar-button-icon="calendar"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('tickets.departaments.default_priority')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-select
            v-model="filters.priority"
            :options="default_prioritys"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="true"
            class="mt-2"
            :placeholder="$t('tickets.departaments.default_priority')"
            label="text"
            track-by="value"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('providers.status')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-select
            v-model="filters.status"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :tabindex="16"
            :allow-empty="true"
            class="mt-2"
            :placeholder="$t('providers.status')"
            label="text"
            track-by="value"
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

    <!-- No hay lista -->
    <sw-empty-table-placeholder
      v-if="showEmptyScreen"
      :title="$t('customer_ticket.no_customer_ticket')"
      :description="$t('customer_ticket.list_of_customer_ticket')"
    >
      <capsule-icon class="mt-5 mb-4" />
      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`tickets/add`"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('customer_ticket.new_ticket') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
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
        <p class="text-sm"></p>
      </div>

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
          <div slot-scope="row" class="relative block">
            <sw-checkbox
              :id="row.id"
              v-model="selectField"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.ticket_number')"
          show="ticket_number"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.ticket_number') }}</span>
              {{ row.ticket_number}}
          </template>
        </sw-table-column>


        <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.summary')"
          show="summary"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.summary') }}</span>

            <router-link
              :to="`tickets/${row.user_id}/${row.id}/view`"
              class="font-medium text-primary-500"
            >
              {{ row.summary }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.departament')"
          show="departament"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.departament') }}</span>
            {{ row.departament }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.assignedTo')"
          show="assigned"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.assignedTo') }}</span>
            {{ row.assigned }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.status') }}</span>
            {{ getStatus(row.status) }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payments.date')"
          sort-as="created_at"
          show="formattedCustomerNoteDate"
        />

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`tickets/${row.user_id}/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <!-- <sw-dropdown-item
                tag-name="router-link"
                :to="`tickets/${row.user_id}/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removePayment(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item> -->
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </base-page>
  <!-- </base-page> -->
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
// import SidebartDepartaments from '../SidebartTickets'
import CapsuleIcon from '@/components/icon/CapsuleIcon'
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
    // SidebartDepartaments,
    EyeIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      assignedTo: [],
      users_select: [],
      default_prioritys: [
        {
          value: 'E',
          text: 'Emergency',
        },
        {
          value: 'C',
          text: 'Critical',
        },
        {
          value: 'H',
          text: 'High',
        },
        {
          value: 'M',
          text: 'Medium',
        },
        {
          value: 'L',
          text: 'Low',
        },
      ],

      status: [
        {
          value: 'S',
          text: 'Awaiting Staff Reply',
        },
        {
          value: 'C',
          text: 'Awaiting Client Reply',
        },
        {
          value: 'I',
          text: 'In Progress',
        },
        {
          value: 'O',
          text: 'On Hold',
        },
        {
          value: 'M',
          text: 'Completed',
        },
      ],

      filters: {
        user: '',
        departament: '',
        assignedTo: '',
        from_date: '',
        to_date: '',
        priority: '',
        status: '',
      },
    }
  },

  computed: {
    showEmptyScreen() {
      /* console.log("Aqui estoy",this.totalCustomerTickets); */
      return !this.totalCustomerTickets && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),
    ...mapGetters('users', ['users']),
    ...mapGetters('user', ['currentUser', 'settingsCompany']),
    ...mapGetters('ticketDepartament', ['departaments']),
    ...mapGetters('customerTicket', [
      'totalCustomerTickets',
      'CustomerNote',
      'selectAllField',
    ]),

    selectField: {
      get: function () {
        return this.selectedPayments
      },
      set: function (val) {
        this.selectPayment(val)
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

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  async created() {
    if ( this.settingsCompany.enable_tickets_customer === "0") {
      this.$router.push('./views/errors/404.vue')
    }
    let data = {
      name: '',
      orderByField: 'created_at',
      orderBy: 'desc',
    }
    await this.fetchDepartaments(data)
  },

  methods: {
    ...mapActions('customerTicket', [
      'fetchCustomerTickets',
      'deleteCustomerTicket',
      'getListUsersCustomers',
      'setSelectAllState',
    ]),

    ...mapActions('ticketDepartament', [
      'fetchDepartaments',
      'fetchDepartament',
    ]),
    ...mapActions('users', ['fetchUsers']),

    getStatus(status) {
      if (status === 'S') {
        return 'Awaiting Staff Reply'
      }
      if (status === 'C') {
        return 'Awaiting Client Reply'
      }
      if (status === 'I') {
        return 'In Progress'
      }
      if (status === 'O') {
        return 'On Hold'
      }
      if (status === 'M') {
        return 'Completed'
      }
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id: -1,
        summary: null,
        note: null,
        user_id: this.currentUser.id,
        dep_id:
          this.filters.departament !== null
            ? this.filters.departament.id
            : null,
        assigned_id: null,

        from_date:
          this.filters.from_date === ''
            ? this.filters.from_date
            : this.filters.from_date,

        to_date:
          this.filters.to_date === ''
            ? this.filters.to_date
            : this.filters.to_date,
        priority: this.filters.priority ? this.filters.priority.value : null,
        status: this.filters.status ? this.filters.status.value : null,

        orderByField: sort.fieldName || 'created_at',

        orderBy: sort.order || 'desc',
        page,
      }

      /* console.log("Lista para enviar",response,this.filters.departament.id); */
      this.isRequestOngoing = true
      let response = await this.fetchCustomerTickets(data)
      this.isRequestOngoing = false
      /*  console.log("Lista para mostrar",response); */
      /*return */
      return {
        data: response.data.customerTicket.data,
        pagination: {
          totalPages: response.data.customerTicket.last_page,
          currentPage: page,
          /* count: response.data.customerTicket.total, */
        },
      }
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    setFilters() {
      this.refreshTable()
    },

    onSelectCustomer(customer) {
      this.filters.user = customer
    },

    clearFilter() {
      /* if (this.filters.user) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.user
        )
      } */

      this.filters = {
        user: '',
        departament: '',
        assignedTo: '',
        from_date: '',
        to_date: '',
        priority: '',
        status: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removePayment(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customer_ticket.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomerTicket({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('customer_ticket.deleted_message', 1)
            )
            this.$refs.table.refresh()
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.user = ''
      this.refreshTable()
    },

    showModel(selectedRow) {
      this.selectedRow = selectedRow
      this.$refs.Delete_modal.open()
    },

    async removeSelectedItems() {
      this.$refs.Delete_modal.close()
      await this.selectedRow.forEach((row) => {
        this.deleteCustomerNote(this.id)
      })
      this.$refs.table.refresh()
    },
  },
}
</script>
