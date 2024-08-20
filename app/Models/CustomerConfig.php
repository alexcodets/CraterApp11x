<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerConfig extends Model
{
    use HasFactory;
    use softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'creator_id',
        'customer_id',
        'invoice_days_before_renewal',
        'auto_debit_days_before_due',
        'suspend_services_days_after_due',
        'auto_debit_attempts',
        'cancel_service_changes_days',
        'apply_invoice_late_fees',
        'enable_auto_debit',
        'set_invoice_method',
        'invoice_suspended_services',
        'invoice_service_together',
        'display_range_date',
        'cancel_services',
        'synchronize_addons',
        'client_create_addons',
        'client_change_service_term',
        'client_change_service_package',
        'client_prorate_credits',
        'auto_apply_credits',
        'auto_paid_pending_services',
        'void_invoice_canceled_service',
        'void_invoice_canceled_service_days',
        'show_client_tax_id',
        'queue_service_changes',
        'send_cancellation_notice',
        'send_payment_notices',
        'notice_1',
        'notice_1_type',
        'notice_2',
        'notice_2_type',
        'notice_3',
        'notice_3_type',
        'auto_debit_pending_notice'
    ];
}
