<template>
    <base-page>
        <sw-page-header :title="$t('customers.service_view')">
            <template slot="actions">
                <sw-button
                  v-if="cancel_service && !isCancelled"
                  class="mr-3"
                  variant="danger"
                  @click="cancelService"
                >
                  {{ $t('customer_profile.cancel_service') }}
                </sw-button>

                <sw-button
                    tag-name="router-link"
                    :to="`/customer/tickets/add`"
                    class="mr-3"
                >
                    {{ $t('customer_ticket.new_ticket') }}
                </sw-button>

                <sw-button
                    tag-name="router-link"
                    :to="`/customer/dashboard`"
                    class="mr-3"
                    variant="primary-outline"
                >
                    {{ $t('general.go_back') }}
                </sw-button>

            </template>
        </sw-page-header>

        <sw-card class="flex flex-col mt-6">
            <!-- Customer basic info -->
            <customer-info v-if="getInfoClient"/>

            <!-- Service details -->
            <confirm-package
                @status=""
            />

            <div class="tabs mb-5 grid col-span-12 pt-5">
            <div class="border-b tab">
              <div class="relative">
                <input
                  class="
                    w-full
                    absolute
                    z-10
                    cursor-pointer
                    opacity-0
                    h-5
                    top-4
                  "
                  type="checkbox"
                />
                <header
                  class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
                >
                  <span class="text-gray-500 uppercase sw-section-title">
                    INVOICES
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
                                :to="`/customer/invoice/${row.id}/view`"
                                class="font-medium text-primary-500"
                                >
                                {{ row.invoice_number }}
                            </router-link>
                    </template>
                </sw-table-column>

                    <sw-table-column
                      :sortable="true"
                      :label="'DATE'"
                      show="invoice_date"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="'STATUS'"
                      show="status"
                    />

                    <sw-table-column
                      :sortable="true"
                      :label="'PAID STATUS'"
                      show="paid_status"
                    />

                    <sw-table-column
                        :sortable="true"
                        :label="$t('invoices.total')"
                        sort-as="total"
                        >
                        <template slot-scope="row">
                            <span>Total</span>

                            <div
                            v-html="$utils.formatMoney(row.total)"
                            />
                        </template>
                    </sw-table-column>

                    <sw-table-column
                        :sortable="true"
                        :label="$t('invoices.amount_due')"
                        sort-as="total"
                        >
                        <template slot-scope="row">
                            <span>AMOUNT DUE</span>

                            <div
                            v-html="$utils.formatMoney(row.due_amount)"
                            />
                        </template>
                    </sw-table-column>
        
        </sw-table-component>

                  <!------------  TOTALS  ------------>
                  <div
                    class="
                      block
                      my-10
                      table-foot
                      lg:justify-between
                      lg:flex
                      lg:items-start
                    "
                  >
                    <div class="w-full lg:w-1/2"></div>

                    <div
                      class="
                        px-5
                        py-4
                        mt-6
                        bg-white
                        border border-gray-200 border-solid
                        rounded
                        table-total
                        lg:mt-0
                      "
                    >
                      <!------- COUNT ------->
                      <div class="flex items-center justify-between w-full">
                        <label
                          class="
                            text-sm
                            font-semibold
                            leading-5
                            text-gray-500
                            uppercase
                            mr-12
                          "
                        >
                          {{ $t('pbx_services.count') }}
                        </label>
                        <label
                          class="
                            flex
                            items-center
                            justify-center
                            m-0
                            text-lg text-black
                            uppercase   
                            ml-12                         
                          "
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
} from '@vue-hero-icons/solid';
import { TicketIcon } from "@vue-hero-icons/outline";
import { CalculatorIcon } from '@vue-hero-icons/outline';
import { RefreshIcon } from '@vue-hero-icons/solid';
import CustomerInfo from './partials/CustomerInfo';
import ConfirmPackage from "./partials/WizardConfirmPackage";
import {mapActions, mapGetters} from "vuex";

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
        CreditCardIcon
    },
    data() {
        return {
            cancel_service: false,
            status_cancelled: 'C',
            invoicesCount: 0,
        }
    },
    computed: {
        ...mapGetters('customer', ['selectedViewCustomer']),
        ...mapGetters('user', ['currentUser']),
        ...mapGetters('service', ['selectedService']),


        async getInfoClient() {
            if (this.currentUser) {
                this.fetchViewCustomer({ id: this.currentUser.id })
                return true
            }
            return false 
        },

        isCancelled() {
            return this.selectedService && this.selectedService.status === this.status_cancelled
        },
    },

    created() {
      this.loadData()
    },
   
    methods: {
        ...mapActions('customer', ['fetchViewCustomer']),
        ...mapActions('service', ['fetchViewService', 'updateService', 'fetchInvoicesPerService']),
        ...mapActions('customerProfile', ['getConfig']),

        async loadData() {
            let response = await this.getConfig(this.currentUser.id)

            if (response.data.config) {
                this.cancel_service = response.data.config.cancel_services === 1
            }
        },

        async cancelService() {
            swal({
                title: this.$t('general.are_you_sure'),
                text: 'you will not be able to recover this service!',
                icon: '/assets/icon/trash-solid.svg',
                buttons: true
            }).then(async (result) => {
                if (result) {
                    let data = {
                        id: this.$route.params.customer_package_id,
                        status: this.status_cancelled,
                        oneKey: true,
                        key: 'status'
                    }
                    let response = await this.updateService(data)

                    if (response.data.success) {
                        this.$router.go();
                        window.toastr['success'](this.$t('services.updated_message'))
                    }
                }
            })

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
                    count: response.data.totals.count
                },
            }

        },
    }
}
</script>

<style scoped>

</style>