<template>
  <sw-card class="flex flex-col mt-8">
    <div class="col-span-12">
      <!------------ PACKAGES ------------>
      <div
        class="tabs mb-5 grid col-span-12"
        style="border-top-color: #f9fbff"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services') }}
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
            <div class="tab-content">
              <div class="text-grey-darkest">
                <sw-tabs :active-tab="activeTab" @update="setStatusFilter">
                  <sw-tab-item :title="$t('customers.active')" filter="A" />
                  <sw-tab-item :title="$t('customers.pending')" filter="P" />
                  <sw-tab-item :title="$t('customers.suspend')" filter="S" />
                  <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
                </sw-tabs>
              </div>
              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchServicesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ capitalizeFirstLetter(row.term) }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="activation_date"
                  show="formattedActivationDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="formattedRenewalDate"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />
                      <sw-dropdown-item
                        :to="`/customer/service/${row.id}/view`"
                        tag-name="router-link"
                      >
                        <cog-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.manage') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

       <!------------ SERVICES PBX ------------>
      <div
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck4"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck4"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services_pbx') }}
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
            <div class="tab-content">
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeServicesPbxTab"
                  @update="setServicesPbxStatusFilter"
                >
                  <sw-tab-item :title="$t('customers.active')" filter="A" />
                  <sw-tab-item :title="$t('customers.pending')" filter="P" />
                  <sw-tab-item :title="$t('customers.suspend')" filter="S" />
                  <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="services_pbx_table"
                :show-filter="false"
                :data="fetchPbxServicesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                  show="pbx_services_number"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="pbx_package.pbx_package_name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ capitalizeFirstLetter(row.term) }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="activation_date"
                  show="formattedActivationDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="formattedRenewalDate"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />
                      <sw-dropdown-item
                        :to="`/customer/pbx-service/${row.id}/view`"
                        tag-name="router-link"
                      >
                        <cog-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.manage') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>


      <!------------ INVOICES ----------->
      <div
        class="tabs mb-5 grid col-span-12 pt-8"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck2"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck2"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('invoices.invoice', 2) }}
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
            <div class="tab-content">
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeInvoiceTab"
                  @update="setInvoiceStatusFilter"
                >
                  <sw-tab-item :title="$t('general.all')" filter="" />
                  <sw-tab-item :title="$t('general.due')" filter="DUE" />
                  <sw-tab-item :title="$t('general.overdue')" filter="OVERDUE" />
                  <sw-tab-item :title="$t('general.completed')" filter="COMPLETED" />
                  <sw-tab-item :title="$t('general.archived')" filter="ARCHIVED" />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="invoices_table"
                :show-filter="false"
                :data="fetchInvoicesData"
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
                    <router-link
                      :to="`#`"
                      class="font-medium text-primary-500"
                    >
                      {{ row.invoice_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('invoices.status')"
                  sort-as="status"
                >
                  <template slot-scope="row">
                    <span> {{ $t('invoices.status') }}</span>

                    <sw-badge
                      :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                      :color="$utils.getBadgeStatusColor(row.status).color"
                    >
                      {{ row.status.replace('_', ' ') }}
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
                  :label="$t('invoices.amount_due')"
                  sort-as="due_amount"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.amount_due') }}</span>

                    <div
                      v-html="
                        $utils.formatMoney(row.due_amount, row.user.currency)
                      "
                    />
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.action') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

      <!------------ ESTIMATES ----------->
      <div
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck3"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck3"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('estimates.estimate', 2) }}
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
            <div class="tab-content">
              <div class="text-grey-darkest">
                <sw-tabs
                  :active-tab="activeEstimateTab"
                  @update="setEstimateStatusFilter"
                >
                  <sw-tab-item :title="$t('general.all')" filter="" />
                  <sw-tab-item :title="$t('general.sent')" filter="SENT" />
                  <sw-tab-item
                    :title="$t('general.pending')"
                    filter="PENDING"
                  />
                </sw-tabs>
              </div>

              <sw-table-component
                ref="estimates_table"
                :show-filter="false"
                :data="fetchEstimatesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('estimates.date')"
                  sort-as="estimate_date"
                  show="formattedEstimateDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('estimates.estimate', 1)"
                  show="estimate_number"
                >
                  <template slot-scope="row">
                    <span>{{ $tc('estimates.estimate', 1) }}</span>
                    <router-link
                      :to="`#`"
                      class="font-medium text-primary-500"
                    >
                      {{ row.estimate_number }}
                    </router-link>
                  </template>
                </sw-table-column>

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
                  :label="$t('estimates.total')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span> {{ $t('estimates.total') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.action') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />
                    </sw-dropdown>
                  </template>
                </sw-table-column>

              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

      <!------------ EXPENSES 
      <div
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck5"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck5"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('expenses.expense', 2) }}
              </span>
              <div
                class="rounded-full border border-grey w-7 h-7 flex items-center justify-center test"
              >
       
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
            <div class="tab-content">
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
                      :to="`#`"
                      class="font-medium text-primary-500"
                    >
                      {{ row.expense_number }}
                    </router-link>
                  </template>
                </sw-table-column>

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
                  :label="$t('expenses.provider')"
                  sort-as="provider_title"
                  show="provider_title"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.provider') }}</span>
                    <span>
                      {{
                        row.provider_title ? row.provider_title : 'Not selected'
                      }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('expenses.note')"
                  sort-as="expense_date"
                >
                  <template slot-scope="row">
                    <span>{{ $t('expenses.note') }}</span>
                    <div class="notes">
                      <div class="truncate note">{{ row.notes }}</div>
                    </div>
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
                    <div
                      v-html="$utils.formatMoney(row.amount, row.user.currency)"
                    />
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
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>
----------->
      <!------------ PAYMENTS ----------->
      <div
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck6"
            />
            <header
              class="col-span-5 flex justify-between items-center py-3 cursor-pointer select-none tab-label"
              for="chck6"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $tc('payments.payment', 2) }}
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
            <div class="tab-content">
              <div class="text-grey-darkest">
                <div class="flex base-tabs"></div>
              </div>

              <sw-table-component
                ref="payments_table"
                :show-filter="false"
                :data="fetchPaymentsData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.date')"
                  sort-as="payment_date"
                  show="formattedPaymentDate"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.payment_number')"
                  show="payment_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.payment_number') }}</span>
                    <router-link
                      :to="`#`"
                      class="font-medium text-primary-500"
                    >
                      {{ row.payment_number }}
                    </router-link>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.payment_mode')"
                  show="payment_mode"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.payment_mode') }}</span>
                    <span>
                      {{ row.payment_mode ? row.payment_mode : 'Not selected' }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.invoice')"
                  sort-as="invoice_id"
                  show="invoice_number"
                >
                  <template slot-scope="row">
                    <span>{{ $t('invoices.invoice_number') }}</span>
                    <span>
                      {{
                        row.invoice_number ? row.invoice_number : 'No Invoice'
                      }}
                    </span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('payments.amount')"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.amount, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown"
                >
                  <template slot-scope="row">
                    <span>{{ $t('payments.action') }}</span>
                    <sw-dropdown>
                      <dot-icon slot="activator" />
                    </sw-dropdown>
                  </template>
                </sw-table-column>

              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

     
    </div>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CogIcon} from '@vue-hero-icons/solid'

