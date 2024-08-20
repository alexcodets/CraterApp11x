<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceCustomer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['status', 'present_balance', 'amount_op', 'amount_final', 'transaction_status', 'payment_id', 'user_id'];
}
