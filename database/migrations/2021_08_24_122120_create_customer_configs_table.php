<?php

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
        Schema::create('customer_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('customer_id');
            $table->integer('invoice_days_before_renewal');
            $table->integer('auto_debit_days_before_due');
            $table->integer('suspend_services_days_after_due');
            $table->integer('auto_debit_attempts');
            $table->integer('cancel_service_changes_days');
            $table->integer('apply_invoice_late_fees');
            $table->boolean('enable_auto_debit');
            $table->boolean('set_invoice_method');
            $table->boolean('invoice_suspended_services');
            $table->boolean('invoice_service_together');
            $table->boolean('display_range_date');
            $table->boolean('cancel_services');
            $table->boolean('synchronize_addons');
            $table->boolean('client_create_addons');
            $table->boolean('client_change_service_term');
            $table->boolean('client_change_service_package');
            $table->boolean('client_prorate_credits');
            $table->boolean('auto_apply_credits');
            $table->boolean('auto_paid_pending_services');
            $table->boolean('void_invoice_canceled_service');
            $table->integer('void_invoice_canceled_service_days');
            $table->boolean('show_client_tax_id');
            $table->boolean('queue_service_changes');
            $table->boolean('send_cancellation_notice');
            $table->boolean('send_payment_notices');
            $table->integer('notice_1');
            $table->string('notice_1_type')->nullable();
            $table->integer('notice_2');
            $table->string('notice_2_type')->nullable();
            $table->integer('notice_3');
            $table->string('notice_3_type')->nullable();
            $table->integer('auto_debit_pending_notice');
            //$table->foreign('customer_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_configs');
    }
};
