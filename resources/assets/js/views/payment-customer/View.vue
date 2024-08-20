<template>
  <base-page v-if="payment" class="xl:pl-96">

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <sw-page-header :title="pageTitle"></sw-page-header>
    <div class="flex flex-wrap items-center justify-end">
       
        <sw-dropdown class="w-full md:w-auto md:ml-4 mb-2 md:mb-0">
          <sw-button slot="activator" variant="primary" class="h-10 w-full md:w-auto md:ml-4 mb-2 md:mb-0">
            <dots-horizontal-icon class="h-5" />
          </sw-button>

          <sw-dropdown-item @click="copyPdfUrl">
            <link-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.copy_pdf_url') }}
          </sw-dropdown-item>

          <hr />
          <label for="" v-if="this.invoice_id != null">Invoices</label>

          <sw-dropdown-item
            v-if="this.invoice_id != null"
            :to="`/customer/invoice/${this.invoice_id.id}/view`"
            tag-name="router-link"
          >
            <credit-card-icon class="h-5 mr-3 text-primary-800" />
            Go to: {{ this.invoice_id.invoice_number }}
          </sw-dropdown-item>
        </sw-dropdown>
      </div>

    </div>
    

    <!-- sidebar -->
    <div
      class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-88 xl:block"
    >
      <div
        class="flex items-center justify-between px-4 pt-8 pb-2 border border-gray-200 border-solid height-full"
      >
        <sw-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          type="text"
          class="mb-6"
          variant="gray"
          @input="searchMethode"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>

        <div class="flex mb-6 ml-3" role="group" aria-label="First group">
          <sw-dropdown position="bottom-start">
            <sw-button slot="activator" size="md" variant="gray-light">
              <filter-icon class="h-5" />
            </sw-button>

            <div
              class="px-2 pb-2 mb-1 text-sm border-b border-gray-200 border-solid"
            >
              {{ $t('general.sort_by') }}
            </div>

            <sw-dropdown-item class="flex cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_invoice_number"
                  :label="$t('invoices.title')"
                  v-model="searchData.orderByField"
                  size="sm"
                  name="filter"
                  value="invoice_number"
                  @change="loadPayments"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_payment_date"
                  :label="$t('payments.date')"
                  v-model="searchData.orderByField"
                  size="sm"
                  name="filter"
                  value="payment_date"
                  @change="loadPayments"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_payment_number"
                  :label="$t('payments.payment_number')"
                  v-model="searchData.orderByField"
                  size="sm"
                  name="filter"
                  value="payment_number"
                  @change="loadPayments"
                />
              </sw-input-group>
            </sw-dropdown-item>
          </sw-dropdown>

          <sw-button
            v-tooltip.top-center="{ content: getOrderName }"
            class="ml-1"
            size="md"
            variant="gray-light"
            @click="sortData"
          >
            <sort-ascending-icon v-if="getOrderBy" class="h-5" />
            <sort-descending-icon v-else class="h-5" />
          </sw-button>
        </div>
      </div>

      <base-loader v-if="isSearching" :show-bg-overlay="false" />

      <div
        class="h-full pb-32 overflow-y-scroll border-l border-gray-200 border-solid sw-scroll"
      >
        <router-link
          v-for="(payment, index) in payments"
          :to="`/customer/payments/${payment.id}/view`"
          :id="'payment-' + payment.id"
          :key="index"
          :class="[
            'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100 border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid':
                hasActiveUrl(payment.id),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
            {{ formatName(payment.user.name) }}
            </div>

            <div
              class="mb-1 text-xs not-italic font-medium leading-5 text-gray-500 capitalize"
            >
              {{ payment.payment_number }}
            </div>

            <div
              class="mb-1 text-xs not-italic font-medium leading-5 text-gray-500 capitalize"
            >
              {{ payment.invoice_number }}
            </div>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="$utils.formatMoney(payment.amount, payment.user.currency)"
            />

            <div class="text-sm text-right text-gray-500 non-italic">
              {{ payment.formattedPaymentDate }}
            </div>
          </div>
        </router-link>

        <pagination
          v-if="pagination.totalPages > 1"
          :pagination="pagination"
          @pageChange="onPageChanged"
          class="mb-4 w-full flex justify-center"
        />
        <p
          v-if="!payments.length"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('payments.no_matching_payments') }}
        </p>
      </div>
    </div>

    <!-- pdf -->
    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden relative"
      style="height: 75vh"
    >
      <base-loader
        class="flex-1 absolute"
        v-if="loadingPdf"
        :show-bg-overlay="true"
      />
      <iframe
        :src="`${shareableLink}`"
        class="flex-1 border border-gray-400 border-solid rounded-md"
      />
    </div>
  </base-page>
