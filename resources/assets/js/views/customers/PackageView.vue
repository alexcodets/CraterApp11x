<template>
  <base-page>
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-page-header :title="$t('customers.service_view')">
      <template slot="actions">
        <sw-button
         
          @click="gobackForm()"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/services`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('navigation.services') }}
        </sw-button>


        <sw-select
          v-model="status_selected"
          :options="getStatuses"
          :searchable="true"
          :show-labels="false"
          :placeholder="$t('customers.select_a_status')"
          class="mr-3"
          label="status_name"
          @select="(item) => changeStatus(item)"
        />

        <sw-dropdown>
          <sw-button slot="activator" variant="primary">
            <dots-horizontal-icon class="h-5 -ml-1 -mr-1" />
          </sw-button>

          <div v-if="!isHiddenStatus">
            <sw-dropdown-item
              :to="`/admin/customers/${$route.params.id}/service/${$route.params.customer_package_id}/edit`"
              tag-name="router-link"
            >
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item
              :to="{
                name: 'invoices.create',
                query: {
                  from: 'customer',
                  code: selectedService.code,
                  customer_packages_id: selectedService.id,
                  customer_id: selectedService.customer_id,
                  package_id: selectedService.package_id,
                },
              }"
              tag-name="router-link"
            >
              <calculator-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('invoices.new_invoice') }}
            </sw-dropdown-item>

            <sw-dropdown-item
              :to="`/admin/tickets/main/add-ticket`"
              tag-name="router-link"
            >
              <ticket-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('customer_ticket.new_ticket') }}
            </sw-dropdown-item>
          </div>

          <sw-dropdown-item
            :to="`/admin/customers/${$route.params.id}/view`"
            tag-name="router-link"
          >
            <users-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.go_to_customer') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            :to="`/admin/packages/${this.selectedService.package_id}/view`"
            tag-name="router-link"
          >
            <ticket-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.go_to_packages') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </template>
    </sw-page-header>

    <sw-card class="flex flex-col mt-6">
      <!-- Customer basic info -->
      <customer-info />

      <!-- Service details -->
      <confirm-package @status="setStatus" />

      <!-- AQUI -->

      <!------------------- INVOICES ------------------->
      <div class="tabs mb-5 grid col-span-12 pt-5">
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('general.invoices') }}
              </span>
              <div
                class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
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

            <div class="tab-content-slide">
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchInvoicesPerServiceData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.number')"
                  show="invoice_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.number') }}</span>
                    <router-link
                      :to="`/admin/invoices/${row.id}/view`"
                      class="font-medium text-primary-500"
                    >
                      {{ row.invoice_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.date')"
                  show="invoice_date"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('estimates.status')"
                  show="status"
                >
                  <template slot-scope="row">
                    <span> {{ $t('estimates.status') }}</span>
                    <sw-badge
                      :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                      :color="$utils.getBadgeStatusColor(row.status).color"
                      class="px-3 py-1"
                    >
                      {{ row.status }}
                    </sw-badge>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.paid_status')"
                  sort-as="paid_status"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.paid_status') }}</span>
                    <sw-badge
                      :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                      :color="$utils.getBadgeStatusColor(row.status).color"
                    >
                      {{ row.paid_status.replace('_', ' ') }}
                    </sw-badge>
                  </template>
                </sw-table-column>
                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.total')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>Total</span>

                    <div v-html="$utils.formatMoney(row.total)" />
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.amount_due')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.amount_due') }}</span>

                    <div v-html="$utils.formatMoney(row.due_amount)" />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="false" :filterable="false">
                  <template slot-scope="row">
                    <span>{{ $t('invoices.action') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />
                      <span v-if="IsArchivedActived != true">
                        <sw-dropdown-item
                          v-if="isSuperAdmin && row.noeditable == 0"
                          tag-name="router-link"
                          :to="`invoices/${row.id}/edit`"
                        >
                          <pencil-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('general.edit') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="isSuperAdmin"
                          tag-name="router-link"
                          :to="`/admin/invoices/${row.id}/view`"
                        >
                          <eye-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('invoices.view') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-else
                          tag-name="router-link"
                          :to="`/admin/invoices/${row.id}/view`"
                        >
                          <eye-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('invoices.view') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="row.status == 'DRAFT' && isSuperAdmin"
                          @click="sendInvoice(row)"
                        >
                          <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('invoices.send_invoice') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="
                            row.status === 'SENT' ||
                            (row.status === 'VIEWED' && isSuperAdmin)
                          "
                          @click="sendInvoice(row)"
                        >
                          <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('invoices.resend_invoice') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="row.status == 'DRAFT' && isSuperAdmin"
                          @click="markInvoiceAsSent(row.id)"
                        >
                          <check-circle-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('invoices.mark_as_sent') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="
                            row.status === 'SENT' ||
                            row.status === 'VIEWED' ||
                            (row.status === 'OVERDUE' && isSuperAdmin)
                          "
                          tag-name="router-link"
                          :to="`/admin/payments/${row.id}/create`"
                        >
                          <credit-card-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('payments.record_payment') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="!isSuperAdmin"
                          tag-name="router-link"
                          :to="`#`"
                        >
                          <credit-card-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('payments.record_payment') }}
                        </sw-dropdown-item>

                        <sw-dropdown-item
                          v-if="
                            isSuperAdmin &&
                            row.status != 'COMPLETED' &&
                            row.paid_status === 'UNPAID'
                          "
                          @click="removeInvoice(row.id)"
                        >
                          <trash-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('general.delete') }}
                        </sw-dropdown-item>
                      </span>
                      <span v-else>
                        <sw-dropdown-item @click="Restore(row)">
                          <save-icon class="h-5 mr-3 text-gray-600" />
                          {{ $t('general.restore') }}
                        </sw-dropdown-item>
                      </span>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>

              <!------------  TOTALS  ------------>
              <div
                class="block my-10 table-foot lg:justify-between lg:flex lg:items-start"
              >
                <div class="w-full lg:w-1/2"></div>

                <div
                  class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded table-total lg:mt-0"
                >
                  <!------- COUNT ------->
                  <div class="flex items-center justify-between w-full">
                    <label
                      class="text-sm font-semibold leading-5 text-gray-500 uppercase mr-12"
                    >
                      {{ $t('pbx_services.count') }}
                    </label>
                    <label
                      class="flex items-center justify-center m-0 text-lg text-black uppercase ml-12"
                    >
                      <span>{{ this.invoicesCount }}</span>
                    </label>
                  </div>

                  <!------- AMOUNT ------->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
  </base-page>