export default {
  components: {
    CogIcon
  },
  data() {
    return {
      activeTab: this.$t('customers.active'),
      activeInvoiceTab: this.$t('general.all'),
      activeEstimateTab: this.$t('general.all'),
      activeServicesPbxTab: this.$t('customers.active'),
      status: { name: 'Active', value: 'A' },
      invoice_status: { name: '', value: '' },
      estimate_status: { name: '', value: '' },
      services_pbx_status: { name: 'Active', value: 'A' },
    }
  },
  methods: {
    ...mapActions('customerProfile', [
      'fetchServices',
      'fetchInvoices',
      'fetchEstimates',
      'fetchExpenses',
      'fetchPayments',
      'fetchPbxServices'
    ]),

    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    },

    async fetchServicesData({ page, filter, sort }) {
      let data = {
        status: this.status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchServices(data)

      let list = response.data.packagesList.data.map((pack) => {
        let discount_type = ''

        switch (pack.discount_type) {
          case 'N':
            discount_type = 'None'
            break
          case 'G':
            discount_type = 'General'
            break
          case 'I':
            discount_type = 'By item'
            break
        }

        return {
          ...pack,
          discount_type_name: discount_type,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.packagesList.last_page,
          currentPage: page,
          count: response.data.packagesList.count,
        },
      }
    },

    setStatusFilter(val) {
      if (this.activeTab === val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.status = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.status = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.status = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.status = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.$refs.table.refresh()
    },

    async fetchInvoicesData({ page, filter, sort }) {
      let data = {
        status: this.invoice_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchInvoices(data)

      let list = response.data.invoicesList.data.map((invoice) => {
        return {
          ...invoice,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.invoicesList.last_page,
          currentPage: page,
          count: response.data.invoicesList.count,
        },
      }
    },

    setInvoiceStatusFilter(val) {
      if (this.activeInvoiceTab === val.title) {
        return true
      }
      this.activeInvoiceTab = val.title
      switch (val.title) {
        case this.$t('general.due'):
          this.invoice_status = {
            name: 'DUE',
            value: 'DUE',
          }
          break

        case this.$t('general.draft'):
          this.invoice_status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break
        case this.$t('general.overdue'):
          this.invoice_status = {
            name: 'OVERDUE',
            value: 'OVERDUE',
          }
          break

        case this.$t('general.completed'):
          this.invoice_status = {
            name: 'COMPLETED',
            value: 'COMPLETED',
          }
          break
        case this.$t('general.archived'):
          this.invoice_status = {
            name: 'ARCHIVED',
            value: 'ARCHIVED',
          }
          break

        default:
          this.invoice_status = {
            name: '',
            value: '',
          }
          break
      }
      this.$refs.invoices_table.refresh()
    },

    async fetchEstimatesData({ page, filter, sort }) {
      let data = {
        status: this.estimate_status.value,
        from_date: '',
        to_date: '',
        estimate_number: '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }


      let response = await this.fetchEstimates(data)

      let list = response.data.estimates.data.map((estimate) => {
        return {
          ...estimate,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.estimates.last_page,
          currentPage: page,
          count: response.data.estimates.count,
        },
      }
    },

    setEstimateStatusFilter(val) {
      if (this.activeEstimateTab === val.title) {
        return true
      }
      this.activeEstimateTab = val.title
      switch (val.title) {
        case this.$t('general.draft'):
          this.estimate_status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break

        case this.$t('general.sent'):
          this.estimate_status = {
            name: 'SENT',
            value: 'SENT',
          }
          break

        case this.$t('general.pending'):
          this.estimate_status = {
            name: 'PENDING',
            value: 'PENDING',
          }
          break

        default:
          this.estimate_status = {
            name: '',
            value: '',
          }
          break
      }
      this.$refs.estimates_table.refresh()
    },

    async fetchExpensesData({ page, filter, sort }) {
      let data = {
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

    async fetchPaymentsData({ page, filter, sort }) {
      let data = {
        status: this.estimate_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchPayments(data)
      return {
        data: response.data.payments.data,
        pagination: {
          totalPages: response.data.payments.last_page,
          currentPage: page,
          count: response.data.payments.count,
        },
      }
    },

    async fetchPbxServicesData({ page, filter, sort }) {
      let data = {
        status: this.services_pbx_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchPbxServices(data)

      return {
        data: response.data.pbxServices.data,
        pagination: {
          totalPages: response.data.pbxServices.last_page,
          currentPage: page,
          count: response.data.pbxServices.count,
        },
      }
    },

    setServicesPbxStatusFilter(val) {
      if (this.activeServicesPbxTab === val.title) {
        return true
      }
      this.activeServicesPbxTab = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.services_pbx_status = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.services_pbx_status = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.services_pbx_status = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.services_pbx_status = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.$refs.services_pbx_table.refresh()
    },
  }
}
</script>

<style lang="scss">
// Dropdown
.tab {
  overflow: hidden;
}
.tab-content {
  max-height: 0;
  transition: all 0.5s;
}
input:checked + .tab-label .test {
  background-color: #000;
}
input:checked + .tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}
input:checked + .tab-label::after {
  transform: rotate(90deg);
}
input:checked ~ .tab-content {
  max-height: 400vh;
}
</style>