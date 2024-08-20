<template>
  <base-page class="payments">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('customer_address.title')"> </sw-page-header>
      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/view`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('customer_address.clientgoback') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/add-address`"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <!-- @click="openCustomerAddressModal()" -->
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('customer_address.create_address') }}
        </sw-button>
      </div>
    </div>

    <sw-empty-table-placeholder
      v-if="showEmptyScreen"
      :title="$t('customer_address.no_customer_address')"
      :description="$t('customer_address.list_of_customer_address')"
    >
      <capsule-icon class="mt-5 mb-4" />
      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/add-address`"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('customer_address.new_address') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm"></p>
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <!-- <sw-table-column
          :sortable="true"
          :label="$t('customer_address.name')"
          show="name"
        >
          <template slot-scope="row">
            <router-link
              :to="`/admin/tickets/main/${row.user_id}/${row.id}/view-ticket`"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column> -->

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('customer_address.country')"
          show="country_id"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_address.country') }}</span>
            {{ row.country.name }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('customer_address.state')"
          show="state_id"
        >
          <template slot-scope="row">
            <span>{{ $t('customer_address.state') }}</span>
            {{ row.state.name }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customer_address.city')"
          show="city"
        />

        <sw-table-column :sortable="true" :label="$t('customer_address.type')">
          <template slot-scope="row">
            {{
              row.type == 'billing'
                ? $t('customer_address.billing')
                : $t('customer_address.services_address')
            }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <!-- <sw-dropdown-item
                tag-name="router-link"
                :to="`/admin/tickets/main/${row.user_id}/${row.id}/view-ticket`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item> -->

              <!-- @click="editAddress(row)" -->
              <sw-dropdown-item
                tag-name="router-link"
                :to="`${row.id}/edit-address`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="avalara_module_backend && row.type && isAvalara"
                @click="showModalException(row)"
              >
                <book-open-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('avalara.exemptions') }}
              </sw-dropdown-item>
              <div v-if="permissionModule.delete">
                <sw-dropdown-item
                  v-if="row.type == 'services_address'"
                  @click="removeAddress(row.id)"
                >
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.delete') }}
                </sw-dropdown-item>
              </div>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
    <CustomerAvalaraCategoryExemptionsModalList
      ref="CustomerAvalaraCategoryExemptionsRef"
    />
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import CapsuleIcon from '@/components/icon/CapsuleIcon'
import CustomerAvalaraCategoryExemptionsModalList from '@/components/base/modal/CustomerAvalaraCategoryExemptionsModalList.vue'
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
    CustomerAvalaraCategoryExemptionsModalList,
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
      user_id: null,
      isAvalara: 0,
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalCustomerAddress && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('customerAddress', [
      'totalCustomerAddress',
      /* 'CustomerNote',
      'selectAllField', */
    ]),
  },
  async mounted() {
    this.user_id = this.$route.params.id
    let resCustomer = await this.fetchCustomer(this.$route.params)
    this.isAvalara = resCustomer.data.customer.avalara_bool
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  async created() {
    this.permissionsUserModule()
    window.hub.$on('showModalException_event', this.showModalExceptionList)
    this.fetchAvalaraModule()
  },
  methods: {
    ...mapActions('customerAddress', [
      'fetchCustomerAddresses',
      'deleteCustomerAddress',
      /* 'setSelectAllState', */
    ]),
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('customer', [
      'fetchCustomer',
      'billingValidation',
      /* 'addCustomer',
      'updateCustomer', */
    ]),

    showModalExceptionList(data) {
      this.$refs.CustomerAvalaraCategoryExemptionsRef.setData(data)
      // this.openModal({
      //   title: this.$t('avalara.category_exemptions'),
      //   componentName: 'CustomerAvalaraCategoryExemptionsModalList',
      //   id: this.$route.params.id,
      //   data: this.modalData,
      //   variant: 'lg',
      //   company: 0,
      // })
    },

    async fetchAvalaraModule() {
      let res = await window.axios.get('/api/v1/module/avalara')
      this.avalara_module_backend = res.data
    },

    async fetchData({ page, filter, sort }) {
      this.isRequestOngoing = true
      let response = await this.fetchCustomerAddresses({
        customer_id: this.$route.params.id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
        limit: 10,
      })
      this.isRequestOngoing = false
      return {
        data: response.data.customerAddress.data,
        pagination: {
          totalPages: response.data.customerAddress.last_page,
          currentPage: page,
          count: response.data.customerAddress.total,
          orderBy: sort.order || 'desc',
        },
      }
    },
    showModalException(billing) {
      var avalaralocationid = billing.user.avalara_location_id
      if (billing.avalaraLocation != null) {
        avalaralocationid = billing.avalaraLocation.id
      }
      const data = {
        billing: billing,
        userId: billing.user_id,
        avalara_location_id: avalaralocationid,
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
    async removeAddress(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customer_address.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomerAddress({ id: id })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('customer_address.deleted_message', 1)
            )
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
        title: this.$t('customer_address.create_address'),
        componentName: 'CustomerAddressModal',
        variant: 'lg',
      })
    },

    editAddress(address = null) {
      if (address) {
        this.openModal({
          title: this.$t('customer_address.edit_address'),
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

    async permissionsUserModule() {
      const data = {
        module: 'cust_address',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions == null) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.access == 0) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1) {
          this.permissionModule.create = true
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true
        }
      }
    },
  },
}
</script>