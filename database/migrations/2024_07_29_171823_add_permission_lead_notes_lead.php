<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        //


        if (Schema::hasTable('rol_permisions')) {
            DB::statement("ALTER TABLE rol_permisions MODIFY COLUMN module ENUM('lead','lead_notes', 'customers', 'providers', 'estimates', 'invoices', 
            'payments', 'items', 'expenses', 'packages', 'corepbx', 'tickets', 'users', 'reports', 'settings', 
            'account_settings', 'company_info', 'preferences', 'customizations', 'notifications', 'tax_Groups', 
            'tax_types', 'payment_modes', 'custom_fields', 'notes', 'expense_categories', 'mail_configuration', 
            'file_disk', 'backup', 'logs', 'Modules', 'PBXware', 'Avalara', 'BillPay', 'roles', 'payment_gateways', 'Authorize', 
            'Paypal', 'services', 'pbx_services', 'services_normal', 'retentions', 'pbx_packages','pbx_extension','pbx_did','pbx_app_rate','pbx_custom_did','pbx_custom_destination',
            'pbx_customization','pbx_report','pbx_tenant', 'tickets_depa', 'tickets_email_temp', 'cust_address', 'cust_contacts', 'cust_payment_acc','cust_mnotes', 'corePOS_module','corePOS_index','corePOS_dashboard', 'open_close_cash_register', 'income_withdrawal_cash', 'assign_user_cash_register')");
        }

        if (Schema::hasTable('user_permisions')) {
            DB::statement("ALTER TABLE user_permisions MODIFY COLUMN module ENUM('lead','lead_notes', 'customers', 'providers', 'estimates', 'invoices', 'payments', 'items', 'expenses', 'packages', 'corepbx', 'tickets', 'users', 'reports', 'settings', 'account_settings', 'company_info', 'preferences', 'customizations', 'notifications', 'tax_Groups', 
            'tax_types', 'payment_modes', 'custom_fields', 'notes', 'expense_categories', 'mail_configuration', 
            'file_disk', 'backup', 'logs', 'Modules', 'PBXware', 'Avalara', 'BillPay', 'roles', 'payment_gateways', 'Authorize', 'Paypal', 'services', 'pbx_services', 'services_normal', 'retentions','pbx_packages','pbx_extension','pbx_did','pbx_app_rate','pbx_custom_did','pbx_custom_destination',
            'pbx_customization','pbx_report','pbx_tenant', 'tickets_depa', 'tickets_email_temp', 'cust_address', 'cust_contacts', 'cust_payment_acc','cust_mnotes', 'corePOS_module','corePOS_index','corePOS_dashboard' ,'open_close_cash_register', 'income_withdrawal_cash', 'assign_user_cash_register')");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
