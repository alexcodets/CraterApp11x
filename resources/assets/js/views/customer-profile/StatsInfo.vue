<template>
  <div>
    <div
      v-if="isLoadedData && Object.keys(this.loggedInCustomer).length"
      class="mb-6 p-4 bg-blue-100 rounded relative"
      role="alert"
    >
      <p>
        <strong>{{
          $t('customer_profile.welcome', { user: getUserName })
        }}</strong>
      </p>
      <p
        v-html="
          $t('customer_profile.welcome_detail', {
            balance: getBalanceDue,
            credit: getCredit,
          })
        "
      />
     <!--   <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <x-icon
          class="fill-current h-5 w-5 text-gray-500"
          role="button"
          viewBox="0 0 20 20"
          @click="isLoadedData = false"
        >
          <title>Close</title>
        </x-icon>
      </span>-->
      <sw-button

       v-if=" getMakePayment == 'YES'"
        tag-name="router-link"
        :to="`/customer/payments/${maxDebitInvoiceId}/create`"
        size="md"
        variant="info"
        class="mt-4"
      >
        <currency-dollar-icon class="h-5 mr-1 -ml-2 font-bold" />
        Make Payment
      </sw-button>

      <sw-button
        v-if="getAddcredit == 'YES'"
        tag-name="router-link"
        :to="`/customer/payments/addcredit`"
        size="md"
        variant="info"
        class="mt-4"
      >
        <currency-dollar-icon class="h-5 mr-1 -ml-2 font-bold" />
        {{ $t('customers.add_credit') }}
      </sw-button>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
      <div
      v-if="showServices"
        slot="item-title"
        class="relative flex justify-between p-2 bg-white rounded shadow lg:col-span-3 xl:p-4"
      >
        <div  >
          <span
            v-if="true"
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ getCountServices }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            Services
          </span>
        </div>
        <div class="flex items-center text-yellow-200">
          <star-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </div>

      <router-link
        slot="item-title"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
        v-if="showInvoices"
        :to="'/customer/invoices'"
      >
        <div>
          <span
            v-if="true"
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ getCountInvoices }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            {{ $t('dashboard.cards.invoices') }}
          </span>
        </div>
        <div class="flex items-center">
          <invoice-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>

      <router-link
        slot="item-title"
         v-if="showQuotes"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
        :to="'/customer/estimates'"
      >
        <div>
          <span
            v-if="true"
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ getCountEstimates }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            {{ $t('dashboard.cards.estimates') }}
          </span>
        </div>
        <div class="flex items-center">
          <estimate-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>

      <router-link
        slot="item-title"
         v-if="showTickets"
        class="relative flex justify-between p-2 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
        :to="'/customer/tickets'"
      >
        <div>
          <span
            v-if="true"
            class="text-sm font-semibold leading-tight text-black xl:text-2xl"
          >
            {{ getCountTickets }}
          </span>
          <span
            class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg"
          >
            Tickets
          </span>
        </div>
        <div class="flex items-center text-green-200">
          <ticket-icon class="w-9 h-9 xl:w-12 xl:h-12" />
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Icon from '../../components/icon/icon'
import InvoiceIcon from '../../components/icon/InvoiceIcon'
import EstimateIcon from '../../components/icon/EstimateIcon'
import {
  StarIcon,
  TicketIcon,
  XIcon,
  CurrencyDollarIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    StarIcon,
    TicketIcon,
    XIcon,
    CurrencyDollarIcon,
    Icon,
    InvoiceIcon,
    EstimateIcon,
  },
  data() {
    return {
      loadedData: true,
      showPayments: false,
      showQuotes: false,
      showInvoices: false,
      showTickets: false,
      showServices: false,
    }
  },
  watch: {
    settingsCompany: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.updateMenuVisibility();
        }
      },
    },
  },
  computed: {
    ...mapGetters('customerProfile', ['loggedInCustomer']),
    ...mapGetters('user', ['currentUser', 'settingsCompany']),


    isLoadedData: {
      get: function () {
        return this.loadedData
      },
      set: function (value) {
        this.loadedData = value
      },
    },

    getUserName() {
      let userName = ''

      if (!this.loggedInCustomer.customer) {
        return userName
      }

      userName = this.loggedInCustomer.customer.first_name
      return userName
    },

    getCredit() {
      let credit = 0.0

      if (!this.loggedInCustomer.chartData) {
        return credit
      }

      credit = this.$utils.formatMoney(
        this.loggedInCustomer.chartData.balanceTotal * 100,
        this.defaultCurrency
      )

      return credit
    },

    getBalanceDue() {
      let balanceDue = 0.0

      if (!this.loggedInCustomer.statsData) {
        return balanceDue
      }

      balanceDue =
        this.loggedInCustomer.statsData.totalAmountDue / 100 +
        this.loggedInCustomer.statsData.callRegisterTotalAmount

      balanceDue = this.$utils.formatMoney(
        balanceDue * 100,
        this.defaultCurrency
      )

      return balanceDue
    },

    getCountServices() {
      let countServices = 0

      if (!this.loggedInCustomer.statsData) {
        return countServices
      }
      if (!this.loggedInCustomer.statsData.countServices) {
        return countServices
      }

      countServices = this.loggedInCustomer.statsData.countServices
      return countServices
    },

    getCountInvoices() {
      let countInvoices = 0

      if (!this.loggedInCustomer.statsData) {
        return countInvoices
      }
      if (!this.loggedInCustomer.statsData.countInvoices) {
        return countInvoices
      }

      countInvoices = this.loggedInCustomer.statsData.countInvoices
      return countInvoices
    },

    getCountEstimates() {
      let countEstimates = 0

      if (!this.loggedInCustomer.statsData) {
        return countEstimates
      }
      if (!this.loggedInCustomer.statsData.countEstimates) {
        return countEstimates
      }

      countEstimates = this.loggedInCustomer.statsData.countEstimates
      return countEstimates
    },

    getCountTickets() {
      let countTickets = 0

      if (!this.loggedInCustomer.statsData) {
        return countTickets
      }
      if (!this.loggedInCustomer.statsData.countTickets) {
        return countTickets
      }

      countTickets = this.loggedInCustomer.statsData.countTickets
      return countTickets
    },

    maxDebitInvoiceId() {
      let InvoiceId = null

      if (!this.loggedInCustomer.statsData) {
        return InvoiceId
      }
      if (!this.loggedInCustomer.statsData.invoiceWithMaxDebit) {
        return InvoiceId
      }

      InvoiceId = this.loggedInCustomer.statsData.invoiceWithMaxDebit.id
      return InvoiceId
    },

    getAddcredit() {
      if (!this.loggedInCustomer) {
        return 'NO'
      }

      if (!this.loggedInCustomer.customer) {
        return 'NO'
      }

      if (!this.loggedInCustomer.customer.formattedAddCredit) {
        return 'NO'
      }

      return this.loggedInCustomer.customer.formattedAddCredit
    },

    getMakePayment() {
      if (!this.loggedInCustomer) {
        return 'NO'
      }

      if (!this.loggedInCustomer.customer) {
        return 'NO'
      }

      if (!this.loggedInCustomer.customer.formattedMakePayment) {
        return 'NO'
      }

      return this.loggedInCustomer.customer.formattedMakePayment
    },
  },
  methods: {
    updateMenuVisibility() {
      if (!this.settingsCompany) return;

      this.showPayments = this.settingsCompany.enable_payment_customer === "1";
      this.showQuotes = this.settingsCompany.enable_quotes_customer === "1";
      this.showInvoices = this.settingsCompany.enable_invoice_customer === "1";
      this.showTickets = this.settingsCompany.enable_tickets_customer === "1";
      this.showServices = this.settingsCompany.enable_service_customer === "1";
    },
  }
}
</script>
