<template>
  <base-page :class="{ 'xl:pl-96': showSideBar }">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <div class="mr-3 hidden xl:block">
          <sw-button
            class=""
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
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
        <sw-dropdown position="bottom-end">
          <sw-button slot="activator" class="mr-3" variant="primary">
            {{ $t('customers.new_transaction') }}
          </sw-button>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/estimates/create?customer=${$route.params.id}`"
          >
            <document-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('estimates.new_estimate') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/invoices/create?customer=${$route.params.id}`"
          >
            <document-text-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('invoices.new_invoice') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/payments/create?customer=${$route.params.id}`"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payments.new_payment') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/expenses/create?customer=${$route.params.id}`"
          >
            <calculator-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('expenses.new_expense') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/add-package`"
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('services.add_service') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/add-corepbx-services`"
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.add_corepbx_services') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/add-corepbx-services`"
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.add_corepbx_services') }}
          </sw-dropdown-item>
          
        </sw-dropdown>
        <sw-dropdown>
          <sw-button slot="activator" variant="primary">
            <dots-horizontal-icon class="h-5 -ml-1 -mr-1" />
          </sw-button>

          <sw-dropdown-item
            :to="`/admin/customers/${$route.params.id}/payment-accounts`"
            tag-name="router-link"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payment_accounts.title') }}
          </sw-dropdown-item>

          <sw-dropdown-item @click="removeCustomer($route.params.id)">
            <trash-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.deletecustomer') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/note`"
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customer_notes.title') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/options`"
          >
            <adjustments-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customers.options') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/customers/${$route.params.id}/ticket`"
          >
            <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('customer_ticket.title') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </template>
    </sw-page-header>

    <!-- sidebar -->
    <slide-x-left-transition>
      <customer-view-sidebar v-show="showSideBar" />
    </slide-x-left-transition>

    <!-- Chart -->
    <customer-chart :refresh="isRefresh" />
  </base-page>
</template>

<script>
import { AdjustmentsIcon } from '@vue-hero-icons/outline'
import {
  DotsHorizontalIcon,
  TrashIcon,
  DocumentIcon,
  DocumentTextIcon,
  CreditCardIcon,
  CalculatorIcon,
  ClipboardListIcon,
  XIcon,
} from '@vue-hero-icons/solid'
import { DocumentDuplicateIcon } from '@vue-hero-icons/outline'
import LineChart from '../../components/chartjs/LineChart'
import CustomerViewSidebar from './partials/CustomerViewSidebar'
import CustomerChart from './partials/CustomerChart'
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
  },
  data() {
    return {
      customer: null,
      showSideBar: false,
      isRefresh: false,
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

    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
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

    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
      this.isRefresh = true
      setTimeout(() => (this.isRefresh = false), 300)
    },
  },
}
</script>
