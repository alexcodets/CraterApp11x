<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDid extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'pbx_did_id',
        'template_did_id',
        'company_id',
        'creator_id',
        'pbx_did_number',
        'pbx_did_server',
        'pbx_did_trunk',
        'pbx_did_type',
        'custom_did_id',
        'custom_did_rate',
        'template_did_name',
        'template_did_rate',
        'custom_did_id',
        'custom_did_rate',
        'name_prefix',
        'price'
    ];
}
