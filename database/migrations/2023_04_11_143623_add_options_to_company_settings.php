<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $options = ["warning_before_due_date", "core_pos_prefix", "expense_template_auto_generate", "expense_template_prefix", "job_expense_pending_time_run", "job_expense_pending_enable", "job_expense_pending_mail_body", "job_expense_pending_mail_subject", "job_expense_pending_mail_bbc", "job_expense_template_time_run", "job_expense_template_enable", "TTW_prefix", "invoice_notice_unpaid_subject", "invoice_notice_three_subject", "invoice_notice_two_subject", "invoice_notice_one_subject", "retention_platform_active", "reactivation_services_subject", "cancellation_services_subject", "suspension_services_subject", "creation_services_subject", "payment_credit_card_rejected_subject", "payment_approved_credit_card_subject", "ticket_bbc_email", "pbx_service_bbc_email", "estimate_bbc_email", "estimate_mail_subject", "current_year", "footer_url_name", "estimate_footer", "carbon_date_format_with_hour", "period_run_pending_payment_job", "allow_pending_payment_job", "reactivation_services", "cancellation_services", "suspension_services", "creation_services"];

        $values = ["3", "CPOS", "YES", "EXPT", "12:00", "1", "<p>Expense: {EXPENSE_NUMBER}</p>", "<p>Expense: {EXPENSE_NUMBER}</p>", "", "20:00", "0", "TTW", "<p>Auto debit 1 {INVOICE_NUMBER}</p>", "<p>Invoice notice 3 {INVOICE_NUMBER}</p>", "<p>Invoice notice 2 {INVOICE_NUMBER}</p>", "<p>Invoice notice 1 {INVOICE_NUMBER}</p><p></p>", "NO", "<p>Service Restored</p>", "<p>Service Cancellation Confirmation</p>", "<p>Service suspended</p>", "<p>New service activation </p>", "<p>Payment declined </p>", "<p>Payment received</p>", "", "", "", "<p>Estimate number {ESTIMATE_NUMBER}</p>", "1", "Company", "<p>This quote is subject to stock availability.</p>", "d M Y h:s", "1000", "1", "<p>Hi {COMPANY_NAME} ,</p><p>Your service,&nbsp;<strong>{SERVICES_NUMBER}</strong> has been reactivated from its suspension. Please contact us if you have any questions.</p>", "<p>Hi <strong>{COMPANY_NAME}</strong>,</p><p>Your service, {SERVICES_NUMBER}, has been canceled.</p>", "<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,</p><p>Your service, {SERVICES_NUMBER} has been suspended. The service may have been suspended for the following reasons:</p><ol><li><p>Non-payment. If your service was suspended for non-payment, you may login <strong>{CUSTOMER_LOGIN}</strong> at to post payment and re-activate the service.</p></li><li><p>TOS or abuse violation.</p></li></ol><p>If the service is suspended for an extended period of time, it may be canceled. Please contact us if you have any questions.</p>", "<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,</p><p>Your service number <strong>{SERVICES_NUMBER}</strong> has been approved and activated. Please keep this email for your records.</p><p>Thank you!</p><p></p>"];

        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        foreach ($companies as $company) {
            foreach ($options as $i => $option) {
                $company_setting = CompanySetting::where("option", $option)
                                                 ->where("company_id", $company->id)
                                                 ->first();

                if ($company_setting == null) {
                    $new_option = new CompanySetting();
                    $new_option->option = $option;
                    $new_option->value = $values[$i];
                    $new_option->company_id = $company->id;
                    $new_option->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            //
        });
    }
};
