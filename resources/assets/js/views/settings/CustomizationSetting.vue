<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    
    <!-- Contenedor principal responsivo -->
    <div class="container-fluid p-0">
      <sw-card class="pl-2">
        <sw-tabs class="p-2 max-w-300">
          <div class="row no-gutters max-w-300">
            <!-- Invoices -->
            <div class="col-12 col-md-6 col-lg-4 mb-3 max-w-300">
              <sw-tab-item :title="$t('settings.customization.invoices.title')">
                <invoices-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>

            <!-- Estimates -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.estimates.title')">
                <estimates-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>

            <!-- Payments -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.payments.title')">
                <payments-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>

            <!-- Items -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.items.title')">
                <items-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>

            <!-- Packages -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.packages.title')">
                <packages-tab :settings="settings" :permission="permissionModule" /> 
              </sw-tab-item>
            </div>

            <!-- Customers -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.customer.title')">
                <preflix-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>

            <!-- Expenses -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.expenses.title')">
                <expenses-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>

            
            <!-- SMS -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
              <sw-tab-item :title="$t('settings.customization.sms_config')">
                <sms-tab :settings="settings" :permission="permissionModule" />
              </sw-tab-item>
            </div>
          </div>
        </sw-tabs>
      </sw-card>
    </div>
  </div>
</template>


<script>
import InvoicesTab from './customization-tabs/InvoicesTab.vue'
import EstimatesTab from './customization-tabs/EstimatesTab.vue'
import PaymentsTab from './customization-tabs/PaymentsTab.vue'
import ItemsTab from './customization-tabs/ItemsTab.vue'
import PackagesTab from './customization-tabs/PackagesTab.vue'
import PreflixTab from './customization-tabs/CustomizationTab.vue'
import ExpensesTab from './customization-tabs/ExpensesTab.vue'
import SmsTab from './customization-tabs/SmsTab.vue'
import { mapActions } from 'vuex'

export default {
  data() {
    return {
      settings: {},
      isRequestOnGoing: false,
      permissionModule:{
        create: false,
        read: false,
        update: false,
        delete: false,
      },

    }
  },

  components: {
    InvoicesTab,
    EstimatesTab,
    PaymentsTab,
    ItemsTab,
    PackagesTab,
    PreflixTab,
    ExpensesTab,
    SmsTab
  },

  created() {
    this.fetchSettings()
    this.permissionsUserModule()
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('user', ['getUserModules']),
    async fetchSettings() {
      this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
        'payment_auto_generate',
        'payment_prefix',
        'payment_subject',
        'payment_mail_body',
        'payment_mail_subject',
        'invoice_auto_generate',
        'invoice_prefix',
        'creation_services',
        'creation_services_subject',
        'suspension_services',
        'suspension_services_subject',
        'cancellation_services',
        'cancellation_services_subject',
        'reactivation_services',
        'reactivation_services_subject',
        'packages_prefix',
        'expense_prefix',
        'expense_auto_generate',
        'expense_template_prefix',
        'expense_template_auto_generate',
        'estimate_auto_generate',
        'estimate_prefix',
        'customer_prefix',
        'estimate_mail_body',
        'estimate_mail_subject',
        'invoice_billing_address_format',
        'invoice_shipping_address_format',
        'invoice_company_address_format',
        'invoice_mail_body',
        'invoice_mail_subject',
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
        'invoice_notice_one_subject', 
        'invoice_notice_two', 
        'invoice_notice_two_subject', 
        'invoice_notice_three', 
        'invoice_notice_three_subject', 
        'invoice_notice_unpaid',
        'invoice_notice_unpaid_subject',
        'customer_customer_creation',
        'customer_customer_creation_subject',
        'customer_account_registration',
        'customer_account_registration_subject',
        /* 'customer_password_creation', */
        'customer_password_reset',
        'customer_password_reset_subject',
        'customer_email_verification',
        'customer_email_verification_subject',
        'customer_forgetting_username',
        'customer_forgetting_username_subject',
        'customer_email_notification',
        'customer_email_notification_subject',
        'customer_lead_notification_subject',
        'customer_lead_notification_body',
        'payment_approved_ach',
        'payment_approved_ach_subject',
        'payment_ach_declined',
        'payment_ach_declined_subject',
        'payment_approved_credit_card',
        'payment_approved_credit_card_subject',
        'payment_credit_card_rejected',
        'payment_credit_card_rejected_subject',
        'payment_card_expiration_reminders',
        'payment_card_expiration_reminders_subject',
        'invoice_footer',
        'estimate_footer',
        'invoice_late_fee_retroactive',
        'enable_make_customer',
        'enable_credit_customer',
        'enable_invoice_customer',
        'enable_quotes_customer',
        'enable_payment_customer',
        'enable_report_customer',
        'enable_service_customer',
        'enable_tickets_customer',
        'enable_paymentaccount_customer',
        'enable_pbxservice_customer',
        'customer_type_selected',
        // late_fee_hour
        'late_fee_hour',
        // actives
        'invoice_late_fee_active_one',
        'invoice_late_fee_active_two',
        'invoice_late_fee_active_three',
        // days
        'invoice_late_fee_days_one',
        'invoice_late_fee_days_two',
        'invoice_late_fee_days_three',
        // value
        'invoice_late_fee_type_one_value',
        'invoice_late_fee_type_two_value',
        'invoice_late_fee_type_three_value',        
        // fixed o percentage
        'invoice_late_fee_type_one',
        'invoice_late_fee_type_two',
        'invoice_late_fee_type_three',
        // Email
        'invoice_bbc_email',
        'estimate_bbc_email',
        'payment_bbc_email',
        'package_bbc_email',
        'customer_bbc_email',
        // Expense Notifications
        'job_expense_pending_mail_bbc',
        'job_expense_pending_mail_subject',
        'job_expense_pending_mail_body',
        'job_expense_pending_enable',
        'job_expense_pending_time_run',
        'warning_before_due_date',
        'job_expense_template_time_run',
        'job_expense_template_enable',
        'pdf_format_pos',
        'invoice_issuance_period'  ,  
        //sms config
        'phoneFrom',
        'default_estimate_sms_body',
        'default_invoice_sms_body',
        'default_lead_sms_body'   
      ])

      this.settings = res.data
      this.isRequestOnGoing = false
    },

    async permissionsUserModule(){
      const data = {
         module: "customizations" 
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
         const modulePermissions = permissions.permissions[0]
          if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          }else if(modulePermissions.access == 0 || modulePermissions.read == 0){
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if(permissions.super_admin == true){
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      }else if(permissions.exist == true ){
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions.create == 1){
            this.permissionModule.create = true
        }
        if(modulePermissions.update == 1){
            this.permissionModule.update = true
        }
        if(modulePermissions.delete == 1){
            this.permissionModule.delete = true
        }
        if(modulePermissions.read == 1){
            this.permissionModule.read = true
        }
      }

    }
  },
}
</script>

