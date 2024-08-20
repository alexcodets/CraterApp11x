<template>
  <base-page class="payments">

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <sw-page-header :title="$t('customer_notes.title')">
    </sw-page-header>
    <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/view`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('customer_notes.clientgoback') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/add-note`"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('customer_notes.create_note') }}
        </sw-button>
      </div>
  

  </div>

    <sw-empty-table-placeholder
      v-if="showEmptyScreen"
      :title="$t('customer_notes.no_customer_notes')"
      :description="$t('customer_notes.list_of_customer_notes')"
    >
      <capsule-icon class="mt-5 mb-4" />
      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/add-note`"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('customer_notes.new_note') }}
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
          :filterable="false"
          :label="$t('customer_notes.summary')"
          show="summary"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_notes.summary') }}</span>
            {{ row.summary }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          :label="$t('customer_notes.creator')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_notes.creator') }}</span>
            {{ row.name }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          :label="$t('customer_notes.pinned')"
          show="stiky"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_notes.pinned') }}</span>
          

            <div v-if="row.stiky === 1">Yes</div>

            <div v-if="row.stiky === 0">No</div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
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
                :to="`${row.id}/edit-note`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`${row.id}/view-note`"
                tag-name="router-link"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removePayment(row.id)" v-if="permissionModule.delete">
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
    permissionModule: {
      create: false,
      update: false,
      read: false,
      delete: false
    }
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalCustomerNote && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('customerNote', [
      'totalCustomerNote',
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

  mounted() {},

  created(){
    this.permissionsUserModule()
  },

  destroyed() {},

  methods: {
    ...mapActions('customerNote', [
      'fetchCustomerNotes',
      'deleteCustomerNote',
      'setSelectAllState',
    ]),

    ...mapActions('user',['getUserModules']),
    async fetchData({ page, filter, sort }) {

      let data = {
        customer_id: this.$route.params.id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchCustomerNotes(data)
      this.isRequestOngoing = false

      return {
        data: response.data.customerNote.data,
        pagination: {
          totalPages: response.data.customerNote.last_page,
          currentPage: page,
          count: response.data.customerNote.count,
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
        text: this.$tc('customer_notes.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomerNote({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('customer_notes.deleted_message', 1)
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

    async permissionsUserModule(){
      const data = {
         module: "cust_mnotes" 
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
          } else 
          if(modulePermissions.access == 0){
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

    }
  },
}
</script>


