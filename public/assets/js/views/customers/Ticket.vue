<template>
  <base-page class="payments">
    <sw-page-header :title="$t('customer_ticket.title')">
      

      <template slot="actions">
                <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/view`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('customer_ticket.clientgoback') }}
        </sw-button>


        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/add-ticket`"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('customer_ticket.create_ticket') }}
        </sw-button>
      </template>
    </sw-page-header>

    <sw-empty-table-placeholder
      v-if="showEmptyScreen"
      :title="$t('customer_ticket.no_customer_ticket')"
      :description="$t('customer_ticket.list_of_customer_ticket')"
    >
      <capsule-icon class="mt-5 mb-4" />
      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/add-ticket`"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('customer_ticket.new_ticket') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
        </p>
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
          :label="$t('customer_ticket.summary')"
          show="summary"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.summary') }}</span>
              {{ row.summary }}
          </template>
        </sw-table-column>

           <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.departament')"
          show="departament"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.departament') }}</span>
              {{ row.departament}}
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
                :to="`${row.id}/edit-ticket`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removePayment(row.id)">
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
import CapsuleIcon from '@/components/icon/CapsuleIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
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
    EyeIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,

      filters: {
        customer: '',
        payment_mode: '',
        payment_number: '',
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

  mounted() {
  },

  destroyed() {
  },

  methods: {
    
    ...mapActions('customerTicket', [
      'fetchCustomerTickets',
      'deleteCustomerTicket',
      'setSelectAllState',
    ]),

    getStatus(status){
      if(status==='S'){
        return 'Awaiting Staff Reply'
      }
      if(status==='C'){
        return 'Awaiting Client Reply'
      }
      if(status==='I'){
        return 'In Progress'
      }
      if(status==='O'){
        return 'On Hold'
      }
      if(status==='M'){
        return 'Completed'
      }  
    },



    async fetchData({ page, filter, sort }) {
      this.isRequestOngoing = true
      let response = await this.fetchCustomerTickets({customer_id : this.$route.params.id})
      this.isRequestOngoing = false
      /* console.log("Lista para mostrar",response); */
      return {
        data: response.data.customerTicket.data,
        pagination: {
          totalPages: response.data.customerTicket.last_page,
          currentPage: page,
          count: response.data.customerTicket.total,
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
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }

      this.filters = {
        customer: '',
        payment_mode: '',
        payment_number: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    onSelectCustomer(customer) {
      this.filters.customer = customer
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
            window.toastr['success'](this.$tc('customer_ticket.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
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


