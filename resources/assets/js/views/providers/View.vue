<template>
  <base-page>
    <sw-page-header class="mb-3" :title="$tc('providers.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="/admin/dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item
          to="/admin/providers"
          :title="$tc('providers.provider', 2)"
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/providers`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>
        <sw-button
          tag-name="router-link"
          :to="`/admin/providers/${$route.params.id}/edit`"
          class="mr-3"
          variant="primary-outline"
          v-if="permissionModule.update"
        >
          {{ $t('general.edit') }}
        </sw-button>

        <sw-dropdown position="bottom-end">
          <sw-button slot="activator" class="mr-3" variant="primary">
            {{ $t('customers.new_transaction') }}
          </sw-button>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/expenses/create?provider=${$route.params.id}`"
            v-if="permissionModule.createExpenses"
          >
            <calculator-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('expenses.new_expense') }}
          </sw-dropdown-item>
        </sw-dropdown>
        <sw-button
          slot="activator"
          variant="primary"
          @click="removeProvider($route.params.id)"
          v-if="permissionModule.delete"
        >
          {{ $t('general.delete') }}
        </sw-button>
      </template>
    </sw-page-header>
    <sw-card class="flex flex-col mt-3">
      <div class="pt-6 mt-5">
        <div class="col-span-12">
          <p class="text-gray-500 uppercase sw-section-title">
            {{ $t('providers.basic_info') }}
          </p>
          <div
            class="
              grid grid-cols-1
              gap-4
              mt-5
              lg:grid-cols-3
              md:grid-cols-2
              sm:grid-cols-1
            "
          >
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.titl') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.title }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.first_name }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.last_name') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.last_name }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.email') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic">
                {{ formData.email }}
              </p>
            </div>

            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.country') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.country"
              >
                {{ formData.country }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.not_selected') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.state') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.state"
              >
                {{ formData.state }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.not_selected') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.city') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.city"
              >
                {{ formData.city }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.street') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.street"
              >
                {{ formData.street }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.zip_code') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.zip_code"
              >
                {{ formData.zip_code }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.phone') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.phone"
              >
                {{ formData.phone }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.mobile') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.mobile"
              >
                {{ formData.mobile }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>

            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.webside') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.webside"
              >
                {{ formData.webside }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.terms') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.terms"
              >
                {{ formData.terms }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>

            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.account_no') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.account_no"
              >
                {{ formData.account_no }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.business_no') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.business_no"
              >
                {{ formData.business_no }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.track_payments') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.track_payments"
              >
                {{ $t('providers.yes') }}
              </p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.no') }}
              </p>
            </div>

            <div>
              <p
                class="
                  mb-1
                  text-sm
                  font-bold font-normal
                  leading-5
                  non-italic
                  text-primary-800
                "
              >
                {{ $t('providers.description') }}
              </p>
              <p
                class="text-sm leading-5 text-black non-italic"
                v-if="formData.description"
                v-html="formData.description"
              ></p>
              <p class="text-sm leading-5 text-black non-italic" v-else>
                {{ $t('providers.empty') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </sw-card>

    <sw-card class="flex flex-col mt-3" v-if="permissionModule.accessExpenses">
      <div class="tabs mb-5 grid col-span-12 pt-6">
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck5"
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
              for="chck5"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('expenses.expense', 2) }}
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
            <div class="tab-content-customer">
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="expenses_table"
                :show-filter="false"
                :data="fetchExpensesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.date')"
                  sort-as="expense_date"
                  show="formattedExpenseDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.expense_number')"
                  show="expense_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.expense_number') }}</span>
                    <router-link
                      :to="{ path: `/admin/expenses/${row.id}/edit` }"
                      class="font-medium text-primary-500"
                      v-if="permissionModule.updateExpenses"
                    >
                      {{ row.expense_number }}
                    </router-link>
                    <span v-else>
                      {{ row.expense_number }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column 
                  :label="$tc('expenses.subject')"
                  :sortable="true"
                  sort-as="subject"
                  show="subject"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('expenses.categories.category', 1)"
                  sort-as="name"
                  show="category.name"
                >
                  <template slot-scope="row">
                    <span>{{ $tc('expenses.categories.category', 1) }}</span>
                    <span> {{ row.category.name }} </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.customer')"
                  sort-as="user_name"
                  show="user_name"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.customer') }}</span>
                    <span>
                      {{ row.user_name ? row.user_name : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.amount')"
                  sort-as="amount"
                  show="category.amount"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.amount') }}</span>
                    <div v-html="$utils.formatMoney(row.amount, currency)" />
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('general.status')"
                  show="status"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.status') }}</span>
                    <span>
                      {{ row.status == 'Active'
                         ? $t('expenses.status.active')
                         : $t('expenses.status.pending')
                       }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.action') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />
                      <sw-dropdown-item
                        tag-name="router-link"
                        :to="{ path: `/admin/expenses/${row.id}/edit` }"                       
                      >
                        <pencil-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.edit') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                          tag-name="router-link"
                          :to="{ path: `/admin/expenses/${row.id}/view` }"  
                      >
                        <eye-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('invoices.view') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                          tag-name="router-link"
                          :to="`/admin/expenses/${row.id}/docs`"
                      >
                        <document-text-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.docs') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item @click="removeExpense(row.id)">
                        <trash-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t("general.delete") }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import ObservatoryIcon from '../../components/icon/ObservatoryIcon'
import { CalculatorIcon,  PencilIcon,
  TrashIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PlusIcon, 
  EyeIcon
} from '@vue-hero-icons/solid'
import {
  DocumentTextIcon,
} from '@vue-hero-icons/outline';
export default {
  components: {
    CalculatorIcon,
    ObservatoryIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    DocumentTextIcon,
    EyeIcon
  },
  data() {
    return {
      formData: {
        title: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        suffix: '',
        company: '',
        display_name: '',
        display_name_check: '',
        country: '',
        state: '',
        city: '',
        street: '',
        zip_code: '',
        description: '',
        phone: '',
        mobile: '',
        fax: '',
        other: '',
        webside: '',
        terms: '',
        opening_balance: '',
        as_of: '',
        account_no: '',
        business_no: '',
        track_payments: '',
        default_expense_account: '',
      },
      permissionModule: {
        update: false,
        delete: false,
        read: false,
        create: false,
        createExpenses: false,
        accessExpenses: false,
        updateExpenses: false,
        readExpenses: false,
        deleteExpenses: false
      }
    }
  },
  computed: {
    ...mapGetters('providers', ['selectedViewProvider']),
    ...mapGetters('company', ['defaultCurrency']),
    currency() {
      return this.defaultCurrency
    },
  },

  created() {
    this.permissionsUserModule()
    this.loadData()
  },

  watch: {
    $route(to, from) {
      this.Provider = this.selectedViewProvider
    },
  },
  methods: {
    ...mapActions('providers', ['fetchProvider', 'deleteProvider']),
    ...mapActions('expense', ['fetchExpenses', 'deleteExpense']),
    ...mapActions('user', ['getUserModules']),

    async loadData() {
      let response = await this.fetchProvider(this.$route.params.id)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.providers }
      }

    },

    async removeProvider(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('providers.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteProvider({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('providers.deleted_message', 1))
            this.$router.push('/admin/providers')
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async fetchExpensesData({ page, filter, sort }) {
      let data = {
        providers_id: this.$route.params.id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchExpenses(data)
      return {
        data: response.data.expenses.data,
        pagination: {
          totalPages: response.data.expenses.last_page,
          currentPage: page,
          count: response.data.expenses.count,
        },
      }
    },

    async removeExpense(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteExpense({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 1))
            /* this.refreshTableExpense() */
            this.$refs.expenses_table.refresh()
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async permissionsUserModule(){
      const data = {
          module: "providers"
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
          }else if(modulePermissions.access == 0){
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

      const dataExpenses = {
          module: "expenses"
      }
      const permissionsExpenses = await this.getUserModules(dataExpenses)
      if(permissionsExpenses.super_admin == true){
        this.permissionModule.accessExpenses = true
        this.permissionModule.createExpenses = true
        this.permissionModule.updateExpenses = true
      }else if(permissionsExpenses.exist == true ){
        const modulePermissions = permissionsExpenses.permissions[0]
        if(modulePermissions == null || modulePermissions.access == 0){
          this.permissionModule.accessExpenses = false
          this.permissionModule.createExpenses = false
          this.permissionModule.updateExpenses = false
          this.permissionModule.deleteExpenses = false
          this.permissionModule.readExpenses = false
        }else {
          if(modulePermissions.access == 1){
            this.permissionModule.accessExpenses = true
          }
          if(modulePermissions.update == 1){
            this.permissionModule.updateExpenses = true
          }
          if(modulePermissions.create == 1){
            this.permissionModule.createExpenses = true
          }
          if(modulePermissions.delete == 1){
            this.permissionModule.deleteExpenses = true
          }
          if(modulePermissions.read == 1){
            this.permissionModule.readExpenses = true
          }
        }
      }

    }
  },
}
</script>