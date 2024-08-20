<template>
  <base-page>
    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('invoices.no_invoices')"
      :description="$t('invoices.list_of_invoices')"
    >
      <moon-walker-icon class="mt-5 mb-4" />
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative mt-5">
      <p class="absolute right-0 m-0 text-sm" style="top: 50px">
        {{ $t('general.showing') }}: <b>{{ archived.length }}</b>

        {{ $t('general.of') }} <b>{{ totalArchived }}</b>
      </p>
    </div>
    <div v-show="!showEmptyScreen" class="relative">
      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :label="$t('invoices.date')"
          sort-as="invoice_date"
          show="formattedInvoiceDate"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.number')"
          show="invoice_number"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.number') }}</span>
            {{ row.invoice_number }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.customer')"
          width="20%"
          show="name"
        />

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <sw-dropdown-item @click="Restore(row.id)">
              <save-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.restore') }}
            </sw-dropdown-item>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="closeArchivedModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
    </div>
  </base-page>
</template>




<script>
import { mapActions, mapGetters } from 'vuex'
import MoonWalkerIcon from '@/components/icon/MoonwalkerIcon'
import moment from 'moment'

import {
  PencilIcon,
  DocumentDuplicateIcon,
  CreditCardIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PlusIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  XCircleIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    MoonWalkerIcon,
    PlusIcon,
    XIcon,
    ChevronDownIcon,
    DotsHorizontalIcon,
    PencilIcon,
    DocumentDuplicateIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    CreditCardIcon,
    HashtagIcon,
  },

  data() {
    return {
      currency: null,
      isRequestOngoing: true,
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalArchived && !this.isRequestOngoing
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('invoice', ['totalArchived', 'archived', 'selectAllField']),
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),

    ...mapActions('invoice', [
      'fetchArchived',
      'getRecord',
      'sendEmail',
      'markAsSent',
      'cloneInvoice',
    ]),
    ...mapActions('customer', ['fetchCustomers']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      this.isRequestOngoing = true
      let response = await this.fetchArchived()
      
      this.isRequestOngoing = false
    //  this.currency = response.data.currency

      return {
        data: response.data.invoices.data,
        pagination: {
          totalPages: response.data.invoices.last_page,
          currentPage: page,
          count: response.data.invoices.invoiceTotalCount,
        },
      }
    },

    /* CONFIRMAR DID / INSERT PBX SERVICES*/
    async Restore(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.did.confirm'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (Confirm) => {
        if (Confirm) {
       
          window.toastr['success'](this.$tc('invoices.restore', 1))
          this.$refs.table.refresh()
          return true
          /*
          let res = await this.RestoreInvoice(id)
          if (res.data.success) {
            window.toastr['success'](
              this.$tc('corePbx.extensions.success_confirm', 1)
            )
            this.$refs.table.refresh()
            return true
          }
          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('packages.user_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }
          window.toastr['error'](res.data.message)
          return true */
        }
      })
    },
    closeArchivedModal() {
      this.resetModalData()
      this.closeModal()
    },
  },
}
</script>
 <!-- 
 1. QUITAR MODAL Y ADAPTAR EL this.data con los tipo Archived
 2. Terminar de retocar el update para quitar el delete_at de un registro
 3. terminar de retocar el updateArchived de los modules/actions.js
 4. Activar botón de Restore.
 -->