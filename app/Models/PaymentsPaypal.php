<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentsPaypal extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'email_address',
        'amount',
        'currency',
        'country_code',
        'payment_status',
        'payment_id',
        'create_time',
        'creator_id',
        'company_id',
        'card_number',
        'card_type',
    ];

    public const STATUS_FAIL = 'FAIL';
    public const  STATUS_SUCCESS = 'SUCCESS';
}
