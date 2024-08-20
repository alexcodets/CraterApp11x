<template>
  <!------------ ESTIMATES ----------->
  <div class="tabs mb-5 grid col-span-12 pt-6">
    <div class="border-b tab">
      <div class="relative">
        <input
          class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
          type="checkbox"
          id="chck3"
        />
        <header
          class="
            col-span-5
            flex
            justify-between
            items-center
            py-3
            cursor-pointer
            select-none
            tab-label
          "
          for="chck3"
        >
          <span class="text-gray-500 uppercase sw-section-title">
            {{ $tc('tickets.tickets_assigned') }}
          </span>
          <div
            class="
              rounded-full
              border border-grey
              w-7
              h-7
              flex
              items-center
              justify-center
              test
            "
          >
            <!-- icon by feathericons.com -->
            <svg
              aria-hidden="true"
              class=""
              data-reactid="266"
              fill="none"
              height="24"
              stroke="#606F7B"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              viewbox="0 0 24 24"
              width="24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </header>
        <div class="tab-content">

          <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >

         <sw-table-column
          :sortable="true"
          :label="$t('customer_ticket.summary')"
          show="summary"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_ticket.summary') }}</span>

             <router-link
              :to="`/admin/tickets/main/${row.user_id}/${row.id}/view-ticket`"
              class="font-medium text-primary-500"
              v-if="readTickets"
            >
              {{ row.summary }}
              
            </router-link>
            <span v-else>
              {{ row.summary }}    
            </span>
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
      </sw-table-component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  props: {
    userId: {
      type: Number,
      required: true,
    },
    readTickets: {
      type: Boolean
    }
  },
  data: () => ({
    estimate_status: '',
    activeEstimateTab: 'All',
  }),
  methods: {
    ...mapActions('users', ['getTicketsAssignedUser']),
    ...mapActions('customerTicket', [
      'fetchCustomerTickets'
    ]),
    async fetchData({ page, filter, sort }) {
      try {
          let data = {
                assigned_id: this.userId,
                status: this.estimate_status,
                orderByField: sort.fieldName || 'created_at',
                orderBy: sort.order || 'desc',
                page,
                perPage: 10,
            }

            let response = await this.fetchCustomerTickets(data)
            return {
              data: response.data.customerTicket.data,
              pagination: {
                totalPages: response.data.customerTicket.last_page,
                currentPage: page,
                /* count: response.data.customerTicket.total, */
              },
            }
      } catch (error) {
       // console.log(error) 
      }
    },
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
  },
}
</script>

<style>
</style>