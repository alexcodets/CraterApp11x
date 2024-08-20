<template>
  <base-page :class="{ 'xl:pl-96': showSideBar }">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="pageTitle"> </sw-page-header>
      <div class="flex flex-wrap items-center justify-end">
        <div class="mr-3 hidden xl:block">
          <sw-button
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
            variant="primary-outline"
            @click="toggleListCustomers"
          >
            {{ $t('customers.contacts_list') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>

        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/edit`"
          class="w-full md:w-auto md:ml-1 mb-2 md:mb-0"
          variant="primary-outline"
          v-if="permissionModule.update"
        >
          {{ $t('general.edit') }}
        </sw-button>

        <sw-dropdown position="bottom-end" class="w-full md:w-auto md:ml-1 mb-2 md:mb-0">
          <sw-button slot="activator" class="w-full md:w-auto md:ml-1 mb-2 md:mb-0"
          
          variant="primary">
            {{ $t('customers.new_transaction') }}
          </sw-button>
          <sw-dropdown-item
            @click="redirectDashboardPos"
            v-if="permissionModule.allowInvoiceFormPos"
          >
            <document-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('core_pos.new_invoice_pos') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/estimates/create?customer=${$route.params.id}`"
            v-if="
              permissionModule.accessEstimates &&
              permissionModule.createEstimates
            "
          >
            <document-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('estimates.new_estimate') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/invoices/create?customer=${$route.params.id}`"
            v-if="
              permissionModule.accessInvoices && permissionModule.createInvoices
            "
          >
            <document-text-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('invoices.new_invoice') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/payments/create?customer=${$route.params.id}`"
            v-if="
              permissionModule.accessPayments && permissionModule.createPayments
            "
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payments.new_payment') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/expenses/create?customer=${$route.params.id}`"
            v-if="
              permissionModule.accessExpenses && permissionModule.createExpenses
            "
          >
            <calculator-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('expenses.new_expense') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/add-package`"
            v-if="
              permissionModule.accessNormalServices &&
              permissionModule.createNormalServices
            "
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('services.add_service') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/add-corepbx-services`"
            v-if="
              permissionModule.accessPBXServices &&
              permissionModule.createPBXServices
            "
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.add_corepbx_services') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/credit`"
            v-if="
              permissionModule.accessPayments && permissionModule.createPayments
            "
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.add_credit') }}
          </sw-dropdown-item>
        </sw-dropdown>

        <!-- 3 points button -->
        <sw-dropdown class="w-full md:w-auto md:ml-1 mb-2 md:mb-0">
          <sw-button slot="activator" variant="primary"  class="w-full md:w-auto md:ml-1 mb-2 md:mb-0">
            <dots-horizontal-icon class="h-5 -ml-1 -mr-1" />
          </sw-button>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/address`"
            v-if="permissionModule.accessCustomerAddress"
          >
            <map-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customer_address.title') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/contacts`"
            v-if="permissionModule.accessCustomerContacts"
          >
            <user-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('contacts.title') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            @click="removeCustomer($route.params.id)"
            v-if="permissionModule.delete"
          >
            <trash-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.deletecustomer') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            :to="`/admin/customers/${$route.params.id}/payment-accounts`"
            tag-name="router-link"
            v-if="permissionModule.accessCustomerPaymentAccounts"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payment_accounts.title') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            :to="`/admin/payments-failed?customer_id=${$route.params.id}`"
            tag-name="router-link"
            v-if="permissionModule.accessCustomerPaymentAccounts"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('failed_payment_history.title') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/note`"
            v-if="permissionModule.accessCustomerMemoNotes"
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customer_notes.title') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/options`"
            v-if="permissionModule.update"
          >
            <adjustments-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.options') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/ticket`"
            v-if="
              permissionModule.accessTickets && permissionModule.createEstimates
            "
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customer_ticket.title') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </div>
    </div>

    <!-- sidebar -->
    <slide-x-left-transition>
      <customer-view-sidebar v-show="showSideBar" />
    </slide-x-left-transition>

    <!-- Chart -->
    <customer-chart :refresh="isRefresh" />
  </base-page>
</template>

<script>
import {
  AdjustmentsIcon,
  DocumentDuplicateIcon,
  MapIcon,
} from '@vue-hero-icons/outline'
import {
  DotsHorizontalIcon,
  TrashIcon,
  DocumentIcon,
  DocumentTextIcon,
  CreditCardIcon,
  CalculatorIcon,
  ClipboardListIcon,
  XIcon,
  UserIcon,
} from '@vue-hero-icons/solid'
import LineChart from '../../components/chartjs/LineChart.vue'
import CustomerViewSidebar from './partials/CustomerViewSidebar.vue'
import CustomerChart from './partials/CustomerChart.vue'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    LineChart,
    DotsHorizontalIcon,
    CustomerViewSidebar,
    DocumentIcon,
    DocumentTextIcon,
    CreditCardIcon,
    CalculatorIcon,
    CustomerChart,
    TrashIcon,
    ClipboardListIcon,
    XIcon,
    DocumentDuplicateIcon,
    AdjustmentsIcon,
    MapIcon,
    UserIcon,
  },
  data() {
    return {
      customer: null,
      showSideBar: false,
      isRefresh: false,
      permissionModule: {
        access: false,
        create: false,
        read: false,
        delete: false,
        update: false,
        accessTickets: false,
        createTickets: false,
        readTickets: false,
        deleteTickets: false,
        updateTickets: false,
        accessEstimates: false,
        createEstimates: false,
        readEstimates: false,
        deleteEstimates: false,
        updateEstimates: false,
        accessInvoices: false,
        createInvoices: false,
        readInvoices: false,
        deleteInvoices: false,
        updateInvoices: false,
        accessPayments: false,
        createPayments: false,
        readPayments: false,
        deletePayments: false,
        updatePayments: false,
        accessExpenses: false,
        createExpenses: false,
        readExpenses: false,
        deleteExpenses: false,
        updateExpenses: false,
        accessNormalServices: false,
        createNormalServices: false,
        readNormalServices: false,
        deleteNormalServices: false,
        updateNormalServices: false,
        accessPBXServices: false,
        createPBXServices: false,
        readPBXServices: false,
        deletePBXServices: false,
        updatePBXServices: false,
        accessCustomerAddress: false,
        accessCustomerContacts: false,
        accessCustomerPaymentAccounts: false,
        accessCustomerMemoNotes: false,
        allowInvoiceFormPos: false,
      },
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),
    pageTitle() {
      return this.selectedViewCustomer.customer
        ? this.selectedViewCustomer.customer.name
        : ''
    },
    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
  },
  created() {
    this.permissionsUserModule()
    this.fetchViewCustomer({ id: this.$route.params.id })
  },
  methods: {
    ...mapActions('customer', [
      'fetchViewCustomer',
      'selectCustomer',
      'deleteCustomer',
      'deleteMultipleCustomers',
      'deleteCustomer',
    ]),

    ...mapActions('user', ['getUserModules']),
    ...mapActions('modules', ['getModules']),
    ...mapActions('company', ['fetchCompanySettings']),

    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
      }).then(async (result) => {
        if (result) {
          let res = await this.deleteCustomer({ ids: [id] })

          if (res.data.type === 'success') {
            window.toastr['success'](this.$tc('customers.deleted_message', 1))
            //this.$refs.table.refresh()
            this.$router.push('/admin/customers')
            return true
          }

          window.toastr[res.data.type](res.data.message)
          return true
        }
      })
    },

    redirectDashboardPos() {
      this.$router.push({
        path: '/admin/corePOS/dashboard',
        query: { customer: this.$route.params.id },
      })
    },

    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
      this.isRefresh = true
      setTimeout(() => (this.isRefresh = false), 300)
    },

    async permissionsUserModule() {
      const modules = ['corePOS', 'PBXware']
      const modulesArray = await this.getModules(modules)
      const moduleCorePos = modulesArray.modules.find(
        (element) => element.name === 'corePOS'
      )
      const modulePbxware = modulesArray.modules.find(
        (element) => element.name === 'PBXware'
      )

      const data = {
        module: 'customers',
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

      // permissinos  tickets
      const dataTickets = {
        module: 'tickets',
      }
      const permissionsTickets = await this.getUserModules(dataTickets)
      if (permissionsTickets.super_admin == true) {
        this.permissionModule.readTickets = true
        this.permissionModule.accessTickets = true
        this.permissionModule.updateTickets = true
        this.permissionModule.deleteTickets = true
        this.permissionModule.createTickets = true
      } else if (permissionsTickets.exist == true) {
        const modulePermissions = permissionsTickets.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readTickets = false
          this.permissionModule.accessTickets = false
          this.permissionModule.updateTickets = false
          this.permissionModule.deleteTickets = false
          this.permissionModule.createTickets = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessTickets = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updateTickets = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deleteTickets = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createTickets = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readTickets = true
          }
        }
      }
      // permission estimates
      const dataEstimates = {
        module: 'estimates',
      }
      const permissionsEstimates = await this.getUserModules(dataEstimates)
      if (permissionsEstimates.super_admin == true) {
        this.permissionModule.readEstimates = true
        this.permissionModule.accessEstimates = true
        this.permissionModule.updateEstimates = true
        this.permissionModule.deleteEstimates = true
        this.permissionModule.createEstimates = true
      } else if (permissionsEstimates.exist == true) {
        const modulePermissions = permissionsEstimates.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readEstimates = false
          this.permissionModule.accessEstimates = false
          this.permissionModule.updateEstimates = false
          this.permissionModule.deleteEstimates = false
          this.permissionModule.createEstimates = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessEstimates = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updateEstimates = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deleteEstimates = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createEstimates = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readEstimates = true
          }
        }
      }

      // permission invoices
      const dataInvoices = {
        module: 'invoices',
      }
      const permissionsInvoices = await this.getUserModules(dataInvoices)
      if (permissionsInvoices.super_admin == true) {
        this.permissionModule.readInvoices = true
        this.permissionModule.accessInvoices = true
        this.permissionModule.updateInvoices = true
        this.permissionModule.deleteInvoices = true
        this.permissionModule.createInvoices = true
      } else if (permissionsInvoices.exist == true) {
        const modulePermissions = permissionsInvoices.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readInvoices = false
          this.permissionModule.accessInvoices = false
          this.permissionModule.updateInvoices = false
          this.permissionModule.deleteInvoices = false
          this.permissionModule.createInvoices = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessInvoices = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updateInvoices = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deleteInvoices = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createInvoices = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readInvoices = true
          }
        }
      }

      // permission invoices
      const dataPayments = {
        module: 'payments',
      }
      const permissionsPayments = await this.getUserModules(dataPayments)
      if (permissionsPayments.super_admin == true) {
        this.permissionModule.readPayments = true
        this.permissionModule.accessPayments = true
        this.permissionModule.updatePayments = true
        this.permissionModule.deletePayments = true
        this.permissionModule.createPayments = true
      } else if (permissionsPayments.exist == true) {
        const modulePermissions = permissionsPayments.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readPayments = false
          this.permissionModule.accessPayments = false
          this.permissionModule.updatePayments = false
          this.permissionModule.deletePayments = false
          this.permissionModule.createPayments = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessPayments = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updatePayments = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deletePayments = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createPayments = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readPayments = true
          }
        }
      }

      // permission Expenses
      const dataExpenses = {
        module: 'expenses',
      }
      const permissionsExpenses = await this.getUserModules(dataExpenses)
      if (permissionsExpenses.super_admin == true) {
        this.permissionModule.readExpenses = true
        this.permissionModule.accessExpenses = true
        this.permissionModule.updateExpenses = true
        this.permissionModule.deleteExpenses = true
        this.permissionModule.createExpenses = true
      } else if (permissionsExpenses.exist == true) {
        const modulePermissions = permissionsExpenses.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readExpenses = false
          this.permissionModule.accessExpenses = false
          this.permissionModule.updateExpenses = false
          this.permissionModule.deleteExpenses = false
          this.permissionModule.createExpenses = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessExpenses = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updateExpenses = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deleteExpenses = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createExpenses = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readExpenses = true
          }
        }
      }

      // permission Services
      const dataNormalServices = {
        module: 'services_normal',
      }
      const permissionsNormalServices = await this.getUserModules(
        dataNormalServices
      )
      if (permissionsNormalServices.super_admin == true) {
        this.permissionModule.readNormalServices = true
        this.permissionModule.accessNormalServices = true
        this.permissionModule.updateNormalServices = true
        this.permissionModule.deleteNormalServices = true
        this.permissionModule.createNormalServices = true
      } else if (permissionsNormalServices.exist == true) {
        const modulePermissions = permissionsNormalServices.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readNormalServices = false
          this.permissionModule.accessNormalServices = false
          this.permissionModule.updateNormalServices = false
          this.permissionModule.deleteNormalServices = false
          this.permissionModule.createNormalServices = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessNormalServices = true
          }
          if (modulePermissions.update == 1) {
            this.permissionModule.updateNormalServices = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deleteNormalServices = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createNormalServices = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readNormalServices = true
          }
        }
      }
      // permission PBX Services
      const dataPBXServices = {
        module: 'pbx_services',
      }
      const permissionsPBXServices = await this.getUserModules(dataPBXServices)
      if (permissionsPBXServices.super_admin == true) {
        this.permissionModule.readPBXServices = true
        if (modulePbxware && modulePbxware.status == 'A') {
          this.permissionModule.accessPBXServices = true
        }
        this.permissionModule.updatePBXServices = true
        this.permissionModule.deletePBXServices = true
        this.permissionModule.createPBXServices = true
      } else if (permissionsPBXServices.exist == true) {
        const modulePermissions = permissionsPBXServices.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.readPBXServices = false
          this.permissionModule.accessPBXServices = false
          this.permissionModule.updatePBXServices = false
          this.permissionModule.deletePBXServices = false
          this.permissionModule.createPBXServices = false
        } else {
          if (modulePbxware && modulePbxware.status == 'A') {
            if (modulePermissions.access == 1) {
              this.permissionModule.accessPBXServices = true
            }
          }

          if (modulePermissions.update == 1) {
            this.permissionModule.updatePBXServices = true
          }
          if (modulePermissions.delete == 1) {
            this.permissionModule.deletePBXServices = true
          }
          if (modulePermissions.create == 1) {
            this.permissionModule.createPBXServices = true
          }
          if (modulePermissions.read == 1) {
            this.permissionModule.readPBXServices = true
          }
        }
      }

      // permission customer address
      const dataCustomerAddress = {
        module: 'cust_address',
      }
      const permissionsCustomerAddress = await this.getUserModules(
        dataCustomerAddress
      )
      if (permissionsCustomerAddress.super_admin == true) {
        this.permissionModule.accessCustomerAddress = true
      } else if (permissionsCustomerAddress.exist == true) {
        const modulePermissions = permissionsCustomerAddress.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.accessCustomerAddress = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessCustomerAddress = true
          }
        }
      }

      // permission customer address
      const dataCustomerContacts = {
        module: 'cust_contacts',
      }
      const permissionsCustomerContacts = await this.getUserModules(
        dataCustomerContacts
      )
      if (permissionsCustomerContacts.super_admin == true) {
        this.permissionModule.accessCustomerContacts = true
      } else if (permissionsCustomerContacts.exist == true) {
        const modulePermissions = permissionsCustomerContacts.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.accessCustomerContacts = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessCustomerContacts = true
          }
        }
      }

      // permission customer address
      const dataCustomerPaymentAccounts = {
        module: 'cust_payment_acc',
      }
      const permissionsCustomerPaymentAccounts = await this.getUserModules(
        dataCustomerPaymentAccounts
      )
      if (permissionsCustomerPaymentAccounts.super_admin == true) {
        this.permissionModule.accessCustomerPaymentAccounts = true
      } else if (permissionsCustomerPaymentAccounts.exist == true) {
        const modulePermissions =
          permissionsCustomerPaymentAccounts.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.accessCustomerPaymentAccounts = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessCustomerPaymentAccounts = true
          }
        }
      }

      // permission customer address
      const dataCustomerMemoNotes = {
        module: 'cust_mnotes',
      }
      const permissionsCustomerMemoNotes = await this.getUserModules(
        dataCustomerMemoNotes
      )
      if (permissionsCustomerMemoNotes.super_admin == true) {
        this.permissionModule.accessCustomerMemoNotes = true
      } else if (permissionsCustomerMemoNotes.exist == true) {
        const modulePermissions = permissionsCustomerMemoNotes.permissions[0]
        if (modulePermissions == null || modulePermissions.access == 0) {
          this.permissionModule.accessCustomerMemoNotes = false
        } else {
          if (modulePermissions.access == 1) {
            this.permissionModule.accessCustomerMemoNotes = true
          }
        }
      }

      if (moduleCorePos && moduleCorePos.status == 'A') {
        let res = await this.fetchCompanySettings(['allow_invoice_form_pos'])
        this.permissionModule.allowInvoiceFormPos =
          res.data.allow_invoice_form_pos == '0' ? false : true
      } else {
        this.permissionModule.allowInvoiceFormPos = false
      }
    },
  },
}
</script>