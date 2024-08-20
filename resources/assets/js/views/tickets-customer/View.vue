<template>
  <!-- Base  -->
  <base-page>
    <!-- Header  -->
    <sw-page-header class="mb-3" :title="$t('customer_ticket.view_ticket')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="/customer/tickets"
          :title="$tc('customer_ticket.title', 2)"
        />
        <sw-breadcrumb-item :title="$t('customer_ticket.view_ticket')" active />
      </sw-breadcrumb>
    </sw-page-header>

    <div class="w-full">
      <div class="col-span-12">
        <sw-card v-if="ticket.customer">
          <div>
            <p class="text-gray-500 uppercase sw-section-title">
              {{ $t('customer_ticket.information_ticket') }}
            </p>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/2">
                <p class="font-bold">{{ $t('customer_ticket.customer') }}</p>
                <p class="text-gray-700 text-sm">{{ ticket.customer.name }}</p>
              </div>

              <div class="w-full md:w-1/2 mt-3 md:mt-0">
                <p class="font-bold">
                  {{ $t('customer_ticket.ticket_number') }}
                </p>
                <p class="text-gray-700 text-sm">{{ ticket.ticket_number }}</p>
              </div>
              <div class="w-full md:w-1/2 mt-3 md:mt-0">
                <p class="font-bold">{{ $t('customer_ticket.summary') }}</p>
                <p class="text-gray-700 text-sm">{{ ticket.summary }}</p>
              </div>
            </div>

            <div class="flex flex-wrap mt-5 md:mt-7">
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $tc('customer_ticket.departament') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ ticket.ticket_departament.name }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $tc('customer_ticket.assignedTo') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ ticket.assigned.name }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $tc('customer_ticket.priority') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ prioritysOptions[ticket.priority] }}
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $tc('customer_ticket.status') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    {{ statusOptions[ticket.status] }}
                  </p>
                </div>
              </div>
            </div>

            <div class="w-full mt-5 md:mt-7">
              <p class="font-bold">{{ $t('customer_ticket.details') }}</p>
              <p class="text-gray-700 text-sm">{{ ticket.note }}</p>
            </div>

            <div class="w-full mt-5 md:mt-7">
              <p class="font-bold mb-2">{{ $t('customer_ticket.user') }}</p>
              <div>
                <!-- avatar and name user -->
                <div
                  class="flex my-2"
                  v-for="(tr, indexTr) in ticket.users"
                  :key="indexTr"
                >
                  <div>
                    <!--    <img class="rounded-full w-8 h-8" :src="tr.avatar" :alt="indexTr">-->
                  </div>
                  <div class="ml-2">
                    <p class="text-gray-700 text">{{ tr.name }}</p>
                    <p class="text-gray-700 text-sm">{{ tr.email }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="relative table-container">
            <div
              class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
            >
              <p class="text-sm"></p>
            </div>

            <sw-table-component
              ref="notes_table"
              :show-filter="false"
              table-class="table"
              :data="fetchNotes"
            >
              <sw-table-column
                :sortable="true"
                :label="$t('customer_ticket.reference')"
                show="reference"
              >
                <template slot-scope="row">
                  <span>{{ $t('customer_ticket.reference') }}</span>
                  <span
                    class="font-medium text-primary-500 cursor-pointer"
                    @click="viewNote(ticket, row)"
                  >
                    {{ row.reference }}
                  </span>
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('customer_ticket.subject')"
                show="subject"
              >
                <template slot-scope="row">
                  <span>{{ $t('customer_ticket.subject') }}</span>
                  {{ row.subject }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('customer_ticket.message')"
                show="message"
              >
                <template slot-scope="row">
                  <span>{{ $t('customer_ticket.message') }}</span>
                  {{ row.message }}
                </template>
              </sw-table-column>

              <sw-table-column
                :sortable="true"
                :label="$t('customer_ticket.user')"
                show="user_name"
              >
                <template slot-scope="row">
                  <span>{{ $t('customer_ticket.user') }}</span>
                  {{ row.user_name }}
                </template>
              </sw-table-column>

              <sw-table-column :sortable="true" :label="'Datetime'">
                <template slot-scope="row">
                  <span> {{ $t('general.datetime') }} </span>
                  {{ row.date }} {{ row.time }}
                </template>
              </sw-table-column>

              <sw-table-column cell-class="action-dropdown no-click">
                <template slot-scope="row">
                  <span>{{ $t('general.actions') }}</span>

                  <sw-dropdown>
                    <dot-icon slot="activator" />

                    <sw-dropdown-item @click="viewNote(ticket, row)">
                      <eye-icon class="h-5 mr-3 text-gray-600" />
                      {{ $t('general.view') }}
                    </sw-dropdown-item>

                    <!-- seccion para cambiar el precio -->
                  </sw-dropdown>
                </template>
              </sw-table-column>
            </sw-table-component>
          </div>
        </sw-card>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PencilIcon, TrashIcon, EyeIcon } from '@vue-hero-icons/outline'
export default {
  components: {
    PencilIcon,
    TrashIcon,
    EyeIcon,
  },
  data() {
    return {
      isLoading: false,
      ticket: {},
      prioritysOptions: {
        E: 'Emergency',
        C: 'Critical',
        H: 'High',
        M: 'Medium',
        L: 'Low',
      },
      statusOptions: {
        S: 'Awaiting Staff Reply',
        C: 'Awaiting Client Reply',
        I: 'In Progress',
        O: 'On Hold',
        M: 'Completed',
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
  },
  created() {
    this.getTicket()
  },
  methods: {
    ...mapActions('customerTicket', [
      'fetchCustomerTicket',
      'deleteCustomerTicket',
      'fetchCustomerTicketNotes',
    ]),
    editTicket({ id, user_id }) {
      this.$router.push({
        name: 'tickets.customer.edit',
        params: {
          id: user_id,
          id1: id,
        },
      })
    },
    async getTicket() {
      try {
        this.isLoading = true
        const res = await this.fetchCustomerTicket(this.$route.params.id1)
        this.ticket = res.data.customerTicket
      } catch (e) {
        this.ticket = {}
      } finally {
        this.isLoading = false
      }
    },
    async deleteTicket(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customer_ticket.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        try {
          if (willDelete) {
            this.isLoading = false
            let res = await this.deleteCustomerTicket({ ids: [id] })

            if (res.data.success) {
              window.toastr['success'](
                this.$tc('customer_ticket.deleted_message', 1)
              )
              this.$router.push({ name: 'tickets.customer' })
            }
          }
        } catch (e) {
          window.toastr['error'](res.data.message)
        } finally {
          this.isLoading = false
        }
      })
    },
    async fetchNotes({ page, filter, sort }) {
      try {
        const params = {
          public: 1,
          ticket_id: this.$route.params.id1,
          orderByField: sort.fieldName || 'created_at',
          orderBy: sort.order || 'desc',
          page,
        }
        this.isRequestOngoing = true

        let res = await this.fetchCustomerTicketNotes(params)

        this.isRequestOngoing = false

        this.notes_count = res.data.notes.total
        return {
          data: res.data.notes.data,
          pagination: {
            totalPages: res.data.notes.last_page,
            currentPage: page,
          },
        }
      } catch (e) {
        this.notes = []
      }
    },
    viewNote({ id, user_id }, note) {
     // console.log('viewNote called with:', { id, user_id }, note)

      const data = {
        ticket: this.ticket,
        note: note,
        isEdit: false,
      }
      //console.log('Data object created:', data)

      this.$router.push({
        name: 'tickets.customernote.view',
        params: {
          id: user_id,
          id1: id,
          data: data,
        },
      })
     /* console.log('Router push called with:', {
        name: 'tickets.customernote.view',
        params: {
          id: user_id,
          id1: id,
          data: data,
        },
      })*/
    },
  },
}
</script>
