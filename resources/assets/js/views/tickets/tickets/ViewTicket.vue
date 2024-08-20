<template>
  <!-- Base  -->
  <base-page v-if="isSuperAdmin">
    <!-- Header  -->
    <sw-page-header class="mb-3" :title="$t('customer_ticket.view_ticket')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="/admin/tickets/main"
          :title="$tc('customer_ticket.title', 2)"
        />
        <sw-breadcrumb-item :title="$t('customer_ticket.view_ticket')" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/tickets/main`"
          class="mr-4"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>
        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${ticket.customer.id}/ticket`"
          class="mr-4"
          variant="primary-outline"
        >
          {{ $t('tickets.go_to_customer') }}
        </sw-button>
        <!-- editar -->
        <sw-button
          @click="editTicket(ticket)"
          class="mr-3"
          variant="primary-outline"
        >
          <pencil-icon class="h-5 mr-3 text-gray-600" />
          {{ $t('general.edit') }}
        </sw-button>

        <!-- eliminar -->
        <sw-button
          @click="deleteTicket(ticket.id)"
          type="button"
          class="btn btn-primary"
          :loading="isLoading"
          :disabled="isLoading"
        >
          <trash-icon class="h-5 mr-3 text-gray-600" />
          <span v-if="!isLoading">{{ $tc('general.delete') }}</span>
        </sw-button>
      </template>
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
                <p class="text-gray-700 text-sm">
                  {{ ticket.customer.customcode }}
                </p>
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
                                 

              <div v-if="ticket.priority == 'E' || ticket.priority == 'C' || ticket.priority == 'H'">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                :color="$utils.getBadgeStatusColor('OVERDUE').color" class="px-3 py-1">
                {{ getPriority(ticket.priority) }}
              </sw-badge>
            </div>

            <div v-if="ticket.priority == 'M'">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
                :color="$utils.getBadgeStatusColor('VIEWED').color" class="px-3 py-1">
                {{ getPriority(ticket.priority) }}
              </sw-badge>
            </div>

            <div v-if="ticket.priority == 'L'">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                :color="$utils.getBadgeStatusColor('COMPLETED').color" class="px-3 py-1">
                {{ getPriority(ticket.priority) }}
              </sw-badge>
            </div>
                  </p>
                </div>
              </div>
              <div class="w-full md:w-1/4">
                <div class="font-bold py-2">
                  {{ $tc('customer_ticket.status') }}
                </div>
                <div>
                  <p class="text-gray-700 text-sm">
                    <div v-if="ticket.status == 'S' ">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                :color="$utils.getBadgeStatusColor('OVERDUE').color" class="px-3 py-1">
                {{ getStatus(ticket.status) }}
              </sw-badge>
            </div>


            <div v-if="ticket.status == 'C' ">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('UNPAID').bgColor"
                :color="$utils.getBadgeStatusColor('UNPAID').color" class="px-3 py-1">
                {{ getStatus(ticket.status) }}
              </sw-badge>
            </div>

            <div v-if="ticket.status == 'I' ">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
                :color="$utils.getBadgeStatusColor('VIEWED').color" class="px-3 py-1">
                {{ getStatus(ticket.status) }}
              </sw-badge>
            </div>

            
            <div v-if="ticket.status == 'O' ">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('SENT').bgColor"
                :color="$utils.getBadgeStatusColor('SENT').color" class="px-3 py-1">
                {{ getStatus(ticket.status) }}
              </sw-badge>
            </div>

            <div v-if="ticket.status == 'M' ">
                <sw-badge :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                :color="$utils.getBadgeStatusColor('COMPLETED').color" class="px-3 py-1">
                {{ getStatus(ticket.status) }}
              </sw-badge>
            </div>
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
                    <!--  <img class="rounded-full w-8 h-8" :src="tr.avatar" :alt="indexTr">-->
                  </div>
                  <div class="ml-2">
                    <p class="text-gray-700 text">{{ tr.name }}</p>
                    <p class="text-gray-700 text-sm">{{ tr.email }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </sw-card>
      </div>
    </div>

    <sw-page-header class="mt-5" title=" ">
      <template slot="actions">
        <div class="w-full">
          <div class="col-span-12">
            <sw-button
              v-show="notes_count"
              variant="primary-outline"
              size="lg"
              @click="toggleFilter"
            >
              {{ $t('general.filter') }}
              <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
            </sw-button>

            <sw-button
              class="mr-3"
              variant="primary"
              @click="createNote(ticket)"
            >
              {{ $t('customer_ticket.create_note') }}
            </sw-button>
          </div>
        </div>
      </template>
    </sw-page-header>

    <!-- table of the notes of tickets -->
    <div class="w-full mt-5">
      <div class="col-span-12">
        <sw-card>
          <div>
            <slide-y-up-transition>
              <sw-filter-wrapper v-show="showFilters" class="mt-3">
                <sw-input-group
                  :label="$tc('customer_ticket.reference')"
                  class="flex-1 mt-2 mr-4"
                >
                  <sw-input
                    v-model="filters.reference"
                    type="text"
                    name="reference"
                    class="mt-2"
                    autocomplete="off"
                  >
                  <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
                </sw-input>
                </sw-input-group>

                <sw-input-group
                  :label="$tc('customer_ticket.subject')"
                  class="flex-1 mt-2 mr-4"
                >
                  <sw-input
                    v-model="filters.subject"
                    type="text"
                    name="subject"
                    class="mt-2"
                    autocomplete="off"
                    />
                 
                
                </sw-input-group>

                <sw-input-group
                  :label="$tc('customer_ticket.user')"
                  class="flex-1 mt-2 mr-4"
                >
              

                  <sw-select
              v-model="filters.user"
              :options="users"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customer_ticket.assignedTo')"
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

                <label
                  class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
                  style="top: 10px; right: 15px"
                  @click="clearFilter"
                >
                  {{ $t('general.clear_all') }}</label
                >
              </sw-filter-wrapper>
            </slide-y-up-transition>
          </div>
          <!-- end filters -->
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

                    <sw-dropdown-item @click="editNote(ticket, row)">
                      <pencil-icon class="h-5 mr-3 text-gray-600" />
                      {{ $t('general.edit') }}
                    </sw-dropdown-item>
                    <!-- seccion para cambiar el precio -->
                    <sw-dropdown-item @click="deleteNote(row.note_id)">
                      <trash-icon class="h-5 mr-3 text-gray-600" />
                      {{ $t('general.delete') }}
                    </sw-dropdown-item>
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
import {
  PencilIcon,
  TrashIcon,
  RefreshIcon,
  FilterIcon,
  XIcon,
  EyeIcon,
  HashtagIcon,
} from '@vue-hero-icons/outline'
export default {
  components: {
    PencilIcon,
    TrashIcon,
    RefreshIcon,
    FilterIcon,
    XIcon,
    EyeIcon,
    HashtagIcon,
  },

  data() {
    return {
      showFilters: false,
      isLoading: false,
      ticket: {},
      notes: [],
      notes_count: 0,
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
      filters: {
        reference: '',
        subject: '',
        user: '',
        from_date: '',
        to_date: '',
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('users', ['users']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.notes_count && !this.notes_count
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },
  async created() {
    this.getTicket()
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    await this.fetchUsers({ limit: 'all' })
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('customerTicket', [
      'fetchCustomerTicket',
      'deleteCustomerTicket',
      'fetchCustomerTicketNotes',
      'deleteNoteTicket',
    ]),

    ...mapActions('modal', ['openModal']),
    ...mapActions('users', ['fetchUsers']),

    getStatus(status) {
      if (status === 'S') {
        return this.$tc('tickets.awat_staff')
      }
      if (status === 'C') {
        return this.$tc('tickets.awat_client')
      }
      if (status === 'I') {
        return this.$tc('tickets.in_progres')
      }
      if (status === 'O') {
        return this.$tc('tickets.on_hold')
      }
      if (status === 'M') {
        return this.$tc('tickets.completed')
      }
    },

    getPriority(status) {
      if (status === 'E') {
        return this.$tc('tickets.emergency')
      }
      if (status === 'C') {
        return this.$tc('tickets.critical')
      }
      if (status === 'H') {
        return this.$tc('tickets.high')
      }
      if (status === 'L') {
        return this.$tc('tickets.low')
      }
      if (status === 'M') {
        return this.$tc('tickets.medium')
      }
    },

    editTicket({ id, user_id }) {
      this.$router.push({
        name: 'main.edit-ticket',
        params: {
          id: user_id,
          id1: id,
        },
      })
    },
    createNote({ id, user_id }) {
      const data = {
        ticket: this.ticket,
        notes_quantity: this.notes_count,
        isEdit: false,
      }
      this.$router.push({
        name: 'note.create',
        params: {
          id: user_id,
          id1: id,
          data: data,
        },
      })
    },

    viewNote({ id, user_id }, note) {
      const data = {
        ticket: this.ticket,
        note: note,
        isEdit: false,
      }
      this.$router.push({
        name: 'note.view',
        params: {
          id: user_id,
          id1: id,
          data: data,
        },
      })
    },

    clearFilter() {
      /* if (this.filters.user) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.user
        )
      } */

      this.filters = {
        user: '',
      }
    },

    refreshTable() {
      this.$refs.notes_table.refresh()
    },

    setFilters() {
      this.refreshTable()
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async fetchNotes({ page, filter, sort }) {
      try {
        const params = {
          ticket_id: this.$route.params.id1,
          reference:
            this.filters.reference !== null ? this.filters.reference : '',
          subject: this.filters.subject !== null ? this.filters.subject : '',
          user: this.filters.user !== null ? this.filters.user.id : null,
          from_date:
            this.filters.from_date !== null ? this.filters.from_date : '',
          to_date: this.filters.to_date !== null ? this.filters.to_date : '',
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
    async getTicket() {
      try {
        this.isLoading = true
        const res = await this.fetchCustomerTicket(this.$route.params.id1)
        this.ticket = res.data.customerTicket
        //console.log('ticket_ ', this.ticket)
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
              this.$router.push({ name: 'tickets.main' })
            }
          }
        } catch (e) {
          window.toastr['error'](res.data.message)
        } finally {
          this.isLoading = false
        }
      })
    },

    async deleteNote(id) {
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
            let res = await this.deleteNoteTicket({ id: id })
            if (res.data.success) {
              window.toastr['success'](
                this.$tc('customer_ticket.deleted_message', 1)
              )
              this.$refs.notes_table.refresh()
              return true
            }
          }
        } catch (e) {
          window.toastr['error'](res.data.message)
        } finally {
          this.isLoading = false
        }
      })
    },

    editNote({ id, user_id }, note) {
      const data = {
        ticket: this.ticket,
        notes_quantity: this.notes_count,
        isEdit: true,
      }
      this.$router.push({
        name: 'note.create',
        params: {
          id: user_id,
          id1: id,
          note: note,
          data: data,
        },
      })
    },

    buttonBack() {
      this.$utils.cancelFormOrBack(this, this.$router, 'back')
    },
  },
}
</script>

<style lang="scss">
.pbx-service-details {
  .table-foot {
    .table-total {
      min-width: 390px;
    }
  }

  @media (max-width: 480px) {
    .table-foot {
      .table-total {
        min-width: 384px;
      }
    }
  }
}

// Dropdown
.tab {
  overflow: hidden;
}

.tab-content-slide {
  max-height: 0;
  transition: all 0.5s;
}

input:checked + .tab-label .test {
  background-color: #000;
}

input:checked + .tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}

input:checked + .tab-label::after {
  transform: rotate(90deg);
}

input:checked ~ .tab-content-slide {
  max-height: 400vh;
}
</style>
