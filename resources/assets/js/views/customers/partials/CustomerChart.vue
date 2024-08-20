<template>
  <sw-card v-if="chartData" class="flex flex-col mt-6">
    <div class="grid grid-cols-12">
      <div class="col-span-12 xl:col-span-9 xxl:col-span-10">
        <div class="flex justify-between mt-1 mb-6">
          <h6 class="flex items-center sw-section-title">
            <chart-square-bar-icon class="h-5 text-primary-400" />{{
              $t('dashboard.monthly_chart.title')
            }}
          </h6>
          <div class="w-40 h-10">
            <sw-select
              v-model="selectedYear"
              :options="years"
              :allow-empty="false"
              :show-labels="false"
              :placeholder="$t('dashboard.select_year')"
              @select="onChangeYear"
            />
          </div>
        </div>
        <line-chart
          :format-money="$utils.formatMoney"
          :format-graph-money="$utils.formatGraphMoney"
          :invoices="getChartInvoices"
          :expenses="getChartExpenses"
          :receipts="getReceiptTotals"
          :income="getNetProfits"
          :labels="getChartMonths"
          :refresh="refresh"
          class="sm:w-full"
        />
      </div>
      <div
        class="grid grid-cols-2 col-span-10 text-center border-t border-l border-gray-200 border-solid lg:border-t-0 lg:text-right lg:col-span-3 xl:col-span-2 lg:grid-cols-1"
      >
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_sales') }}
          </span>
          <br />
          <span class="block mt-1 text-xl font-semibold leading-8">
            <div v-html="getFormattedSalesTotal" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_receipts') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #00c99c"
          >
            <div v-html="getFormattedTotalReceipts" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_expense') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #fb7178"
          >
            <div v-html="getFormattedTotalExpenses" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.net_income') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #5851d8"
          >
            <div v-html="getFormattedTotalNetProfit" />
          </span>
        </div>

        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.credit_add') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #900c3f"
          >
            <div v-html="getFormattedpaymentcredits" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_balance') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #900c3f"
          >
            <div v-html="getFormattedTotalBalance" />
          </span>
        </div>
        <div  class="px-6 py-2">
          <sw-dropdown style="width:100%; cursor: pointer;">
            <div slot="activator" class="grid grid-cols-2 gap-3">

              <div class="col-span-2 content-end">
                <plus-circle-icon class="h-5 text-gray-600" style="display: inline;"/>
                <span class=" text-xs leading-5 lg:text-sm">
                  {{ $t('dashboard.chart_info.callRegister') }}
                </span>

              </div>

              
              <span
                class="col-span-2 text-xl font-semibold leading-8"
                style="color: #900c3f"
              >
                <div v-html="getFormattedAccountBalance" />
              </span>

            </div>          

            <!-- <sw-dropdown-item
              :to="`/admin/invoices`"
              tag-name="router-link"
            >
              <b> Account Balance </b> &nbsp; <div v-html="getFormattedAccountBalance"></div>
            </sw-dropdown-item> -->

            <sw-dropdown-item
            :to="`/admin/invoices`"
              tag-name="router-link"
            >
              <b>  {{ $t('dashboard.invoice_total.invoice_total') }}</b>&nbsp; <div v-html="getFormattedTotalInvoices"></div>
            </sw-dropdown-item>

            <sw-dropdown-item
            :to="`/admin/invoices?status=DUE`"
              tag-name="router-link"
            >
              <b>  {{ $t('dashboard.invoice_total.invoice_due') }}</b>&nbsp; <div v-html="getFormattedDueInvoices"></div>
            </sw-dropdown-item>

            <sw-dropdown-item
            :to="`/admin/invoices?status=OVERDUE`"
              tag-name="router-link"
            >
              <!-- <check-circle-icon class="h-5 mr-3 text-gray-600" /> -->
              <b> {{ $t('dashboard.invoice_total.invoice_overdue') }}</b>&nbsp;  <div v-html="getFormattedOverdueInvoices"/>
            </sw-dropdown-item>

            <sw-dropdown-item
            :to="`/admin/invoices?paid_status=UNPAID`"
              tag-name="router-link"
            >
              <!-- <check-circle-icon class="h-5 mr-3 text-gray-600" /> -->
              <b>{{ $t('dashboard.invoice_total.invoice_unpaid') }}</b> &nbsp; <div v-html="getFormattedUnpaidInvoices"/>
            </sw-dropdown-item>

            <sw-dropdown-item
            :to="`/admin/invoices?status=DUE`"
              tag-name="router-link"
            >
              <!-- <check-circle-icon class="h-5 mr-3 text-gray-600" /> -->
              <b> {{ $t('dashboard.invoice_total.service_unpaid') }} </b>&nbsp;   <div v-html="getFormattedService"/>
            </sw-dropdown-item>

            <sw-dropdown-item
            :to="`/admin/invoices?status=COMPLETED`"
              tag-name="router-link"
            >
              <!-- <check-circle-icon class="h-5 mr-3 text-gray-600" /> -->
              <b> {{ $t('dashboard.invoice_total.service_unpaid_pbx') }}</b>&nbsp;  <div v-html="getFormattedpbxService"/>
            </sw-dropdown-item>

            <sw-dropdown-item
            :to="`/admin/invoices?status=COMPLETED`"
              tag-name="router-link"
            >

              <!-- <check-circle-icon class="h-5 mr-3 text-gray-600" /> -->
              <b>{{ $t('dashboard.invoice_total.cdr_balance') }}</b>&nbsp;  <div v-html="getFormattedcallRegister"/>
            </sw-dropdown-item>
        
          </sw-dropdown>
        </div>
      </div>
    </div>

    <!-- basic info -->
    <customer-info />
  </sw-card>
