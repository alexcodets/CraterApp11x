<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePbxExtensionDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_pbx_extension_detail';

    protected $fillable = [
        'invoice_id',
        'name',
        'quantity',
        'price',
        'total',
    ];
}
