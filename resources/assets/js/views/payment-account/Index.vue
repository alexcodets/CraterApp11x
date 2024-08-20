<template>
  <base-page class="customer-create">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('payment_accounts.title')"> </sw-page-header>
    

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/view`"
          variant="primary-outline"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <sw-button
          v-show="totalPaymentAccounts"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-dropdown position="bottom-end" class="w-full md:w-auto md:ml-1 mb-2 md:mb-0" >
          <sw-button slot="activator" class="w-full md:w-auto md:ml-1 mb-2 md:mb-0" variant="primary">
            <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
            {{ $t('payment_accounts.add_account') }}
          </sw-button>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/payment-accounts/create-ACH`"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payment_accounts.ach_account') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/payment-accounts/create-CC`"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payment_accounts.cc_account') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </div>
    </div>
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('payment_accounts.name')"
          class="flex-1 mt-2 mr-2"
        >
          <sw-input
            v-model="filters.first_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <!-- <sw-input-group
          :label="$t('payment_accounts.last_name')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.last_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group> -->

        <sw-input-group
          :label="$t('payment_accounts.type')"
          class="flex-1 mt-2 mx-2"
        >
          <sw-select
            v-model="filters.payment_account_type"
            :options="payment_account_typeOptions"
            class="mt-2"
            label="label"
            track-by="value"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('payment_accounts.address')"
          class="flex-1 mt-2 ml-2"
        >
          <sw-input
            v-model="filters.address_1"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('payment_accounts.no_payment_account')"
      :description="$t('payment_accounts.list_of_payment_account')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/payment-accounts/create-CC`"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('payment_accounts.add_new_payment_account') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ paymentAccounts.length }}</b>
          {{ $t('general.of') }} <b>{{ totalPaymentAccounts }}</b>
        </p>
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.name')"
          show="first_name"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.first_name') }}</span>
            <router-link
              :to="{
                path: `payment-accounts/${row.id}/view-${row.payment_account_type}`,
              }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.first_name }}
            </router-link>
            <span v-else>
              {{ row.first_name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.address')"
          show="address_1"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.address') }}</span>
            <span> {{ row.address_1 }} </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.payment_account_type')"
          show="payment_account_type"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.payment_account_type') }}</span>
            <span>
              {{
                row.payment_account_type == 'CC'
                  ? $t('payment_accounts.types.credit_card')
                  : $t('payment_accounts.types.ach')
              }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.default_payment_account')"
          show="main_account"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.default') }}</span>
            <span v-if="row.main_account">
              {{ $t('payment_accounts.default') }}
            </span>
            <span v-else>
              {{ $t('payment_accounts.no_default') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('payment_accounts.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />
              <div v-if="permissionModule.update">
                <sw-dropdown-item
                  @click="changeDefaultPayAccount(row.id)"
                  v-if="!row.main_account"
                >
                  <check-circle-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.make_default_pay_account') }}
                </sw-dropdown-item>

                <sw-dropdown-item
                  @click="changeDefaultPayAccount(row.id)"
                  v-else
                >
                  <check-circle-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.unmake_defaut_pay_account') }}
                </sw-dropdown-item>
              </div>

              <sw-dropdown-item
                :to="`payment-accounts/${row.id}/edit-${row.payment_account_type}`"
                tag-name="router-link"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`/admin/customers/${$route.params.id}/payment-accounts/${row.id}/view-${row.payment_account_type}`"
                tag-name="router-link"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="removePaymentAccount(row.id)"
                v-if="permissionModule.delete"
              >
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
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  CheckCircleIcon,
  BadgeCheckIcon,
  DocumentIcon,
  DocumentTextIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../components/icon/AstronautIcon'

export default {
  components: {
    AstronautIcon,
    ChevronDownIcon,
    PlusSmIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    CheckCircleIcon,
    BadgeCheckIcon,
    DocumentIcon,
    DocumentTextIcon,
    CreditCardIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      filters: {
        first_name: '',
        last_name: '',
        address_1: '',
        payment_account_type: '',
      },
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
      },
      payment_account_typeOptions: [
        { label: this.$t('payment_accounts.types.credit_card'), value: 'CC' },
        { label: this.$t('payment_accounts.types.ach'), value: 'ACH' },
      ],
    }
  },
  created() {
    this.permissionsUserModule()
  },
  computed: {
    showEmptyScreen() {
      return !this.totalPaymentAccounts && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('paymentAccounts', [
      'paymentAccounts',
      'totalPaymentAccounts',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('paymentAccounts', [
      'fetchPaymentAccounts',
      'deletePaymentAccount',
      'defaultPayAccount',
    ]),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('user', ['getUserModules']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        user_id: this.$route.params.id,
        first_name: this.filters.first_name,
        last_name: this.filters.last_name,
        address_1: this.filters.address_1,
        payment_account_type: this.filters.payment_account_type
          ? this.filters.payment_account_type.value
          : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchPaymentAccounts(data)
      this.isRequestOngoing = false

      return {
        data: response.data.payment_accounts.data,
        pagination: {
          totalPages: response.data.payment_accounts.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        first_name: '',
        last_name: '',
        adress_1: '',
        payment_account_type: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removePaymentAccount(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payment_accounts.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePaymentAccount({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('payment_accounts.deleted_message', 1)
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async changeDefaultPayAccount(id) {
      this.id = id

      let res = await this.defaultPayAccount(id)

      if (res.data.success) {
        window.toastr['success'](
          this.$tc('payment_accounts.updated_message', 1)
        )
      }
      this.$refs.table.refresh()
    },

    async permissionsUserModule() {
      const data = {
        module: 'cust_payment_acc',
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
