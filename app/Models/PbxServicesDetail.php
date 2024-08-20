<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbxServicesDetail extends Model
{
    use HasFactory;

    protected $table = 'pbx_services_detail';

    protected $fillable = [
        'invoice_id',
        'count_extension',
        'count_did',
        'price_did',

    ];
}
