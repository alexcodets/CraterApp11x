<template>
  <base-page class="payments">

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> 
    <sw-page-header :title="$t('contacts.title')">
    </sw-page-header>
     
    <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/view`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('contacts.clientgoback') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/add-contact`"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <!-- @click="openCustomerAddressModal()" -->
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('contacts.create_contact') }}
        </sw-button>
   
      </div>

    
  </div>

    <sw-empty-table-placeholder
      v-if="showEmptyScreen"
      :title="$t('contacts.no_contacts')"
      :description="$t('contacts.list_of_contacts')"
    >
      <capsule-icon class="mt-5 mb-4" />
      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/add-contact`"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('contacts.new_contact') }}
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
          :sortable="true"
          :filterable="true"
          :label="$t('contacts.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('contacts.name') }}</span>
            <router-link :to="{ path: `edit-contact/${row.id}` }" class="font-medium text-primary-500">
              {{ row.name }}
            </router-link>            
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('contacts.last_name')"
          show="last_name"
        />

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('contacts.phone')"
          show="phone"
        />

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('contacts.position')"
          show="position"
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

              <!-- @click="editAddress(row)" -->
              <sw-dropdown-item
                tag-name="router-link"
                :to="`edit-contact/${row.id}`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeContact(row.id)" v-if="permissionModule.delete">
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
// import CustomerAvalaraCategoryExemptionsModalList from '@/components/base/modal/CustomerAvalaraCategoryExemptionsModalList.vue'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  BookOpenIcon,
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
    BookOpenIcon,
    // CustomerAvalaraCategoryExemptionsModalList,
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
      avalara_module_backend: true,
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false
      }
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalCustomerContacts && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('customerContacts', [
      'totalCustomerContacts',
      /* 'CustomerNote',
      'selectAllField', */
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  created() {
    this.permissionsUserModule()
    window.hub.$on('showModalException_event', this.showModalExceptionList)
    this.fetchAvalaraModule()
  },
  methods: {
    ...mapActions('customerContacts', [
      'fetchCustomerContacts',
      'deleteCustomerContact',
      /* 'setSelectAllState', */
    ]),
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    async fetchAvalaraModule() {
      let res = await window.axios.get('/api/v1/module/avalara')
      this.avalara_module_backend = res.data
    },

    async fetchData({ page, filter, sort }) {
      this.isRequestOngoing = true
      let response = await this.fetchCustomerContacts({
        customer_id: this.$route.params.id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
        limit: 10,
      })
      this.isRequestOngoing = false
      return {
        data: response.data.contacts.data,
        pagination: {
          totalPages: response.data.contacts.last_page,
          currentPage: page,
          count: response.data.contacts.total,
          orderBy: sort.order || 'desc',
        },
      }
    },
    showModalException(billing) {
      const data = {
        billing: billing,
        userId: billing.user_id,
        avalara_location_id: billing.user.avalara_location_id,
      }
      this.openModal({
        title: this.$t('avalara.category_exemptions'),
        componentName: 'CustomerAvalaraCategoryExemptionsModal',
        id: this.$route.params.id,
        data: data,
        variant: 'lg',
        company: 0,
      })
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
    async removeContact(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('contacts.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomerContact({ id: id })
          if (res.data.success) {
            window.toastr['success'](this.$tc('contacts.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    openCustomerAddressModal() {
      this.openModal({
        title: this.$t('contacts.create_address'),
        componentName: 'CustomerAddressModal',
        variant: 'lg',
      })
    },

    editAddress(address = null) {
      if (address) {
        this.openModal({
          title: this.$t('contacts.edit_address'),
          id: address.id,
          data: address,
          componentName: 'CustomerAddressModal',
          variant: 'lg',
        })
      }
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
         module: "cust_contacts" 
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