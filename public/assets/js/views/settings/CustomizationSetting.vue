<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card>
      <sw-tabs class="p-2">
        <!-- Invoices -->
        <sw-tab-item :title="$t('settings.customization.invoices.title')">
          <invoices-tab :settings="settings" />
        </sw-tab-item>

        <!-- Estimates -->
        <sw-tab-item :title="$t('settings.customization.estimates.title')">
          <estimates-tab :settings="settings" />
        </sw-tab-item>

        <!-- Payments -->
        <sw-tab-item :title="$t('settings.customization.payments.title')">
          <payments-tab :settings="settings" />
        </sw-tab-item>

        <!-- Items  Faltaba agregar settings-->
        <sw-tab-item :title="$t('settings.customization.items.title')">
          <items-tab :settings="settings" />
        </sw-tab-item>

         <!-- Packages -->
        <sw-tab-item :title="$t('settings.customization.packages.title')">
          <packages-tab :settings="settings"/> 
        </sw-tab-item>

        <!-- Customers -->
        <sw-tab-item :title="$t('settings.customization.customer.title')">
          <preflix-tab :settings="settings"/>
        </sw-tab-item>

        <!-- Expenses -->
        <sw-tab-item :title="$t('settings.customization.expenses.title')">
          <expenses-tab :settings="settings"/>
        </sw-tab-item>

      </sw-tabs>
    </sw-card>
  </div>
</template>

<script>
import InvoicesTab from './customization-tabs/InvoicesTab'
import EstimatesTab from './customization-tabs/EstimatesTab'
import PaymentsTab from './customization-tabs/PaymentsTab'
import ItemsTab from './customization-tabs/ItemsTab'
import PackagesTab from './customization-tabs/PackagesTab'
import PreflixTab from './customization-tabs/CustomizationTab'
import ExpensesTab from './customization-tabs/ExpensesTab'
import { mapActions } from 'vuex'

export default {
  data() {
    return {
      settings: {},
      isRequestOnGoing: false,
    }
  },

  components: {
    InvoicesTab,
    EstimatesTab,
    PaymentsTab,
    ItemsTab,
    PackagesTab,
    PreflixTab,
    ExpensesTab
  },

  created() {
    this.fetchSettings()
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    async fetchSettings() {
      this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
        'payment_auto_generate',
        'payment_prefix',
        'payment_mail_body',
        'invoice_auto_generate',
        'invoice_prefix',
        'creation_services',
        'suspension_services',
        'cancellation_services',
        'reactivation_services',
        'packages_prefix',
        'expense_prefix',
        'expense_auto_generate',
        'invoice_mail_body',
        'estimate_auto_generate',
        'estimate_prefix',
        'customer_prefix',
        'estimate_mail_body',
        'invoice_billing_address_format',
        'invoice_shipping_address_format',
        'invoice_company_address_format',
        'invoice_mail_body',
        'payment_mail_body',
        'payment_company_address_format',
        'payment_from_customer_address_format',
        'estimate_company_address_format',
        'estimate_billing_address_format',
        'estimate_shipping_address_format',
        'service_prefix',
        'allow_renewal_date_job',
        'time_run_renewal_date_job',
        'allow_autodebit_customer_job',
        'time_run_autodebit_customer_job',
        'time_run_reminder_payment_job',
        'allow_reminder_payment_job',
        'time_run_suspension_packages_job',
        'allow_suspension_packages_job',
        'period_run_unsuspend_packages_job',
        'allow_unsuspend_packages_job',
        'period_run_pending_payment_job',
        'allow_pending_payment_job',
        'time_run_cardexpiration_payment_job',
        'allow_cardexpiration_payment_job',
        'prov_prefix',
        'item_prefix',
        'allow_send_invoice_job',
        'period_time_run_send_invoice_job',
        'invoice_notice_one', 
        'invoice_notice_two', 
        'invoice_notice_three', 
        'invoice_notice_unpaid',
        'customer_customer_creation',
        'customer_account_registration',
        /* 'customer_password_creation', */
        'customer_password_reset',
        'customer_email_verification',
        'customer_forgetting_username',
        'customer_email_notification',
        'payment_approved_ach',
        'payment_ach_declined',
        'payment_approved_credit_card',
        'payment_credit_card_rejected',
        'payment_card_expiration_reminders',

      ])

      this.settings = res.data
      this.isRequestOnGoing = false
    },
  },
}
</script>