<style>
  /* Aseguramos que las pestañas se ajusten correctamente en pantallas pequeñas */
  .sw-tabs {
    display: flex;
    flex-wrap: wrap;
  }

  .sw-tab-item {
    flex: 1 1 auto;
    min-width: 100%; /* Fuerza a que las pestañas ocupen el 100% del ancho en pantallas pequeñas */
  }

  @media (min-width: 576px) {
    .sw-tab-item {
      min-width: 50%; /* Ajuste para pantallas medianas */
    }
  }

  @media (min-width: 992px) {
    .sw-tab-item {
      min-width: 33.33%; /* Ajuste para pantallas grandes */
    }
  }

  /* Aseguramos que el contenedor principal no tenga padding innecesario */
  .container-fluid {
    padding: 0;
  }

  /* Estilos para dispositivos móviles (celulares)}*/
@media (max-width: 600px) {
    .max-w-300{
    max-width: 300px;
    }
    .base-tabs{
      overflow: auto;
    }

    .input-expand{
      max-width: 100% !important;
    }

    .margin-grid{
      grid-template-columns: repeat(2, minmax(0,1fr));
      display: grid;
      margin-left: 0%;
    }

    .ProseMirror{
      padding-bottom: 50px;
    }

    .display-grid{
      grid-template-columns: repeat(1, minmax(0, 1fr));
      display: grid;
      margin-left: 0%;
    }

    .margin-calendar{
      top: -12px !important;
      left: -21% !important;
    }

    .margin-tl{
      top: -16px !important;
      left: 40% !important;
    }

    .margin-tl-wh{
      top: 26% !important;
      width: 25% !important;
      left: 36% !important;
      max-height: 50%;
    }

    .margin-tl2{
      top:-36px !important;
      left: 50% !important;
    }

    .margin-buttonl{
      margin-left: 21%;
    }

    .margin-buttonlw{
      margin-left: 21%;
      width: 60%;
    }
  }
</style>