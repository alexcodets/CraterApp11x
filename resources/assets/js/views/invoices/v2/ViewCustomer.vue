<template>
  <base-page v-if="invoice" class="xl:pl-96">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="pageTitle"></sw-page-header>
      <div class="flex flex-wrap items-center justify-end">
        <div class="mr-3 text-sm"></div>

        <sw-button
          v-if="invoice.status != 'COMPLETED' && invoice.status != 'SAVE_DRAFT'"
          :to="`/customer/payments/${$route.params.id}/create`"
          tag-name="router-link"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('payments.make_payment') }}
        </sw-button>
        <sw-dropdown class="w-full md:w-auto md:ml-4 mb-2 md:mb-0">
          <sw-button slot="activator" variant="primary" class="h-10 w-full md:w-auto md:ml-4 mb-2 md:mb-0">
            <dots-horizontal-icon class="h-5" />
          </sw-button>

          <sw-dropdown-item @click="copyPdfUrl">
            <link-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.copy_pdf_url') }}
          </sw-dropdown-item>

          <hr />

          <sw-dropdown-item
            v-for="(item, index) in this.payments"
            :key="index"
            :to="`/customer/payments/${item.id}/view`"
            tag-name="router-link"
          >
            <credit-card-icon class="h-5 mr-3 text-primary-800" />
            Go to: {{ item.payment_number }}
          </sw-dropdown-item>
        </sw-dropdown>
      </div>
    </div>

    <!-- sidebar -->
    <div
      class="fixed top-0 left-0 hidden h-full pt-16 pb-5 ml-56 bg-white xl:ml-64 w-88 xl:block"
    >
      <div
        class="flex items-center justify-between px-4 pt-8 pb-2 border border-gray-200 border-solid height-full"
      >
        <sw-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          class="mb-6"
          type="text"
          variant="gray"
          @input="onSearch"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>
      </div>

      <base-loader v-if="isSearching" :show-bg-overlay="true" />

      <div
        class="h-full pb-32 overflow-y-scroll border-l border-gray-200 border-solid sw-scroll"
      >
        <router-link
          v-for="(invoice, index) in invoices"
          :to="`/customer/invoice/${invoice.id}/view`"
          :id="'invoice-' + invoice.id"
          :key="index"
          :class="[
            'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100  border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid':
                hasActiveUrl(invoice.id),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
              {{ formatName(invoice.user.name) }}
            </div>

            <div
              class="mt-1 mb-2 text-xs not-italic font-medium leading-5 text-gray-600"
            >
              {{ invoice.invoice_number }}
            </div>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(invoice.status).bgColor"
              :color="$utils.getBadgeStatusColor(invoice.status).color"
              :font-size="$utils.getBadgeStatusColor(invoice.status).fontSize"
              class="px-1 text-xs"
            >
              {{ invoice.status }}
            </sw-badge>
          </div>

          <sw-table-column
            :sortable="true"
            :label="$t('invoices.total')"
            sort-as="total"
          >
            <template slot-scope="row">
              <span>{{ $t('invoices.total') }}</span>

              <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
            </template>
          </sw-table-column>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="
                $utils.formatMoney(invoice.due_amount, invoice.user.currency)
              "
            />
            <div
              class="text-sm not-italic font-normal leading-5 text-right text-gray-600"
            >
              {{ invoice.formattedInvoiceDate }}
            </div>
          </div>
        </router-link>

        <p
          v-if="!invoices.length"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('invoices.no_matching_invoices') }}
        </p>
      </div>
    </div>

    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden"
      style="height: 75vh"
    >
      <iframe
        :src="`${shareableLink}`"
        class="flex-1 border border-gray-400 border-solid rounded-md frame-style"
      />
    </div>
  </base-page>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import {
  DotsHorizontalIcon,
  FilterIcon,
  SortAscendingIcon,
  SortDescendingIcon,
  SearchIcon,
  LinkIcon,
  TrashIcon,
  PencilIcon,
  UsersIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'

const _ = require('lodash')
export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    LinkIcon,
    PencilIcon,
    TrashIcon,
    UsersIcon,
    CreditCardIcon,
  },
  data() {
    return {
      id: null,
      count: null,
      invoices: [],
      invoice: null,
      currency: null,
      payments: [],
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      isRequestOnGoing: false,
      isSearching: false,
      isSendingEmail: false,
      isMarkingAsSent: false,
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser && this.currentUser.role == 'super admin'
        ? true
        : false
    },
    pageTitle() {
      return this.invoice.invoice_number
    },
    getOrderBy() {
      if (
        this.searchData.orderBy === 'asc' ||
        this.searchData.orderBy == null
      ) {
        return true
      }
      return false
    },
    getOrderName() {
      if (this.getOrderBy) {
        return this.$t('general.ascending')
      }
      return this.$t('general.descending')
    },
    shareableLink() {
      return `/invoices-customer/pdf/${this.invoice.unique_hash}`
    },
    getCurrentInvoiceId() {
      if (this.invoice && this.invoice.id) {
        return this.invoice.id
      }
      return null
    },
    isArchived() {
      return this.invoice.deleted_at !== null
    },
  },
  watch: {
    $route(to, from) {
      this.loadInvoice()
    },
  },
  created() {
    this.loadInvoices()
    this.loadInvoice()
    this.onSearch = _.debounce(this.onSearch, 500)
  },
  methods: {
    ...mapActions('invoiceCustomer', [
      'fetchInvoicesCustomer',
      'getRecord',
      'searchInvoiceCustomer',
      'markAsSentCustomer',
      'sendEmailCustomer',
      'deleteInvoiceCustomer',
      'selectInvoiceCustomer',
      'fetchInvoiceCustomer',
    ]),

    ...mapActions('modal', ['openModal']),

    /**
     * Formatea el nombre para mostrar hasta 21 caracteres seguidos de puntos suspensivos si es más largo.
     * @param {string} name - El nombre a formatear.
     * @return {string} El nombre formateado.
     */
    formatName(name) {
      // Verifica si el nombre es más largo de 21 caracteres
      if (name.length > 20) {
        // Retorna los primeros 21 caracteres y concatena puntos suspensivos
        return name.substring(0, 20) + '...'
      }
      // Retorna el nombre completo si es igual o menor a 21 caracteres
      return name
    },

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadInvoices() {
      let response = await this.fetchInvoicesCustomer({ limit: 'all' })
      if (response.data) {
        this.invoices = response.data.invoices.data
      }
      setTimeout(() => {
        this.scrollToInvoice()
      }, 500)
    },
    scrollToInvoice() {
      const el = document.getElementById(`invoice-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadInvoice() {
      let response = await this.fetchInvoiceCustomer(this.$route.params.id)

      if (response.data) {
        this.invoice = response.data.invoice
        // console.log(this.invoice);
        if (response.data.payments) {
          this.payments = response.data.payments
          // console.log(this.payments)
        }
      }
    },
    async onSearch() {
      let data = ''
      if (
        this.searchData.searchText !== '' &&
        this.searchData.searchText !== null &&
        this.searchData.searchText !== undefined
      ) {
        data += `search=${this.searchData.searchText}&`
      }

      if (
        this.searchData.orderBy !== null &&
        this.searchData.orderBy !== undefined
      ) {
        data += `orderBy=${this.searchData.orderBy}&`
      }

      if (
        this.searchData.orderByField !== null &&
        this.searchData.orderByField !== undefined
      ) {
        data += `orderByField=${this.searchData.orderByField}`
      }
      this.isSearching = true
      let response = await this.searchInvoiceCustomer(data)
      this.isSearching = false
      if (response.data) {
        this.invoices = response.data.invoices.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearch()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearch()
      return true
    },
    async onMarkAsSent() {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.invoice_mark_as_sent'),
          icon: '/assets/icon/check-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            this.isMarkingAsSent = true
            let response = await this.markAsSentCustomer({
              id: this.invoice.id,
              status: 'SENT',
            })
            this.isMarkingAsSent = false
            if (response.data) {
              this.invoice.status = 'SENT'
              window.toastr['success'](
                this.$tc('invoices.marked_as_sent_message')
              )
            }
          }
        })
    },
    async onSendInvoice() {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: this.invoice.id,
        data: this.invoice,
      })
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/invoices/pdf/${this.invoice.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$t('general.copied_pdf_url_clipboard'))
    },
    async removeInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this invoice!',
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteInvoiceCustomer({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('invoices.deleted_message', 1))
              this.$router.push('/admin/invoices')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },
  },
}
</script>
