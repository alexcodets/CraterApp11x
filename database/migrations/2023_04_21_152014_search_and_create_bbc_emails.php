<?php

use Crater\Models\CompanySetting;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $bbc_email_options = ["payment_bbc_email", "estimate_bbc_email", "pbx_service_bbc_email", "ticket_bbc_email", "invoice_bbc_email", "job_expense_pending_mail_bbc", "customer_bbc_email", "package_bbc_email"];

        $user_super_admin = User::where("role", "super admin")->first();

        if (is_null($user_super_admin)) {
            return;
        }

        foreach ($bbc_email_options as $option) {
            $company_setting = CompanySetting::where("option", $option)->first();

            //NO existe la option en company_setting
            if ($company_setting == null) {
                $new_option = new CompanySetting();
                $new_option->option = $option;
                $new_option->value = $user_super_admin->email;
                $new_option->company_id = $user_super_admin->company_id;
                $new_option->save();
            }

            //SI existe la option en company_setting PERO el value esta vacio -> ""
            if ($company_setting != null && $company_setting->value == "") {
                $company_setting->value = $user_super_admin->email;
                $company_setting->save();
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
        // down
    }
};
