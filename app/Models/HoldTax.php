<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoldTax extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'hold_invoice_id',
        'tax_type_id',
        'amount',
        'compound_tax',
        'percent',
        'name',
    ];

    public function holdInvoice(): BelongsTo
    {
        return $this->belongsTo(HoldInvoice::class);
    }
}
