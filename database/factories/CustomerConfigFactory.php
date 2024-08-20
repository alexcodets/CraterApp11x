<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'invoice_days_before_renewal' => 5,
            'auto_debit_days_before_due' => 5,
            'suspend_services_days_after_due' => 3,
            'auto_debit_attempts' => 3,
            'cancel_service_changes_days' => 5,
            'apply_invoice_late_fees' => true,
            'enable_auto_debit' => true,
            'set_invoice_method' => true,
            'invoice_suspended_services' => true,
            'invoice_service_together' => true,
            'display_range_date' => true,
            'cancel_services' => true,
            'synchronize_addons' => true,
            'client_create_addons' => true,
            'client_change_service_term' => true,
            'client_change_service_package' => true,
            'client_prorate_credits' => true,
            'auto_apply_credits' => true,
            'auto_paid_pending_services' => true,
            'void_invoice_canceled_service' => true,
            'void_invoice_canceled_service_days' => 5,
            'show_client_tax_id' => true,
            'queue_service_changes' => true,
            'send_cancellation_notice' => true,
            'send_payment_notices' => true,
            'notice_1' => true,
            'notice_1_type' => null,
            'notice_2' => true,
            'notice_2_type' => null,
            'notice_3' => true,
            'notice_3_type' => null,
            'auto_debit_pending_notice' => 11,
        ];
    }
}