</template>

<script>
import {
  DotsHorizontalIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  DocumentDuplicateIcon,
  CreditCardIcon,
  UsersIcon,
} from '@vue-hero-icons/solid'
import { TicketIcon } from '@vue-hero-icons/outline'
import CustomerInfo from './partials/CustomerInfo'
import ConfirmPackage from './partials/WizardConfirmPackage'
import { mapActions, mapGetters } from 'vuex'
import { CalculatorIcon } from '@vue-hero-icons/outline'
import { RefreshIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    DotsHorizontalIcon,
    PencilIcon,
    TrashIcon,
    TicketIcon,
    CustomerInfo,
    ConfirmPackage,
    CalculatorIcon,
    RefreshIcon,
    EyeIcon,
    PaperAirplaneIcon,
    CheckCircleIcon,
    DocumentDuplicateIcon,
    CreditCardIcon,
    UsersIcon,
  },
  data() {
    return {
      isRequestOnGoing: false,
      isHiddenStatus: false,
      status_selected: null,
      status: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ],
      options: [
        { name: 'Active', value: 'A' },
        { name: 'Pending', value: 'P' },
        { name: 'Suspend', value: 'S' },
        { name: 'Cancelled', value: 'C' },
      ],
      invoicesCount: 0,
      invoicesTotal: 0,
      editextension: true,
      IsArchivedActived: false,
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),
    ...mapGetters('service', ['selectedService']),
    ...mapGetters('company', ['defaultCurrency']),
    ...mapGetters('user', ['currentUser']),

    getStatuses() {
      return this.status.map((status) => {
        return {
          ...status,
          status_name: 'Status: ' + status.name,
        }
      })
    },

    isSuperAdmin() {
      return this.currentUser.role == 'super admin' ? true : false
    },
  },
  created() {
    this.loadCustomer()
    // this.selectedViewCustomer
  },
  methods: {
    ...mapActions('invoice', ['markAsSent', 'deleteInvoice']),
    ...mapActions('customer', ['fetchViewCustomer']),
    ...mapActions('service', [
      'deleteService',
      'updateService',
      'fetchViewService',
      'invoicesServices',
      'fetchInvoicesPerService',
    ]),
    ...mapActions('modal', ['openModal']),

    async loadCustomer() {
      await this.fetchViewCustomer({ id: this.$route.params.id })

      await this.fetchViewService(this.$route.params.customer_package_id)

      if (this.status_selected.value == 'C') {
        this.isHiddenStatus = true
      }
    },

    async sendInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: invoice.id,
        data: invoice,
        variant: 'lg',
      })
    },

    async markInvoiceAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](
              this.$tc('invoices.mark_as_sent_successfully')
            )
          }
        }
      })
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchInvoicesPerServiceData({ page, filter, sort }) {
      let data = {
        customer_package_id: this.$route.params.customer_package_id,
        order_by: sort.fieldName || 'invoice_number',
        order: sort.order || 'desc',
        page,
        limit: 10,
      }

      let response = await this.fetchInvoicesPerService(data)

      this.invoicesCount = response.data.totals.count

      return {
        data: response.data.invoices.data,
        pagination: {
          totalPages: response.data.invoices.data.last_page,
          currentPage: page,
          count: response.data.totals.count,
        },
      }
    },

    async removeService(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this service!',
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteService({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$t('services.deleted_message'))
              this.$router.push(
                `/admin/customers/${this.$route.params.id}/view`
              )
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },

    gobackForm() {   this.$router.go(-1)},

    async removeInvoice(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
        this.resetSelectedInvoices()
      })
    },

    async setStatus(val) {
      this.status_selected = this.getStatuses.find(
        (_status) => val === _status.value
      )
    },

    async changeStatus(item) {
      let old_status = this.status_selected.value
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('general.change_status_confirm'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
      }).then(async (result) => {
        if (result) {
          this.isRequestOnGoing = true
          let data = {
            id: this.$route.params.customer_package_id,
            status: item.value,
            oneKey: true,
            key: 'status',
          }
          let response = await this.updateService(data)

          if (response.data.success) {
            this.isRequestOnGoing = false
            window.toastr['success'](this.$t('services.updated_message'))
          }
          if (this.status_selected.value == 'C') {
            this.isRequestOnGoing = false
            this.isHiddenStatus = true
          }
        } else {
          isRequestOnGoing = false
          await this.setStatus(old_status)
        }
      })
    },
  },
}
</script>

<style scoped>
</style>