</template>
<script>
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
import { mapActions, mapGetters } from 'vuex'
const _ = require('lodash')
export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    TrashIcon,
    PencilIcon,
    LinkIcon,
    UsersIcon,
    CreditCardIcon,
  },
  data() {
    return {
      id: null,
      count: null,
      payments: [],
      payment: null,
      currency: null,
      invoice_id: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      isRequestOnGoing: false,
      isSearching: false,
      isSendingEmail: false,
      isMarkingAsSent: false,
      pagination: {
        totalPages: 1,
        currentPage: 1,
        count: 1,
      },
      loadingPdf: false,
    }
  },
  computed: {
    pageTitle() {
      return this.payment.payment_number
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
      return `/payments/pdf/${this.payment.unique_hash}`
    },
  },

  watch: {
    $route(to, from) {
      this.loadPayment()
    },
  },

  created() {
    this.loadPayments()
    this.loadPayment()
    this.searchMethode = _.debounce(this.searchMethode, 800)
  },

  methods: {
    ...mapActions('paymentCust', [
      'fetchPaymentsCustomers',
      'fetchPaymentCustomers',
      /* 'sendEmail', */
      /* 'deletePayment', */
      'searchPayment',
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
    onPageChanged(page) {
      this.loadPayments(page)
    },

    async loadPayments(page) {
      try {
        this.isSearching = true
        const data = {
          page,
          search: this.searchData.searchText || '',
          orderBy: this.searchData.orderBy || '',
          orderByField: this.searchData.orderByField || '',
        }
        let response = await this.fetchPaymentsCustomers(data)
        if (response.data) {
          this.payments = response.data.payments.data
        }
        setTimeout(() => {
          this.scrollToPayment()
        }, 500)
        this.pagination = {
          totalPages: response.data.payments.last_page,
          currentPage: response.data.payments.current_page,
          count: response.data.payments.count,
        }
      } catch (error) {
        //console.log(error)
      } finally {
        this.isSearching = false
      }
    },
    scrollToPayment() {
      const el = document.getElementById(`payment-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadPayment() {
      try {
        this.loadingPdf = true
        let response = await this.fetchPaymentCustomers(this.$route.params.id)

        if (response.data) {
          this.payment = response.data.payment
          this.invoice_id = response.data.invoice_id
        }
      } catch (error) {
       // console.log(error)
      } finally {
        this.loadingPdf = false
      }
    },
    searchMethode(value) {
      this.searchData.searchText = value
      this.loadPayments()
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.loadPayments()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.loadPayments()
      return true
    },
    /* async onPaymentSend() {
      this.openModal({
        title: this.$t('payments.send_payment'),
        componentName: 'SendPaymentModal',
        id: this.payment.id,
        data: this.payment,
        variant: 'lg',
      })
    }, */
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/payments/pdf/${this.payment.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$t('general.copied_pdf_url_clipboard'))
    },
    /* async removePayment(id) {
      this.id = id
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this payment!',
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deletePayment({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('payments.deleted_message', 1))
              this.$router.push('/admin/payments')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    }, */
  },
}
</script>
