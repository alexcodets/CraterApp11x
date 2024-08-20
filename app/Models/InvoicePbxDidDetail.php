<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePbxDidDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_pbx_did_detail';

    protected $fillable = [
        'invoice_id',
        'name',
        'quantity',
        'price',
        'total',
    ];
}
