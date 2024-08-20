<template>
  <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
    <!-- Amount Due -->
    <router-link slot="item-title" class="
        relative
        flex
        justify-between
        p-2
        bg-white
        rounded
        shadow
        hover:bg-gray-100
        lg:col-span-3
        xl:p-4
      " to="/admin/invoices">
      <div>
        <span v-if="getDashboardDataLoaded" class="text-sm font-semibold leading-tight text-black xl:text-2xl">
          <span v-html="$utils.formatMoney(getTotalDueAmount, defaultCurrency)" />
        </span>
        <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
          {{ $t('dashboard.cards.due_amount') }}
        </span>
      </div>
      <div class="flex items-center">
        <dollar-icon class="w-9 h-9 xl:w-12 xl:h-12" />
      </div>
    </router-link>

    <!-- Customers -->
    <router-link slot="item-title" class="
        relative
        flex
        justify-between
        p-2
        bg-white
        rounded
        shadow
        hover:bg-gray-100
        lg:col-span-2
        xl:p-4
      " to="/admin/customers">
      <div>
        <span v-if="getDashboardDataLoaded" class="text-sm font-semibold leading-tight text-black xl:text-2xl">
          {{ getContacts }}
        </span>
        <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
          {{ $t('dashboard.cards.customers') }}
        </span>
      </div>
      <div class="flex items-center">
        <contact-icon class="w-9 h-9 xl:w-12 xl:h-12" />
      </div>
    </router-link>

    <!-- Invoices -->
    <router-link slot="item-title" class="
        relative
        flex
        justify-between
        p-2
        bg-white
        rounded
        shadow
        hover:bg-gray-100
        lg:col-span-2
        xl:p-4
      " to="">
      <sw-dropdown style="width:100%">
        <div slot="activator" class="relative flex justify-between lg:col-span-2" style="width:100%">
          <div>
            <span v-if="getDashboardDataLoaded" class="text-sm font-semibold leading-tight text-black xl:text-2xl">
              {{ getInvoices }}

            </span>
            <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
              {{ $t('dashboard.cards.invoices') }}
            </span>
          </div>
          <div class="flex items-center">
            <invoice-icon class="w-9 h-9 xl:w-12 xl:h-12" />
          </div>
        </div>

        <sw-dropdown-item :to="`/admin/invoices`" tag-name="router-link">
          <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
          Go to invoices
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=DRAFT`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Draft:</b>&nbsp; {{ getinvoicesCountDraft }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=DUE`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Sent:</b>&nbsp; {{ getinvoicesCountSend }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=DUE`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Unpaid:</b>&nbsp; {{ getinvoicesunpaid }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=DUE`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Partially Paid:</b>&nbsp; {{ getinvoicesCountppaid }}
        </sw-dropdown-item>



        <sw-dropdown-item :to="`/admin/invoices?status=DUE`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Viewed:</b>&nbsp; {{ getinvoicesCountView }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=COMPLETED`" tag-name="router-link">
          <check-circle-icon class="
        h-5 mr-3 text-gray-600" />
          <b>
            Completed:</b>&nbsp; {{ getinvoicesCountCompleted }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=COMPLETED`" tag-name="router-link">

          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Paid:</b>&nbsp; {{ getInvoicespaid }}
        </sw-dropdown-item>


        <sw-dropdown-item :to="`/admin/invoices?status=OVERDUE`" tag-name="router-link">
          <x-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Overdue:</b> &nbsp; {{ getinvoicesCountOverdue }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/invoices?status=ARCHIVED`" tag-name="router-link">
          <x-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Archived:</b> &nbsp; {{ getinvoicesCountDeleted }}
        </sw-dropdown-item>

      </sw-dropdown>
    </router-link>

    <router-link slot="item-title" class="
        relative
        flex
        justify-between
        p-2
        bg-white
        rounded
        shadow
        hover:bg-gray-100
        lg:col-span-2
        xl:p-4
      " to="">
      <sw-dropdown style="width:100%">
        <div slot="activator" class="relative flex justify-between lg:col-span-2" style="width:100%">
          <div>
            <span v-if="getDashboardDataLoaded" class="text-sm font-semibold leading-tight text-black xl:text-2xl">
              {{ getEstimates }}

            </span>
            <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
              {{ $t('dashboard.cards.estimates') }}
            </span>
          </div>
          <div class="flex items-center">
            <invoice-icon class="w-9 h-9 xl:w-12 xl:h-12" />
          </div>
        </div>

        <sw-dropdown-item :to="`/admin/estimates`" tag-name="router-link">
          <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
          Go to estimates
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/estimates?status=DRAFT`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Draft:</b>&nbsp; {{ getEstimatesDraft }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/estimates?status=SENT`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Sent:</b>&nbsp; {{ getEstimatesSent }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/estimates?status=VIEWED`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Viewed:</b>&nbsp; {{ getEstimatesViewed }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/estimates?status=EXPIRED`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Expired:</b>&nbsp; {{ getEstimatesExpired }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/estimates?status=ACCEPTED`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Accepted:</b>&nbsp; {{ getEstimatesAccepted }}
        </sw-dropdown-item>

        <sw-dropdown-item :to="`/admin/estimates?status=REJECTED`" tag-name="router-link">
          <check-circle-icon class="h-5 mr-3 text-gray-600" />
          <b>
            Rejected:</b>&nbsp; {{ getEstimatesRejected }}
        </sw-dropdown-item>

      </sw-dropdown>
    </router-link>

  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import DollarIcon from '../../components/icon/DollarIcon'
import ContactIcon from '../../components/icon/ContactIcon'
import InvoiceIcon from '../../components/icon/InvoiceIcon'
import EstimateIcon from '../../components/icon/EstimateIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  XCircleIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  PencilIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    XCircleIcon,
  },
  data() {
    return {
      ...this.$store.state.dashboard,
      showPayments: false,
      showQuotes: false,
      showInvoices: false,
      showReports: false,
      showServices: false,
      showPbx: false,
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
    ...mapGetters('user', ['currentUser', 'settingsCompany']),
    ...mapGetters('dashboard', [
      'getContacts',
      'getInvoices',
      'getInvoicespaid',
      'getinvoicesunpaid',
      'getinvoicesCountppaid',
      'getinvoicesCountDeleted',
      'getinvoicesCountSend',
      'getinvoicesCountView',
      'getinvoicesCountOverdue',
      'getinvoicesCountCompleted',
      'getinvoicesCountDraft',
      'getEstimates',
      'getEstimatesCount',
      'getEstimatesDraft',
      'getEstimatesSent',
      'getEstimatesViewed',
      'getEstimatesExpired',
      'getEstimatesAccepted',
      'getEstimatesRejected',
      'getTotalDueAmount',
      'getDashboardDataLoaded',
    ]),
    ...mapGetters('company', ['defaultCurrency']),
  },
  methods: {
    updateMenuVisibility() {
      if (!this.settingsCompany) return;

      this.showPayments = this.settingsCompany.enable_payment_customer === "1";
      this.showQuotes = this.settingsCompany.enable_quotes_customer === "1";
      this.showInvoices = this.settingsCompany.enable_invoice_customer === "1";
      this.showReports = this.settingsCompany.enable_report_customer === "1";
      this.showServices = this.settingsCompany.enable_service_customer === "1";
      this.showPBX = this.settingsCompany.enable_pbxservice_customer === "1";
    },

  }
}
</script>
