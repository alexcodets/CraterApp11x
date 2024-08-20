<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE user_permisions MODIFY COLUMN module ENUM('customers', 'providers', 'estimates', 'invoices', 
        'payments', 'items', 'expenses', 'packages', 'corepbx', 'tickets', 'users', 'reports', 'settings', 
        'account_settings', 'company_info', 'preferences', 'customizations', 'notifications', 'tax_Groups', 
        'tax_types', 'payment_modes', 'custom_fields', 'notes', 'expense_categories', 'mail_configuration', 
        'file_disk', 'backup', 'logs', 'Modules', 'PBXware', 'Avalara', 'BillPay', 'roles', 'payment_gateways', 'Authorize', 
        'Paypal', 'services', 'pbx_services', 'services_normal', 'retentions','pbx_packages','pbx_extension','pbx_did','pbx_app_rate','pbx_custom_did','pbx_custom_destination',
        'pbx_customization','pbx_report','pbx_tenant','cust_address', 'cust_contacts', 'cust_payment_acc','cust_mnotes', 'corePOS_module','corePOS_index','corePOS_dashboard')");

        DB::statement("ALTER TABLE rol_permisions MODIFY COLUMN module ENUM('customers', 'providers', 'estimates', 'invoices', 
            'payments', 'items', 'expenses', 'packages', 'corepbx', 'tickets', 'users', 'reports', 'settings', 
            'account_settings', 'company_info', 'preferences', 'customizations', 'notifications', 'tax_Groups', 
            'tax_types', 'payment_modes', 'custom_fields', 'notes', 'expense_categories', 'mail_configuration', 
            'file_disk', 'backup', 'logs', 'Modules', 'PBXware', 'Avalara', 'BillPay', 'roles', 'payment_gateways', 'Authorize', 
            'Paypal', 'services', 'pbx_services', 'services_normal', 'retentions', 'pbx_packages','pbx_extension','pbx_did','pbx_app_rate','pbx_custom_did','pbx_custom_destination',
            'pbx_customization','pbx_report','pbx_tenant','cust_address', 'cust_contacts', 'cust_payment_acc','cust_mnotes', 'corePOS_module','corePOS_index','corePOS_dashboard' )");
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
