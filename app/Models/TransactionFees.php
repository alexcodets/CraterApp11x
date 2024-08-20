<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionFees extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];
}
