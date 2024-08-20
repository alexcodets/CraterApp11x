<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvalaraServiceType extends Model
{
    use HasFactory;

    protected $table = 'avalara_service_types';

    protected $fillable = ['service_type', 'avalara_transaction_types', 'service_type_name'];
}
