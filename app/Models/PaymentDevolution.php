<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDevolution extends Model
{
    public const STATUS_REFUNDED = 'Refunded';

    protected $table = 'payment_devolutions';

    protected $guarded = ['id'];
}