</template>

<script>
import CustomerInfo from './CustomerInfo'
import LineChart from '../../../components/chartjs/LineChart'
import { mapActions, mapGetters } from 'vuex'
import { ChartSquareBarIcon, PlusCircleIcon } from '@vue-hero-icons/outline'


export default {
  components: {
    LineChart,
    CustomerInfo,
    ChartSquareBarIcon,
    PlusCircleIcon
  },
  props: {
    refresh: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      id: null,
      customers: [],
      isLoaded: false,
      chartData: null,
      years: ['This year', 'Previous year'],
      selectedYear: 'This year',
      customerLoaded: {},
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency']),
    getChartInvoices() {
      if (this.chartData && this.chartData.invoiceTotals) {
        return this.chartData.invoiceTotals
      }
      return []
    },
    getChartExpenses() {
      if (this.chartData && this.chartData.expenseTotals) {
        return this.chartData.expenseTotals
      }
      return []
    },
    getReceiptTotals() {
      if (this.chartData && this.chartData.receiptTotals) {
        return this.chartData.receiptTotals
      }
      return []
    },
    getNetProfits() {
      if (this.chartData && this.chartData.netProfits) {
        return this.chartData.netProfits
      }
      return []
    },
    getChartMonths() {
      if (this.chartData && this.chartData.months) {
        return this.chartData.months
      }
      return []
    },
    getFormattedSalesTotal() {
      if (this.chartData && this.chartData.salesTotal) {
        return this.$utils.formatMoney(
          this.chartData.salesTotal,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalReceipts() {
      if (this.chartData && this.chartData.totalReceipts) {
        return this.$utils.formatMoney(
          this.chartData.totalReceipts,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalExpenses() {
      if (this.chartData && this.chartData.totalExpenses) {
        return this.$utils.formatMoney(
          this.chartData.totalExpenses,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalNetProfit() {
      if (this.chartData && this.chartData.netProfit) {
        return this.$utils.formatMoney(
          this.chartData.netProfit,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalBalance() {
      if (this.chartData && this.chartData.balanceTotal) {
        return this.$utils.formatMoney(
          this.chartData.balanceTotal * 100,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalInvoices() {
      if (this.chartData && this.chartData.invoiceTotals) {

        let sumInvoiceTotals = 0;
        
        this.chartData.invoiceTotals.forEach(element => {
          sumInvoiceTotals = sumInvoiceTotals + Number(element)
        });

        return this.$utils.formatMoney(
          sumInvoiceTotals,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedDueInvoices() {
      if (this.chartData && this.chartData.invoiceDue) {
        return this.$utils.formatMoney(
          this.chartData.invoiceDue,
          this.defaultCurrency
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedOverdueInvoices() {
      if (this.chartData && this.chartData.invoiceOverdue) {
        return this.$utils.formatMoney(
          this.chartData.invoiceOverdue,
          this.defaultCurrency
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedUnpaidInvoices() {
      if (this.chartData && this.chartData.invoiceUnpaid) {
        return this.$utils.formatMoney(
          this.chartData.invoiceUnpaid,
          this.defaultCurrency
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedAccountBalance() {
      if (this.chartData && this.chartData.balanceTotal) {
        
        return this.$utils.formatMoney (
          this.chartData.accountBalance,
          this.defaultCurrency,
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedcallRegister() {
      if (this.chartData && this.chartData.callRegisterTotalAmount) {
        return (
          this.defaultCurrency.symbol +
          ' ' +
          this.chartData.callRegisterTotalAmount
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedService() {
      if (this.chartData && this.chartData.servicetotal) {
        return this.$utils.formatMoney (
          this.chartData.servicetotal,
          this.defaultCurrency,
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedpbxService() {
      if (this.chartData && this.chartData.pbxservicetotal) {
        return this.$utils.formatMoney (
          this.chartData.pbxservicetotal,
          this.defaultCurrency,
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
    getFormattedpaymentcredits() {
      if (this.chartData && this.chartData.paymentsCredits) {
        return this.$utils.formatMoney (
          this.chartData.paymentsCredits,
          this.defaultCurrency,
        )
      }
      return this.defaultCurrency.symbol + '0.00'
    },
  },
  watch: {
    $route(to, from) {
      this.loadCustomer()
      this.selectedYear = 'This year'
    },
  },
  created() {
    this.loadCustomer()
  },
  methods: {
    ...mapActions('customer', ['fetchViewCustomer']),

    async loadCustomer() {
      this.isLoaded = false
      let response = await this.fetchViewCustomer({ id: this.$route.params.id })
      if (response.data) {
        this.chartData = response.data.chartData
        this.customerLoaded = response.data.customer
        console.log(this.chartData )
      }
      this.isLoaded = false
    },
    async onChangeYear(data) {
      if (data == 'Previous year') {
        let response = await this.fetchViewCustomer({
          id: this.$route.params.id,
          previous_year: true,
        })
        if (response.data) {
          this.chartData = response.data.chartData
        }
        return true
      }
      let response = await this.fetchViewCustomer({ id: this.$route.params.id })
      if (response.data) {
        this.chartData = response.data.chartData
      }
      return true
    },
  },
}
</script>