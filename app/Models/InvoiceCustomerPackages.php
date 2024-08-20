<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCustomerPackages extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'package_id',
        'customer_id'
    ];
}
