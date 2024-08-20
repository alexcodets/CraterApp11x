<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAppRates extends Model
{
    use HasFactory;

    protected $table = 'invoice_app_rates';

    // fillable fields
    protected $fillable = [
        'app_name',
        'quantity',
        'costo',
        'pbx_package_id',
        'pbx_service_id',
        'invoice_id',
    ];
}
