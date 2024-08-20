<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceAdditionalCharge extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'additional_charge_id',
        'company_id',
        'creator_id',
        'additional_charge_name',
        'additional_charge_amount',
        'additional_charge_type',
        'template_name',
        'qty',
        'total',
        'profile_extension_id',
        'profile_did_id',
    ];

    public function additionalCharge(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AdditionalCharge::class);

    }
}
